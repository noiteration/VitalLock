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
                        <div class="alert alert-success">Success. The person has completed a vision test and has a score of more than 30/50 which means they can skip the vision test at the exam center.</div>
                    </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    </div>
</body>

</html>
