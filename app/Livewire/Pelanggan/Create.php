<?php

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasSettings;
use App\Traits\HasDataFormatting;

class Create extends Component
{
    use Toast, HasSettings, HasDataFormatting;

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

    public function mount()
    {
        $this->formData['id_pelanggan'] = $this->generateId();
        $this->formData['tanggal_daftar'] = date('Y-m-d');
    }

    protected function generateId(): string
    {
        // Get prefix from Setting
        $prefix = $this->getSetting('format_id_pelanggan', 'PLG');
        $prefixLength = strlen($prefix);

        $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
        $response = $sheetdb->get();
        $data = collect(json_decode(json_encode($response), true));

        $maxId = $data->map(function ($item) use ($prefix, $prefixLength) {
            $id = $item['ID Pelanggan'] ?? $prefix . '000';
            // Extract number part after prefix
            return (int) substr($id, $prefixLength);
        })->max();

        $newIdNumber = $maxId + 1;
        return $prefix . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
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

            $sheetdb->create($data);
            $this->purgeCache();
            $this->success('Pelanggan berhasil ditambahkan!', position: 'toast-bottom');

            return $this->redirect('/pelanggan', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan pelanggan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.create');
    }
}
