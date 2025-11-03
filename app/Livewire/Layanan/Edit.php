<?php

namespace App\Livewire\Layanan;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasDataFormatting;

class Edit extends Component
{
    use Toast, HasDataFormatting;

    public string $idLayanan;

    public array $formData = [
        'id_layanan' => '',
        'nama_layanan' => '',
        'harga_per_kg' => '',
        'durasi_jam' => '',
        'deskripsi' => '',
        'status' => 'Aktif',
    ];

    public function mount($id)
    {
        $this->idLayanan = $id;
        $this->loadLayanan();
    }

    protected function loadLayanan()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $layanan = $data->firstWhere('ID Layanan', $this->idLayanan);

            if ($layanan) {
                $this->formData = [
                    'id_layanan' => $layanan['ID Layanan'] ?? '',
                    'nama_layanan' => $layanan['Nama Layanan'] ?? '',
                    'harga_per_kg' => $this->parseHarga($layanan['Harga per Kg'] ?? 0), // Parse dari "5.000" atau "5000"
                    'durasi_jam' => (int) str_replace(['.', ','], '', $layanan['Durasi (Jam)'] ?? 0), // Hilangkan pemisah ribuan
                    'deskripsi' => $layanan['Deskripsi'] ?? '',
                    'status' => $layanan['Status'] ?? 'Aktif',
                ];
            } else {
                $this->error('Layanan tidak ditemukan', position: 'toast-bottom');
                return $this->redirect('/layanan', navigate: true);
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
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');

            $data = [
                'ID Layanan' => $this->formData['id_layanan'],
                'Nama Layanan' => $this->formData['nama_layanan'],
                'Harga per Kg' => (string) $this->formData['harga_per_kg'], // Simpan sebagai string agar tidak berubah
                'Durasi (Jam)' => (string) $this->formData['durasi_jam'], // Simpan sebagai string agar tidak berubah
                'Deskripsi' => $this->formData['deskripsi'],
                'Status' => $this->formData['status'],
            ];

            $sheetdb->update('ID Layanan', $this->formData['id_layanan'], $data);
            $this->purgeCache();
            $this->success('Layanan berhasil diupdate!', position: 'toast-bottom');

            return $this->redirect('/layanan', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan layanan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.layanan.edit');
    }
}
