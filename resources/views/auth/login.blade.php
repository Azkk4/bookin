<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | BookIn'</title>

    @vite('resources/css/auth.css')

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop playsinline class="bg-video">
        <source src="{{ asset('videos/schale.mp4') }}" type="video/mp4">
    </video>

    <div class="overlay-top"></div>
    <div class="overlay-bottom"></div>

    <div class="auth-card">

        <div class="auth-logo">

            <img src="{{ asset('images/Trinity Logo Cropped.png') }}">

            <h1>BookIn'</h1>

            <span>Digital Library System</span>

        </div>

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <div class="input-group">

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    value="{{ old('email') }}"
                >

            </div>

            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >

            </div>

            @if ($errors->any())
                <div class="error-text">
                    {{ $errors->first() }}
                </div>
            @endif

            <button class="auth-btn" type="submit">
                Login
            </button>

        </form>

        <div class="auth-footer">

            Belum punya akun?

            <a href="{{ route('register') }}">
                Register
            </a>

        </div>

    </div>

</body>
</html>