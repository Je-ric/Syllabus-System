<meta charset="UTF-8">
<title>{{ $title ?? 'Syllabus System' }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Anton&family=Libre+Franklin:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
    rel="stylesheet">
<script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v1.x.x/dist/livewire-sortable.js"></script>
<script src="/lib/jquery/dist/jquery.min.js"></script>

@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])




