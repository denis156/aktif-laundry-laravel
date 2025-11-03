<?php

namespace App\Livewire\Layanan;

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
        'id_layanan' => '',
        'nama_layanan' => '',
        'harga_per_kg' => '',
        'durasi_jam' => '',
        'deskripsi' => '',
        'status' => 'Aktif',
    ];

    public function mount()
    {
        $this->formData['id_layanan'] = $this->generateId();
    }

    protected function generateId(): string
    {
        // Get prefix from Setting
        $prefix = $this->getSetting('format_id_layanan', 'LYN');
        $prefixLength = strlen($prefix);

        $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');
        $response = $sheetdb->get();
        $data = collect(json_decode(json_encode($response), true));

        $maxId = $data->map(function ($item) use ($prefix, $prefixLength) {
            $id = $item['ID Layanan'] ?? $prefix . '000';
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
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');

            $data = [
                'ID Layanan' => $this->formData['id_layanan'],
                'Nama Layanan' => $this->formData['nama_layanan'],
                'Harga per Kg' => (string) $this->formData['harga_per_kg'], // Simpan sebagai string agar tidak berubah
                'Durasi (Jam)' => (string) $this->formData['durasi_jam'], // Simpan sebagai string agar tidak berubah
                'Deskripsi' => $this->formData['deskripsi'],
                'Status' => $this->formData['status'],
            ];

            $sheetdb->create($data);
            $this->purgeCache();
            $this->success('Layanan berhasil ditambahkan!', position: 'toast-bottom');

            return $this->redirect('/layanan', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan layanan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.layanan.create');
    }
}
