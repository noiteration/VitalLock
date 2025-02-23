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
                <h3>Add Patient Comment</h3>
            </div>
            <div class="page-content">
                <div class="container">
                    <div class="content-wrapper">
                        <div class="alert alert-success">Success. The patient comment has been added to the blockchain</div>
                    </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    </div>
</body>

</html>
