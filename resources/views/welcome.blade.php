<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SplaceConnectED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #121212; 
            color: #ffffff; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .welcome-card {
            text-align: center;
            max-width: 500px;
            padding: 40px;
        }
        .logo-box {
            background-color: #ff2d75;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-weight: bold;
            font-size: 24px;
            color: white;
            box-shadow: 0 0 15px rgba(255, 45, 117, 0.4);
        }
        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 10px;
        }
        .brand-pink { color: #ff2d75; }
        .summary {
            color: #888;
            font-size: 1rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .btn-pink { 
            background-color: #ff2d75; 
            border: none; 
            color: white; 
            padding: 12px 30px; 
            border-radius: 8px; 
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-pink:hover { background-color: #e62668; transform: translateY(-2px); }
        .btn-outline-white {
            border: 1px solid #444;
            color: #fff;
            padding: 12px 30px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-outline-white:hover { border-color: #ff2d75; color: #ff2d75; }
    </style>
</head>
<body>

    <div class="welcome-card">
        <div class="logo-box">S</div>

        <div class="brand-name">
            SPLACE<span class="brand-pink">CONNECTED</span>
        </div>

        <p class="summary">
            A streamlined employee management system for attendance tracking and payroll operations. 
            Sign in to access your dashboard.
        </p>

        <div class="d-flex gap-3 justify-content-center">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-pink">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-pink">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-white">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

</body>
</html>