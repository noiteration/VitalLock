<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Keys</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .key-container {
            margin-bottom: 15px;
        }
        input[type="text"] {
            width: 100%;
            max-width: 300px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        button {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>User Keys</h1>

    @if(isset($keysExist) && $keysExist)
        <p>Your keys have already been generated. If you need to recover them, please contact support.</p>
    @else
        <p>Your keys have been generated. Please save them securely as they won't be shown again.</p>
        
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
            <input type="text" id="emergencyContactKey" value="{{ $emergencyContactKey ?? '' }}" readonly>
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

            var blob = new Blob([content], { type: 'text/plain' });
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
</body>
</html>
