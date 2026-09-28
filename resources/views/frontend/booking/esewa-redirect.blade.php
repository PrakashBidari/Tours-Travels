<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Redirecting to eSewa…</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; min-height: 100vh; align-items: center; justify-content: center; background: #f8fafc; color: #0b1f47; margin: 0; }
        .box { text-align: center; }
        .spinner { width: 42px; height: 42px; border: 4px solid #d1fae5; border-top-color: #60bb46; border-radius: 50%; margin: 0 auto 16px; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        button { margin-top: 14px; background: #60bb46; color: #fff; border: 0; padding: 10px 22px; border-radius: 999px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
    <form id="esewa" method="POST" action="{{ $action }}" class="box">
        <div class="spinner"></div>
        <p>Redirecting you to <strong>eSewa</strong> to complete your payment…</p>
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <noscript><button type="submit">Continue to eSewa</button></noscript>
    </form>
    <script>document.getElementById('esewa').submit();</script>
</body>
</html>
