<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Text with Links</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full viewport height */
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .centered-text {
            text-align: center;
            margin-bottom: 20px; /* Space between text and links */
        }
        a {
            display: block; /* Make links stack vertically */
            margin: 5px 0; /* Space between links */
            text-decoration: none; /* Remove underline */
            color: blue; /* Link color */
        }
        a:hover {
            text-decoration: underline; /* Underline on hover */
        }
    </style>
</head>
<body>

    <div class="centered-text">
        <h1>Vital Lock</h1>
        <p>Privacy Focused Health Passport</p>
    </div>

    <a href="{{route('login')}}">Login</a>


</body>
</html>
