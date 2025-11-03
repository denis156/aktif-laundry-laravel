<?php

namespace App\Livewire\JenisPakaian;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;

class Edit extends Component
{
    use Toast;

    public string $idJenis;

    public array $formData = [
        'id_jenis' => '',
        'nama_jenis' => '',
        'keterangan' => '',
        'status' => 'Aktif',
    ];

    public function mount($id)
    {
        $this->idJenis = $id;
        $this->loadJenisPakaian();
    }

    protected function loadJenisPakaian()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $jenisPakaian = $data->firstWhere('ID Jenis', $this->idJenis);

            if ($jenisPakaian) {
                $this->formData = [
                    'id_jenis' => $jenisPakaian['ID Jenis'] ?? '',
                    'nama_jenis' => $jenisPakaian['Nama Jenis'] ?? '',
                    'keterangan' => $jenisPakaian['Keterangan'] ?? '',
                    'status' => $jenisPakaian['Status'] ?? 'Aktif',
                ];
            } else {
                $this->error('Jenis Pakaian tidak ditemukan', position: 'toast-bottom');
                return $this->redirect('/jenis-pakaian', navigate: true);
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
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');

            $data = [
                'ID Jenis' => $this->formData['id_jenis'],
                'Nama Jenis' => $this->formData['nama_jenis'],
                'Keterangan' => $this->formData['keterangan'],
                'Status' => $this->formData['status'],
            ];

            $sheetdb->update('ID Jenis', $this->formData['id_jenis'], $data);
            $this->purgeCache();
            $this->success('Jenis Pakaian berhasil diupdate!', position: 'toast-bottom');

            return $this->redirect('/jenis-pakaian', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan jenis pakaian: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.jenis-pakaian.edit');
    }
}
