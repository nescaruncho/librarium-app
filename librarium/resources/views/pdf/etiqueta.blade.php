<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Etiqueta</title>
    <style>
        @page {
            margin: 6mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
        }

        .wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .codigo {
            font-size: 22pt;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .sec {
            font-size: 9pt;
            color: #333;
            line-height: 1.25;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="codigo">{{ $codigo }}</div>
        <div class="sec">Título: {{ \Illuminate\Support\Str::limit($titulo, 46) }}</div>
        <div class="sec">ISBN: {{ $isbn }}</div>
    </div>
</body>

</html>
