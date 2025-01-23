<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPULO</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #d32f2f, #f44336); /* Warna merah Telkom */
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            padding: 20px;
            position: relative;
            text-align: center;
        }

        .login-container h1 {
            margin: 0;
            font-size: 18px;
            color: #d32f2f; /* Merah Telkom */
            font-family: Arial, sans-serif;
        }

        .login-container h2 {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
            font-family: Arial, sans-serif;
        }

        .login-container img {
            margin: 10px 0;
        }

        .login-container label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
            font-family: Arial, sans-serif;
        }

        .login-container input {
            width: 93%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            font-family: Arial, sans-serif;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container input {
            padding-right: 35px;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 30%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #555;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #d32f2f; /* Warna merah Telkom */
            color: white;
            border: none;
            border-radius: 5px;
            font-family: Arial, sans-serif;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #b71c1c;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            color: #d32f2f;
            cursor: pointer;
        }

        .error-message {
            color: red;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Sistem Informasi Penjadwalan Uji Laboratorium Optik (SIPULO)</h1>
        <img src="{{ asset('storage/avatar/ftth.png') }}" alt="Logo Telkom" style="width: 100px; height: 70px; margin: 10px auto;">

        @if($errors->any())
            <div>
                @foreach($errors->all() as $error)
                    <p class="error-message">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Masukan Email" required>

            <label for="password">Password:</label>
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Masukan Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Login</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈'; 
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️'; 
            }
        }
    </script>
</body>
</html>
