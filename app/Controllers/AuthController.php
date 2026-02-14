<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $username = trim((string) $this->request->getPost('username'));
            $password = (string) $this->request->getPost('password');

            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->where('is_active', 1)->first();

            if ($user && password_verify($password, $user['password_hash'])) {
                session()->regenerate();
                session()->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                ]);

                $userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
                AuditLogger::log('login', 'auth', $user['id']);

                return redirect()->to('/dashboard');
            }

            return redirect()->back()->withInput()->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }

        return view('auth/login');
    }

    public function logout()
    {
        AuditLogger::log('logout', 'auth', session()->get('user_id'));
        session()->destroy();

        return redirect()->to('/login')->with('success', 'ออกจากระบบแล้ว');
    }
}
