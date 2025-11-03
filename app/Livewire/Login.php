<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use SheetDB\SheetDB;

class Login extends Component
{
    use Toast;

    public string $username = '';
    public string $password = '';

    public function login()
    {
        // Validasi sederhana
        if (empty($this->username)) {
            $this->error('Username wajib diisi!', position: 'toast-top');
            return;
        }

        if (empty($this->password)) {
            $this->error('Password wajib diisi!', position: 'toast-top');
            return;
        }

        try {
            // Get users from SheetDB
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Users');
            $response = $sheetdb->get();
            $users = collect(json_decode(json_encode($response), true));

            // Cek username dan password
            $user = $users->first(function ($item) {
                return ($item['Username'] ?? '') === $this->username
                    && ($item['Password'] ?? '') === $this->password;
            });

            if ($user) {
                // Login berhasil - simpan ke session
                session([
                    'logged_in' => true,
                    'username' => $this->username
                ]);

                $this->success('Login berhasil! Selamat datang ' . $this->username, position: 'toast-bottom');

                // Redirect ke dashboard
                return $this->redirect('/', navigate: true);
            } else {
                $this->error('Username atau password salah!', position: 'toast-bottom');
            }

        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    #[Layout('components.layouts.guest')]
    public function render()
    {
        return view('livewire.login');
    }
}
