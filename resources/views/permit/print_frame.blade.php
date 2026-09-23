<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    {{-- Ikon tab sama dengan portal TWP (layouts/app.blade.php) --}}
    <link rel="shortcut icon" href="{{ url('img/logoweb/logoweb.png') }}">
    <style>
        html, body { height: 100%; margin: 0; background: #525659; }
        iframe { display: block; width: 100%; height: 100%; border: 0; }
    </style>
</head>
<body>
    <iframe src="{{ $pdf_url }}" title="{{ $title }}"></iframe>
</body>
</html>
