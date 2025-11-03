<?php

namespace App\Livewire\Pelanggan;

use Exception;
use SheetDB\SheetDB;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
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

    public array $sortBy = ['column' => 'id_pelanggan', 'direction' => 'desc'];

    public string $statusFilter = '';

    public int $minTransaksi = 0;

    public int $maxTransaksi = 999999;

    public int $perPage = 10;

    // Clear filters
    public function clear(): void
    {
        $this->reset(['search', 'statusFilter', 'minTransaksi', 'maxTransaksi']);
        $this->success('Filter berhasil dibersihkan.', position: 'toast-bottom');
    }

    // Purge SheetDB cache
    protected function purgeCache(): void
    {
        try {
            $cacheKey = config('app.api_dbsheet_cache_key');
            $apiId = config('app.api_dbsheet');

            Http::get("https://sheetdb.io/api/v1/{$apiId}/cache/purge/{$cacheKey}");
        } catch (Exception $e) {
            // Silent fail, cache purge is not critical
        }
    }

    // Show delete confirmation modal
    public function confirmDelete($idPelanggan, $nama): void
    {
        $this->deleteId = $idPelanggan;
        $this->deleteName = $nama;
        $this->deleteModal = true;
    }

    // Delete action
    public function delete(): void
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
            $sheetdb->delete('ID Pelanggan', $this->deleteId);

            // Purge cache after deletion
            $this->purgeCache();

            $this->success("Pelanggan {$this->deleteName} berhasil dihapus!", position: 'toast-bottom');
            $this->deleteModal = false;
            $this->reset(['deleteId', 'deleteName']);
        } catch (Exception $e) {
            $this->error('Gagal menghapus pelanggan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id_pelanggan', 'label' => 'ID', 'class' => 'w-20'],
            ['key' => 'nama', 'label' => 'Nama', 'class' => 'w-48'],
            ['key' => 'no_hp', 'label' => 'No. HP', 'class' => 'w-32'],
            ['key' => 'email', 'label' => 'Email', 'class' => 'w-48'],
            ['key' => 'total_transaksi', 'label' => 'Total Transaksi', 'class' => 'w-32'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ];
    }

    /**
     * Fetch pelanggan data from SheetDB
     */
    public function pelanggan()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
            $response = $sheetdb->get();

            // Convert SheetDB response to array, then collection
            $data = collect(json_decode(json_encode($response), true));

            // Transform data to match our structure
            $transformed = $data->map(function ($item) {
                return [
                    'id_pelanggan' => $item['ID Pelanggan'] ?? '',
                    'nama' => $item['Nama'] ?? '',
                    'no_hp' => $item['No. HP'] ?? '',
                    'alamat' => $item['Alamat'] ?? '',
                    'email' => $item['Email'] ?? '',
                    'tanggal_daftar' => $item['Tanggal Daftar'] ?? '',
                    'total_transaksi' => (int) ($item['Total Transaksi'] ?? 0), // Cast ke int
                    'status' => $item['Status'] ?? '',
                ];
            });

            $filtered = $transformed
                ->when($this->search, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['nama'])->contains($this->search, true) ||
                        str($item['no_hp'])->contains($this->search, true) ||
                        str($item['id_pelanggan'])->contains($this->search, true) ||
                        str($item['email'])->contains($this->search, true)
                    );
                })
                ->when($this->statusFilter, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['status'])->lower() == str($this->statusFilter)->lower()
                    );
                })
                ->filter(function (array $item) {
                    // Tidak perlu cast lagi karena sudah int dari transform
                    return $item['total_transaksi'] >= $this->minTransaksi && $item['total_transaksi'] <= $this->maxTransaksi;
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
        return view('livewire.pelanggan.index', [
            'pelanggan' => $this->pelanggan(),
            'headers' => $this->headers()
        ]);
    }
}
