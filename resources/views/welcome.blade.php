<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SplaceConnectED | Workforce Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: #0a0a0a; 
            color: #ffffff; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .welcome-card {
            text-align: center;
            max-width: 450px;
            padding: 40px;
            background: #111;
            border: 1px solid #1f1f1f;
            border-radius: 24px;
        }
        .brand-name {
            font-size: 2.5rem;
            font-weight: 900;
            letter-spacing: -2px;
            margin-bottom: 8px;
        }
        .brand-pink { color: #ff2d75; }
        .summary {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-pink { 
            background-color: #ff2d75; 
            border: none; 
            color: white; 
            padding: 12px 28px; 
            border-radius: 12px; 
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-pink:hover { background-color: #d62562; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="welcome-card shadow-lg">
        <div class="brand-name">
            SPLACE<span class="brand-pink">ED</span>
        </div>
        <p class="summary">
            Enterprise-grade workforce management. <br>
            Secure access to attendance and payroll.
        </p>

        <div class="d-grid gap-3">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-pink">Enter Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-pink">Login to System</a>
                @endauth
            @endif
        </div>
    </div>

</body>
</html>