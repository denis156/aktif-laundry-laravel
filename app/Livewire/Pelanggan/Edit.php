<?php

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasDataFormatting;

class Edit extends Component
{
    use Toast, HasDataFormatting;

    public string $idPelanggan;

    public array $formData = [
        'id_pelanggan' => '',
        'nama' => '',
        'no_hp' => '',
        'alamat' => '',
        'email' => '',
        'tanggal_daftar' => '',
        'total_transaksi' => 0,
        'status' => 'Aktif',
    ];

    public function mount($id)
    {
        $this->idPelanggan = $id;
        $this->loadPelanggan();
    }

    protected function loadPelanggan()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $pelanggan = $data->firstWhere('ID Pelanggan', $this->idPelanggan);

            if ($pelanggan) {
                $this->formData = [
                    'id_pelanggan' => $pelanggan['ID Pelanggan'] ?? '',
                    'nama' => $pelanggan['Nama'] ?? '',
                    'no_hp' => $pelanggan['No. HP'] ?? '',
                    'alamat' => $pelanggan['Alamat'] ?? '',
                    'email' => $pelanggan['Email'] ?? '',
                    'tanggal_daftar' => $this->parseDate($pelanggan['Tanggal Daftar'] ?? ''), // Gunakan trait method
                    'total_transaksi' => (int) ($pelanggan['Total Transaksi'] ?? 0), // Cast ke int
                    'status' => $pelanggan['Status'] ?? 'Aktif',
                ];
            } else {
                $this->error('Pelanggan tidak ditemukan', position: 'toast-bottom');
                return $this->redirect('/pelanggan', navigate: true);
            }
        } catch (\Exception $e) {
            $this->error('Gagal memuat data: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    protected function purgeCache(): void
    {
        try {
            $cacheKey = config('app.api_dbsheet_cache_key');
            $apiId = config('app.api_dbsheet');
            Http::get("https://sheetdb.io/api/v1/{$apiId}/cache/purge/{$cacheKey}");
        } catch (\Exception $e) {
        }
    }

    public function save()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');

            $data = [
                'ID Pelanggan' => $this->formData['id_pelanggan'],
                'Nama' => $this->formData['nama'],
                'No. HP' => $this->formData['no_hp'],
                'Alamat' => $this->formData['alamat'],
                'Email' => $this->formData['email'],
                'Tanggal Daftar' => $this->formData['tanggal_daftar'],
                'Total Transaksi' => (string) $this->formData['total_transaksi'], // Simpan sebagai string agar tidak berubah
                'Status' => $this->formData['status'],
            ];

            $sheetdb->update('ID Pelanggan', $this->formData['id_pelanggan'], $data);
            $this->purgeCache();
            $this->success('Pelanggan berhasil diupdate!', position: 'toast-bottom');

            return $this->redirect('/pelanggan', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan pelanggan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.edit');
    }
}
