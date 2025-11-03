<?php

namespace App\Livewire\Layanan;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasDataFormatting;

class Index extends Component
{
    use Toast, HasDataFormatting, WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public bool $deleteModal = false;
    public string $deleteId = '';
    public string $deleteName = '';
    public array $sortBy = ['column' => 'id_layanan', 'direction' => 'desc'];
    public string $statusFilter = '';
    public int $minHarga = 0;
    public int $maxHarga = 999999;
    public int $perPage = 10;

    public function clear(): void
    {
        $this->reset(['search', 'statusFilter', 'minHarga', 'maxHarga']);
        $this->success('Filter berhasil dibersihkan.', position: 'toast-bottom');
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

    public function confirmDelete($idLayanan, $nama): void
    {
        $this->deleteId = $idLayanan;
        $this->deleteName = $nama;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');
            $sheetdb->delete('ID Layanan', $this->deleteId);
            $this->purgeCache();
            $this->success("Layanan {$this->deleteName} berhasil dihapus!", position: 'toast-bottom');
            $this->deleteModal = false;
            $this->reset(['deleteId', 'deleteName']);
        } catch (\Exception $e) {
            $this->error('Gagal menghapus layanan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id_layanan', 'label' => 'ID', 'class' => 'w-16'],
            ['key' => 'nama_layanan', 'label' => 'Nama Layanan', 'class' => 'w-48'],
            ['key' => 'harga_per_kg', 'label' => 'Harga/Kg', 'class' => 'w-32'],
            ['key' => 'durasi_jam', 'label' => 'Durasi (Jam)', 'class' => 'w-32'],
            ['key' => 'deskripsi', 'label' => 'Deskripsi'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ];
    }

    public function layanan()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $transformed = $data->map(function ($item) {
                return [
                    'id_layanan' => $item['ID Layanan'] ?? '',
                    'nama_layanan' => $item['Nama Layanan'] ?? '',
                    'harga_per_kg' => $this->parseHarga($item['Harga per Kg'] ?? 0), // Parse dari format "5.000" atau "5000"
                    'durasi_jam' => (int) str_replace(['.', ','], '', $item['Durasi (Jam)'] ?? 0), // Hilangkan pemisah ribuan
                    'deskripsi' => $item['Deskripsi'] ?? '',
                    'status' => $item['Status'] ?? '',
                ];
            });

            $filtered = $transformed
                ->when($this->search, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['nama_layanan'])->contains($this->search, true) ||
                        str($item['deskripsi'])->contains($this->search, true) ||
                        str($item['id_layanan'])->contains($this->search, true)
                    );
                })
                ->when($this->statusFilter, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['status'])->lower() == str($this->statusFilter)->lower()
                    );
                })
                ->filter(function (array $item) {
                    // Tidak perlu cast lagi karena sudah float dari transform
                    return $item['harga_per_kg'] >= $this->minHarga && $item['harga_per_kg'] <= $this->maxHarga;
                })
                ->sortBy([[...array_values($this->sortBy)]]);

            // Manual pagination untuk collection
            $currentPage = $this->getPage();
            $items = $filtered->forPage($currentPage, $this->perPage);

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $filtered->count(),
                $this->perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        } catch (\Exception $e) {
            $this->error('Gagal mengambil data dari SheetDB: ' . $e->getMessage(), position: 'toast-bottom');
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function render()
    {
        return view('livewire.layanan.index', [
            'layanan' => $this->layanan(),
            'headers' => $this->headers()
        ]);
    }
}
