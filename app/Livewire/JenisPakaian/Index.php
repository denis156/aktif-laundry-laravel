<?php

namespace App\Livewire\JenisPakaian;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    use Toast, WithPagination;

    public string $search = '';
    public bool $drawer = false;
    public bool $deleteModal = false;
    public string $deleteId = '';
    public string $deleteName = '';
    public array $sortBy = ['column' => 'id_jenis', 'direction' => 'desc'];
    public string $statusFilter = '';
    public int $perPage = 10;

    public function clear(): void
    {
        $this->reset(['search', 'statusFilter']);
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

    public function confirmDelete($idJenis, $nama): void
    {
        $this->deleteId = $idJenis;
        $this->deleteName = $nama;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');
            $sheetdb->delete('ID Jenis', $this->deleteId);
            $this->purgeCache();
            $this->success("Jenis Pakaian {$this->deleteName} berhasil dihapus!", position: 'toast-bottom');
            $this->deleteModal = false;
            $this->reset(['deleteId', 'deleteName']);
        } catch (\Exception $e) {
            $this->error('Gagal menghapus jenis pakaian: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id_jenis', 'label' => 'ID', 'class' => 'w-20'],
            ['key' => 'nama_jenis', 'label' => 'Nama Jenis', 'class' => 'w-48'],
            ['key' => 'keterangan', 'label' => 'Keterangan'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ];
    }

    public function jenisPakaian()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $transformed = $data->map(function ($item) {
                return [
                    'id_jenis' => $item['ID Jenis'] ?? '',
                    'nama_jenis' => $item['Nama Jenis'] ?? '',
                    'keterangan' => $item['Keterangan'] ?? '',
                    'status' => $item['Status'] ?? '',
                ];
            });

            $filtered = $transformed
                ->when($this->search, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['nama_jenis'])->contains($this->search, true) ||
                        str($item['keterangan'])->contains($this->search, true) ||
                        str($item['id_jenis'])->contains($this->search, true)
                    );
                })
                ->when($this->statusFilter, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['status'])->lower() == str($this->statusFilter)->lower()
                    );
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
        return view('livewire.jenis-pakaian.index', [
            'jenisPakaian' => $this->jenisPakaian(),
            'headers' => $this->headers()
        ]);
    }
}
