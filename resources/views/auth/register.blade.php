<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | BookIn'</title>

    @vite('resources/css/auth.css')

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <video autoplay muted loop playsinline class="bg-video">
        <source src="{{ asset('videos/schale.mp4') }}" type="video/mp4">
    </video>

    <div class="overlay-top"></div>
    <div class="overlay-bottom"></div>

    <div class="auth-card register-card">

        <div class="auth-logo">

            <img src="{{ asset('images/Trinity Logo Cropped.png') }}">

            <h1>BookIn'</h1>

            <span>Digital Library System</span>

        </div>

        <form method="POST" action="{{ route('register') }}">

            @csrf

            <div class="input-group">
                <input type="text" name="Username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <input type="email" name="Email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <input type="text" name="NamaLengkap" placeholder="Nama Lengkap" required>
            </div>

            <div class="input-group">
                <input type="text" name="Alamat" placeholder="Alamat" required>
            </div>

            <div class="input-group">
                <input type="password" name="Password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <input type="password" name="Password_confirmation" placeholder="Konfirmasi Password" required>
            </div>

            <button class="auth-btn" type="submit">
                Register
            </button>

        </form>

        <div class="auth-footer">

            Sudah punya akun?

            <a href="{{ route('login') }}">
                Login
            </a>

        </div>

    </div>

</body>
</html>