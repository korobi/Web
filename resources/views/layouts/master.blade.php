{{-- The master layout file. Right now this is a skeleton (not an *actual* skeleton, a basic structure). --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1"> {{-- We want to be responsive :) --}}
    @include("partials.page-title")
</head>
<body>
    <nav>
        @include("partials.navigation")
    </nav>
    <main>
        @yield("main")
    </main>
    <footer>
        @include("partials.footer")
    </footer>
</body>
</html>