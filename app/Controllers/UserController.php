<?php

namespace App\Controllers;

use App\Libraries\AuditLogger;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UserController extends BaseController
{
    public function index()
    {
        $users = (new UserModel())->orderBy('id', 'DESC')->findAll();

        return view('users/index', [
            'users' => $users,
        ]);
    }

    public function new()
    {
        return view('users/form', [
            'mode' => 'create',
            'user' => null,
        ]);
    }

    public function create()
    {
        $model = new UserModel();
        $username = trim((string) $this->request->getPost('username'));
        $fullName = trim((string) $this->request->getPost('full_name'));
        $role = (string) $this->request->getPost('role');
        $password = (string) $this->request->getPost('password');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if ($username === '' || $fullName === '' || $password === '') {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields.');
        }

        if (! in_array($role, ['admin', 'staff', 'doctor'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid role.');
        }

        if ($model->where('username', $username)->first()) {
            return redirect()->back()->withInput()->with('error', 'Username already exists.');
        }

        $data = [
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $fullName,
            'role' => $role,
            'is_active' => $isActive,
        ];

        $model->insert($data);
        $id = (int) $model->getInsertID();
        AuditLogger::log('create', 'user', $id, null, $data);

        return redirect()->to('/users')->with('success', 'User created successfully.');
    }

    public function edit(int $id)
    {
        $user = (new UserModel())->find($id);
        if (! $user) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('users/form', [
            'mode' => 'edit',
            'user' => $user,
        ]);
    }

    public function update(int $id)
    {
        $model = new UserModel();
        $old = $model->find($id);
        if (! $old) {
            throw PageNotFoundException::forPageNotFound();
        }

        $username = trim((string) $this->request->getPost('username'));
        $fullName = trim((string) $this->request->getPost('full_name'));
        $role = (string) $this->request->getPost('role');
        $password = (string) $this->request->getPost('password');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;

        if ($username === '' || $fullName === '') {
            return redirect()->back()->withInput()->with('error', 'Please fill all required fields.');
        }

        if (! in_array($role, ['admin', 'staff', 'doctor'], true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid role.');
        }

        $existing = $model->where('username', $username)->first();
        if ($existing && (int) $existing['id'] !== $id) {
            return redirect()->back()->withInput()->with('error', 'Username already exists.');
        }

        $data = [
            'username' => $username,
            'full_name' => $fullName,
            'role' => $role,
            'is_active' => $isActive,
        ];

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Prevent disabling your own active account by mistake.
        if ($id === (int) session()->get('user_id') && $isActive === 0) {
            return redirect()->back()->withInput()->with('error', 'Cannot disable your own account.');
        }

        $model->update($id, $data);
        AuditLogger::log('update', 'user', $id, $old, $data);

        return redirect()->to('/users')->with('success', 'User updated successfully.');
    }

    public function delete(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Cannot delete your own account.');
        }

        $model = new UserModel();
        $old = $model->find($id);
        if ($old) {
            $model->delete($id);
            AuditLogger::log('delete', 'user', $id, $old, null);
        }

        return redirect()->to('/users')->with('success', 'User deleted successfully.');
    }
}

