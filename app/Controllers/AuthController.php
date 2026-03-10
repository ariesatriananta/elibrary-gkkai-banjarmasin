<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->has('admin_id')) {
            return redirect()->to(site_url('/'));
        }

        return view('auth/login', [
            'pageTitle' => 'Login',
        ]);
    }

    public function attemptLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($username === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $admin = (new AdminModel())
            ->where('username', $username)
            ->where('is_active', 1)
            ->first();

        if (! $admin || ! password_verify($password, $admin['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Login gagal. Periksa kembali username dan password.');
        }

        session()->set([
            'admin_id' => $admin['id'],
            'admin_name' => $admin['full_name'],
            'admin_username' => $admin['username'],
            'is_authenticated' => true,
        ]);

        (new AdminModel())->update($admin['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
        ]);

        $intendedUrl = session('intended_url') ?: site_url('/');
        session()->remove('intended_url');

        return redirect()->to($intendedUrl)->with('success', 'Login berhasil.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'))->with('success', 'Anda sudah logout.');
    }
}
