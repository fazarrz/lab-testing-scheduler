<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = JWTAuth::attempt($credentials)) {
            $user = Auth::user();

            // Set token in cookie and redirect based on role
            $redirectRoute = 'admin.dashboard';

            return redirect()->route($redirectRoute)->withCookie(cookie('token', $token, 120));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return redirect()->route('login')->withoutCookie('token')->with('message', 'Logged out successfully');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Tetapkan role default sebagai engineer
        $role = $request->input('role', 'engineer'); // Gunakan 'engineer' jika role tidak disediakan

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        // Login otomatis setelah registrasi
        $token = JWTAuth::fromUser($user);

        // Redirect ke dashboard sesuai role
        $redirectRoute = 'admin.dashboard';

        return redirect()->route($redirectRoute)->withCookie(cookie('token', $token, 60));
    }
}
