<?php

namespace App\Livewire\JenisPakaian;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasSettings;

class Create extends Component
{
    use Toast, HasSettings;

    public array $formData = [
        'id_jenis' => '',
        'nama_jenis' => '',
        'keterangan' => '',
        'status' => 'Aktif',
    ];

    public function mount()
    {
        $this->formData['id_jenis'] = $this->generateId();
    }

    protected function generateId(): string
    {
        // Get prefix from Setting
        $prefix = $this->getSetting('format_id_jenis_pakaian', 'JNS');
        $prefixLength = strlen($prefix);

        $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');
        $response = $sheetdb->get();
        $data = collect(json_decode(json_encode($response), true));

        $maxId = $data->map(function ($item) use ($prefix, $prefixLength) {
            $id = $item['ID Jenis'] ?? $prefix . '000';
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
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');

            $data = [
                'ID Jenis' => $this->formData['id_jenis'],
                'Nama Jenis' => $this->formData['nama_jenis'],
                'Keterangan' => $this->formData['keterangan'],
                'Status' => $this->formData['status'],
            ];

            $sheetdb->create($data);
            $this->purgeCache();
            $this->success('Jenis Pakaian berhasil ditambahkan!', position: 'toast-bottom');

            return $this->redirect('/jenis-pakaian', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan jenis pakaian: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.jenis-pakaian.create');
    }
}
