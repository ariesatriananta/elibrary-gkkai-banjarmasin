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

    public function changePassword()
    {
        $adminId = (int) session('admin_id');

        if ($adminId <= 0) {
            return redirect()->to(site_url('login'))->with('error', 'Sesi login tidak ditemukan.');
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        $errors = [];

        if ($currentPassword === '') {
            $errors['current_password'] = 'Password saat ini wajib diisi.';
        }

        if ($newPassword === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($newPassword) < 8) {
            $errors['new_password'] = 'Password baru minimal 8 karakter.';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
        } elseif ($confirmPassword !== $newPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak sama.';
        }

        if ($errors !== []) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Periksa kembali form ganti password.')
                ->with('account_password_errors', $errors)
                ->with('account_dropdown_open', true)
                ->with('account_password_open', true);
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->find($adminId);

        if (! $admin || (int) ($admin['is_active'] ?? 0) !== 1) {
            return redirect()->back()
                ->with('error', 'Akun admin tidak ditemukan atau sudah tidak aktif.')
                ->with('account_dropdown_open', true);
        }

        if (! password_verify($currentPassword, $admin['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Password saat ini tidak sesuai.')
                ->with('account_password_errors', [
                    'current_password' => 'Password saat ini tidak sesuai.',
                ])
                ->with('account_dropdown_open', true)
                ->with('account_password_open', true);
        }

        if (password_verify($newPassword, $admin['password_hash'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Password baru harus berbeda dari password saat ini.')
                ->with('account_password_errors', [
                    'new_password' => 'Gunakan password baru yang berbeda.',
                ])
                ->with('account_dropdown_open', true)
                ->with('account_password_open', true);
        }

        $adminModel->update($adminId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->back()
            ->with('success', 'Password berhasil diperbarui.')
            ->with('account_dropdown_open', true);
    }
}
