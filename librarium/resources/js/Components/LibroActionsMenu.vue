<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import { MoreVertical } from "lucide-vue-next";
import Swal from "sweetalert2";

const props = defineProps({
    idLibro: { type: Number, required: true },
    idBiblioteca: { type: Number, default: null },
    estadoLectura: { type: String, default: null },
    mostrarAbandonar: { type: Boolean, default: true },
    trigger: { type: String, default: "click" },
});

const open = ref(false);
const btnRef = ref(null);

// estilo calculado del panel (fixed sobre <body>)
const panelStyle = reactive({
    position: "fixed",
    top: "0px",
    left: "0px",
    zIndex: "60", // por encima de la tarjeta
});

function placePanel() {
    const btn = btnRef.value;
    if (!btn) return;
    const r = btn.getBoundingClientRect();
    // 8px de separación a la derecha
    panelStyle.top = `${r.top}px`;
    panelStyle.left = `${r.right + 8}px`;
}

async function openMenu() {
    open.value = true;
    await nextTick();
    placePanel();
}

function toggle(e) {
    e?.stopPropagation();
    open.value ? close() : openMenu();
}
function close() {
    open.value = false;
}

function onKey(e) {
    if (e.key === "Escape") close();
}
function onDocClick(e) {
    if (!open.value) return;
    // si haces click fuera del botón o del panel -> cerrar
    const btn = btnRef.value;
    const clickedInsideBtn = btn && btn.contains(e.target);
    const panel = document.getElementById(panelId);
    const clickedInsidePanel = panel && panel.contains(e.target);
    if (!clickedInsideBtn && !clickedInsidePanel) close();
}
function onScrollOrResize() {
    if (open.value) placePanel();
}

onMounted(() => {
    document.addEventListener("keydown", onKey);
    document.addEventListener("click", onDocClick, true);
    window.addEventListener("scroll", onScrollOrResize, true);
    window.addEventListener("resize", onScrollOrResize, true);
});
onBeforeUnmount(() => {
    document.removeEventListener("keydown", onKey);
    document.removeEventListener("click", onDocClick, true);
    window.removeEventListener("scroll", onScrollOrResize, true);
    window.removeEventListener("resize", onScrollOrResize, true);
});

// id estable para el panel (por accesibilidad)
const panelId = `libro-menu-${Math.random().toString(36).slice(2, 9)}`;

const post = (name, payload = {}, msgs = { ok: "Hecho", err: "Error" }) =>
  router.post(
    route(name, { idLibro: props.idLibro }),
    props.idBiblioteca ? { ...payload, idBiblioteca: props.idBiblioteca } : payload,
    {
      preserveScroll: true,
      onSuccess: () => {
        Swal.fire("OK", msgs.ok, "success");
        // 🔁 refresca solo la prop 'libros' de la página Biblioteca/Show
        router.reload({ only: ['libros'] });
        close();
      },
      onError: () => Swal.fire("Ups", msgs.err, "error"),
    }
  );

const volverALeer = async (e) => {
  e.stopPropagation();
  const r = await Swal.fire({
    title: "¿Volver a leer?",
    text: "Se reabrirá tu lectura y se actualizarán las fechas.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sí, continuar",
    cancelButtonText: "Cancelar",
    buttonsStyling: false,
    customClass: {
      confirmButton: "bg-brandblue text-white rounded-full px-4 py-2 text-sm font-semibold",
      cancelButton: "bg-gray-300 text-gray-800 rounded-full px-4 py-2 text-sm font-semibold",
      popup: "rounded-xl",
    },
  });
  if (!r.isConfirmed) return;
  post("lecturas.marcar.leyendo", {}, { ok: "Lectura iniciada" });
};

const comenzar = (e) => {
    e.stopPropagation();
    post("lecturas.marcar.leyendo", {}, { ok: "Lectura iniciada" });
};
const marcarLeido = (e) => {
    e.stopPropagation();
    post("lecturas.marcar.leido", {}, { ok: "Marcado como leído" });
};

const abandonar = (e) => {
    e.stopPropagation();
    post("lecturas.marcar.abandonado", {}, { ok: "Lectura abandonada" });
};

</script>

<template>
    <!-- Botón (se queda dentro de la tarjeta) -->
    <button
        ref="btnRef"
        type="button"
        class="absolute right-2 top-2 z-30 p-2 rounded-full bg-white/95 shadow hover:bg-white focus:outline-none focus:ring-2 focus:ring-brandgold"
        :aria-expanded="open"
        :aria-controls="panelId"
        aria-haspopup="menu"
        @click.stop="toggle"
        @mouseenter="trigger === 'hover' ? openMenu() : null"
        @mouseleave="trigger === 'hover' ? close() : null"
    >
        <MoreVertical class="w-4 h-4 text-slate-700" />
        <span class="sr-only">Abrir acciones</span>
    </button>

    <!-- Panel fuera del overflow-hidden -->
<!-- Panel fuera del overflow-hidden -->
<teleport to="body">
  <div
    v-show="open"
    :id="panelId"
    :style="panelStyle"
    class="w-52 rounded-xl border bg-white/95 backdrop-blur shadow-lg ring-1 ring-black/5 p-1"
    role="menu"
    tabindex="-1"
    @mouseenter="trigger === 'hover' ? openMenu() : null"
    @mouseleave="trigger === 'hover' ? close() : null"
    @click.stop
  >
    <!-- LEYENDO: mostrar completar + abandonar -->
    <template v-if="estadoLectura === 'leyendo'">
      <button @click="marcarLeido" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-slate-50" role="menuitem">
        ✅ Marcar como leído
      </button>
      <button v-if="mostrarAbandonar" @click="abandonar" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-slate-50 text-red-600" role="menuitem">
        ⛔ Abandonar
      </button>
    </template>

    <!-- COMPLETADO o ABANDONADO: mostrar volver a leer -->
    <template v-else-if="estadoLectura === 'completado' || estadoLectura === 'abandonado'">
      <button @click="volverALeer" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-slate-50" role="menuitem">
        🔁 Volver a leer
      </button>
    </template>

    <!-- SIN LECTURA: comenzar -->
    <template v-else>
      <button @click="comenzar" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-slate-50" role="menuitem">
        📖 Comenzar a leer
      </button>
    </template>
  </div>
</teleport>
</template>
