<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }} — billable</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #030712;
            color: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container { text-align: center; max-width: 480px; }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 3rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 32px; height: 32px;
            background: #7c3aed;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: white;
        }
        .logo-text { font-size: 1.1rem; font-weight: 600; color: white; }
        .code {
            font-size: 6rem;
            font-weight: 800;
            color: #7c3aed;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; }
        .desc { color: #9ca3af; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            background: #7c3aed;
            color: white;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn:hover { background: #6d28d9; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="logo">
            <div class="logo-icon">B</div>
            <span class="logo-text">billable</span>
        </a>
        @yield('content')
    </div>
</body>
</html>
