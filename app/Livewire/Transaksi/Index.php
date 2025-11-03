<?php

namespace App\Livewire\Transaksi;

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
    public array $sortBy = ['column' => 'tanggal_masuk', 'direction' => 'desc'];
    public string $statusFilter = '';
    public string $metodePembayaranFilter = '';
    public string $tanggalDari = '';
    public string $tanggalSampai = '';
    public int $perPage = 10;

    public function clear(): void
    {
        $this->reset(['search', 'statusFilter', 'metodePembayaranFilter', 'tanggalDari', 'tanggalSampai']);
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

    public function confirmDelete($idTransaksi, $nama): void
    {
        $this->deleteId = $idTransaksi;
        $this->deleteName = $nama;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');
            $sheetdb->delete('ID Transaksi', $this->deleteId);
            $this->purgeCache();
            $this->success("Transaksi {$this->deleteName} berhasil dihapus!", position: 'toast-bottom');
            $this->deleteModal = false;
            $this->reset(['deleteId', 'deleteName']);
        } catch (\Exception $e) {
            $this->error('Gagal menghapus transaksi: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id_transaksi', 'label' => 'ID', 'class' => 'w-20'],
            ['key' => 'tanggal_masuk', 'label' => 'Tgl Masuk', 'class' => 'w-28'],
            ['key' => 'nama_pelanggan', 'label' => 'Pelanggan', 'class' => 'w-40'],
            ['key' => 'nama_layanan', 'label' => 'Layanan', 'class' => 'w-40'],
            ['key' => 'jenis_pakaian', 'label' => 'Jenis Pakaian', 'class' => 'w-48'],
            ['key' => 'berat_kg', 'label' => 'Berat', 'class' => 'w-20'],
            ['key' => 'total', 'label' => 'Total', 'class' => 'w-32'],
            ['key' => 'metode_pembayaran', 'label' => 'Pembayaran', 'class' => 'w-28'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-28'],
        ];
    }

    public function transaksi()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $transformed = $data->map(function ($item) {
                return [
                    'id_transaksi' => $item['ID Transaksi'] ?? '',
                    'tanggal_masuk' => $item['Tanggal Masuk'] ?? '',
                    'id_pelanggan' => $item['ID Pelanggan'] ?? '',
                    'nama_pelanggan' => $item['Nama Pelanggan'] ?? '',
                    'id_layanan' => $item['ID Layanan'] ?? '',
                    'nama_layanan' => $item['Nama Layanan'] ?? '',
                    'jenis_pakaian' => $item['Jenis Pakaian'] ?? '',
                    'berat_kg' => $this->parseBerat($item['Berat (Kg)'] ?? 0),
                    'harga_per_kg' => $this->parseHarga($item['Harga per Kg'] ?? 0),
                    'subtotal' => $this->parseHarga($item['Subtotal'] ?? 0),
                    'diskon' => $this->parseHarga($item['Diskon'] ?? 0),
                    'total' => $this->parseHarga($item['Total'] ?? 0),
                    'metode_pembayaran' => $item['Metode Pembayaran'] ?? '',
                    'tanggal_selesai' => $item['Tanggal Selesai'] ?? '',
                    'status' => $item['Status'] ?? '',
                    'catatan' => $item['Catatan'] ?? '',
                ];
            });

            $filtered = $transformed
                ->when($this->search, function (Collection $collection) {
                    return $collection->filter(fn(array $item) =>
                        str($item['id_transaksi'])->contains($this->search, true) ||
                        str($item['nama_pelanggan'])->contains($this->search, true) ||
                        str($item['nama_layanan'])->contains($this->search, true)
                    );
                })
                ->when($this->statusFilter, fn(Collection $collection) =>
                    $collection->filter(fn(array $item) =>
                        str($item['status'])->lower() == str($this->statusFilter)->lower()
                    )
                )
                ->when($this->metodePembayaranFilter, fn(Collection $collection) =>
                    $collection->filter(fn(array $item) =>
                        str($item['metode_pembayaran'])->lower() == str($this->metodePembayaranFilter)->lower()
                    )
                )
                ->when($this->tanggalDari, fn(Collection $collection) =>
                    $collection->filter(fn(array $item) => $item['tanggal_masuk'] >= $this->tanggalDari)
                )
                ->when($this->tanggalSampai, fn(Collection $collection) =>
                    $collection->filter(fn(array $item) => $item['tanggal_masuk'] <= $this->tanggalSampai)
                )
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
            $this->error('Gagal mengambil data: ' . $e->getMessage(), position: 'toast-bottom');
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function render()
    {
        return view('livewire.transaksi.index', [
            'transaksi' => $this->transaksi(),
            'headers' => $this->headers()
        ]);
    }
}
