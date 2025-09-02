<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtiquetaPdfController;
use App\Http\Controllers\LecturaController;
use App\Http\Controllers\LibroBibliotecaController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BibliotecaController;
use App\Http\Controllers\EjemplarController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\SolicitudUnionController;
use App\Http\Controllers\NotificacionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/account', [ProfileController::class, 'account'])->name('profile.account.settings');
    Route::patch('/profile/account/email', [ProfileController::class, 'updateEmail'])->middleware('throttle:6,1')->name('profile.account.email.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware('throttle:6,1')->name('profile.destroy');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    Route::put('/profile/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('password.update');

});

Route::middleware('auth')->group(function () {
    Route::get('/bibliotecas/search', [BibliotecaController::class, 'search'])->name('bibliotecas.search');
    Route::resource('bibliotecas', BibliotecaController::class)->except('search');
});

Route::middleware('auth')->group(function () {
    Route::get('/bibliotecas/{biblioteca}/configuracion', [BibliotecaController::class, 'editConfig'])->name('bibliotecas.editConfig');
    Route::patch('/bibliotecas/{biblioteca}/configuracion', [BibliotecaController::class, 'updateConfig'])->name('bibliotecas.updateConfig');
    Route::post(
        '/bibliotecas/{biblioteca}/etiquetas/regenerar',
        [BibliotecaController::class, 'regenerarEtiquetas']
    )->name('bibliotecas.regenerarEtiquetas');
});

Route::middleware('auth')->group(function () {
    Route::prefix('bibliotecas/{biblioteca}')->group(function () {
        Route::post('unirse', [MiembroController::class, 'store'])->name('miembro.store');
        Route::delete('abandonar', [MiembroController::class, 'leave'])->name('miembro.leave');
        Route::get('miembros', [MiembroController::class, 'index'])->name('miembro.index');
        Route::patch('miembros/{miembro}', [MiembroController::class, 'update'])->name('miembro.update');
        Route::get('miembros/{miembro}', [MiembroController::class, 'show'])->name('miembro.show');
        Route::delete('miembros/{miembro}', [MiembroController::class, 'destroy'])->name('miembro.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('bibliotecas/{biblioteca}')->group(function () {
        Route::post('solicitud-union', [SolicitudUnionController::class, 'store'])->name('solicitud-union.store');
        Route::get('solicitudes', [SolicitudUnionController::class, 'index'])->name('solicitud-union.index');
        Route::patch('solicitudes/{idSolicitudUnion}', [SolicitudUnionController::class, 'update'])->name('solicitud-union.update');
        Route::delete('solicitudes/{idSolicitudUnion}', [SolicitudUnionController::class, 'destroy'])->name('solicitud-union.destroy');
        Route::delete('solicitud-propia', [SolicitudUnionController::class, 'destroyPropia'])
            ->name('solicitud-union.destroyPropia');
        Route::get('solicitudes/{idSolicitudUnion}', [SolicitudUnionController::class, 'show'])->name('solicitud-union.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('bibliotecas/{biblioteca}')->group(function () {
        Route::get('ejemplares', [EjemplarController::class, 'create'])->name('ejemplares.create');
        Route::post('ejemplares', [EjemplarController::class, 'store'])->name('ejemplares.store');
        Route::post('libros/{libro}/ejemplares', [EjemplarController::class, 'storeCopia'])->name('ejemplares.storeCopia');
        Route::patch('ejemplares/{ejemplar}', [EjemplarController::class, 'update'])->name('ejemplares.update');
        Route::delete('ejemplares/{ejemplar}', [EjemplarController::class, 'destroy'])->name('ejemplares.destroy');
        Route::get('ejemplares/{ejemplar}', [EtiquetaPdfController::class, 'show'])->name('ejemplares.etiqueta.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('bibliotecas/{biblioteca}')->group(function () {
        Route::get('/search', [LibroController::class, 'searchEnBiblioteca'])->name('libros.searchEnBiblioteca');
        Route::get('libros/{idLibro}', [LibroController::class, 'showEnBiblioteca'])->name('libros.showEnBiblioteca');
        Route::delete('libros/{idLibro}', [LibroBibliotecaController::class, 'destroy'])->name('libros.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/lecturas', [LecturaController::class, 'index'])->name('lecturas.index');
    Route::prefix('lecturas')->group(function () {
        Route::get('/leyendo', [LecturaController::class, 'leyendo'])->name('lecturas.leyendo');
        Route::get('/leidos', [LecturaController::class, 'leidos'])->name('lecturas.leidos');
        Route::post('/{idLibro}/marcar-leyendo', [LecturaController::class, 'start'])->name('lecturas.marcar.leyendo');
        Route::post('/{idLibro}/marcar-leido', [LecturaController::class, 'finish'])->name('lecturas.marcar.leido');
        Route::post('/{idLibro}/marcar-abandonado', [LecturaController::class, 'abandon'])->name('lecturas.marcar.abandonado');

    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/notificaciones', [NotificacionController::class, 'index'])
        ->name('notificaciones.index');

    Route::get('/notificaciones/unread-count', [NotificacionController::class, 'unreadCount'])
        ->middleware('throttle:60,1')
        ->name('notificaciones.unread');

    Route::patch('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'marcarLeida'])
        ->middleware('throttle:60,1')
        ->name('notificaciones.leer');

    Route::patch('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])
        ->middleware('throttle:30,1')
        ->name('notificaciones.leerTodas');

    Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy'])
        ->middleware('throttle:60,1')
        ->name('notificaciones.destroy');

    Route::get('/notificaciones/{notificacion}/go', [NotificacionController::class, 'go'])
        ->name('notificaciones.go');
});

Route::get('/aviso', function () {
    return Inertia::render('Aviso');
})->middleware('auth')->name('aviso');

Route::get('/privacidad', function () {
    return Inertia::render('Privacidad');
})->middleware('auth')->name('privacidad');

Route::get('/cookies', function () {
    return Inertia::render('Cookies');
})->middleware('auth')->name('cookies');

require __DIR__ . '/auth.php';
