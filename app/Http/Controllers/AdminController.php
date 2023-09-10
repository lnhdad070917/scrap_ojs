<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        // Periksa apakah pengguna sudah login
        if (Auth::check()) {
            return redirect('/admin');
        }
        return view('auth/pages/login');
    }

    public function dashboard()
    {
        return view('admin/pages/dashboard');
    }

    public function showRegistrationForm()
    {
        return view('auth/pages/register');
    }

    // Metode untuk menangani registrasi
    public function register(Request $request)
    {
        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Metode untuk menangani login
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::where('username', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            return redirect('/admin');
        }

        return back()->withErrors(['username' => 'Username atau password salah']);
    }


    public function logout(Request $request)
    {
        Auth::logout(); // Melakukan logout

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}