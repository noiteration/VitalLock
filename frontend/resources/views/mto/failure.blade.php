<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @vite(['resources/css/app.css', 'resources/css/bootstrap.css', 'resources/css/mazer.css'])
</head>

<body class="font-sans antialiased">
    <div id="app">
        @include('layouts.mynav')
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <h3>Vision Test Result</h3>
            </div>
            <div class="page-content">
                <div class="container">
                    <div class="content-wrapper">
                        <div class="alert alert-failure">Failure. The person has either low score in a vision test or has not given a vision test within the last 30 days. They CANNOT skip the vision test.</div>
                    </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    </div>
</body>

</html>
