<!-- resources/views/errors/custom-error.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f44336, #e91e63);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .error-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            text-align: center;
            animation: pop 0.4s ease-out;
        }
        .error-box h1 {
            font-size: 3em;
            margin-bottom: 10px;
        }
        .error-box p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .error-box a {
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .error-box a:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        @keyframes pop {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>Oops!</h1>
        <p>{{ $message ?? 'An unexpected error occurred. Please try again later.' }}</p>
        <a href="{{ url('/') }}">Back to Home</a>
    </div>
</body>
</html>
