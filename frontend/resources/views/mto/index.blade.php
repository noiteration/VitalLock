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
                <h3>Check Vision - MTO</h3>
            </div>
            <div class="page-content">
                <div class="container">
                    <div class="content-wrapper">

                        <form method="POST" action="{{ route('mto.show') }}">
                            @csrf
                            <div>
                                <label for="UserKey">User Key:</label>
                                <input type="text" id="UserKey" name="UserKey" value="{{ old('UserKey') }}">
                            </div>
                            <br />
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </form>

                    </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    </div>
</body>

</html>
