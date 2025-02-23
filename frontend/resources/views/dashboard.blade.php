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
                <h3>Patient Key Generation</h3>
            </div>
            <div class="page-content">
                <section class="row">
                    @if (isset($keysExist) && $keysExist)
                        <p>Your keys have already been generated. I hope you kept that file securely.
                        </p>
                    @else
                        <p>Your keys have been generated below. Please save them securely as they won't be shown again.</p>

                        <div class="key-container">
                            <label for="idKey">ID Key:</label>
                            <input type="text" id="idKey" value="{{ $idKey ?? '' }}" readonly>
                        </div>

                        <div class="key-container">
                            <label for="secretKey">Secret Key:</label>
                            <input type="text" id="secretKey" value="{{ $secretKey ?? '' }}" readonly>
                        </div>

                        <div class="key-container">
                            <label for="emergencyContactKey">Emergency Contact Key:</label>
                            <input type="text" id="emergencyContactKey" value="{{ $emergencyContactKey ?? '' }}"
                                readonly>
                        </div>

                        <button onclick="downloadKeys()">Download Keys</button>
                    @endif

                    <script>
                        function downloadKeys() {
                            var idKey = document.getElementById('idKey').value;
                            var secretKey = document.getElementById('secretKey').value;
                            var emergencyContactKey = document.getElementById('emergencyContactKey').value;

                            var content = "ID Key: " + idKey + "\n";
                            content += "Secret Key: " + secretKey + "\n";
                            content += "Emergency Contact Key: " + emergencyContactKey;

                            var blob = new Blob([content], {
                                type: 'text/plain'
                            });
                            var url = window.URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'keys.txt';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                        }

                        // Automatically trigger download when the page loads
                        window.onload = function() {
                            if (!{{ $keysExist ? 'true' : 'false' }}) {
                                downloadKeys();
                            }
                        };
                    </script>
                </section>
            </div>
            </section>
        </div>
    </div>
    </div>
</body>

</html>
