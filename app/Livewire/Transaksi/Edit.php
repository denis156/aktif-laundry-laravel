<?php

namespace App\Livewire\Transaksi;

use Mary\Traits\Toast;
use Livewire\Component;
use Illuminate\Support\Collection;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasDataFormatting;

class Edit extends Component
{
    use Toast, HasDataFormatting;

    public string $idTransaksi;

    public array $formData = [
        'id_transaksi' => '',
        'tanggal_masuk' => '',
        'id_pelanggan' => '',
        'nama_pelanggan' => '',
        'id_layanan' => '',
        'nama_layanan' => '',
        'jenis_pakaian' => '',
        'berat_kg' => '',
        'harga_per_kg' => '',
        'subtotal' => 0,
        'diskon' => 0,
        'total' => 0,
        'metode_pembayaran' => 'Tunai',
        'tanggal_selesai' => '',
        'status' => 'Menunggu',
        'catatan' => '',
    ];

    public Collection $pelangganList;
    public Collection $layananList;

    // Listener untuk event dari component JenisPakaianInput
    protected $listeners = ['jenisPakaianUpdated'];

    public function jenisPakaianUpdated($value)
    {
        $this->formData['jenis_pakaian'] = $value;
    }

    public function mount($id)
    {
        $this->idTransaksi = $id;
        $this->loadPelangganList();
        $this->loadLayananList();
        $this->loadTransaksi();
    }

    protected function loadPelangganList()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $this->pelangganList = $data
                ->where('Status', 'Aktif')
                ->map(function ($item) {
                    return [
                        'id' => $item['ID Pelanggan'] ?? '',
                        'name' => $item['Nama'] ?? '',
                    ];
                });
        } catch (\Exception $e) {
            $this->pelangganList = collect([]);
        }
    }

    protected function loadLayananList()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Layanan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $this->layananList = $data
                ->where('Status', 'Aktif')
                ->map(function ($item) {
                    $harga = $this->parseHarga($item['Harga per Kg'] ?? 0);
                    return [
                        'id' => $item['ID Layanan'] ?? '',
                        'name' => $item['Nama Layanan'] . ' - Rp ' . number_format($harga, 0, ',', '.'),
                        'harga' => $harga,
                    ];
                });
        } catch (\Exception $e) {
            $this->layananList = collect([]);
        }
    }

    protected function loadTransaksi()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $transaksi = $data->first(function ($item) {
                return ($item['ID Transaksi'] ?? '') === $this->idTransaksi;
            });

            if ($transaksi) {
                $this->formData = [
                    'id_transaksi' => $transaksi['ID Transaksi'] ?? '',
                    'tanggal_masuk' => $this->convertToDatetimeLocal($transaksi['Tanggal Masuk'] ?? ''),
                    'id_pelanggan' => $transaksi['ID Pelanggan'] ?? '',
                    'nama_pelanggan' => $transaksi['Nama Pelanggan'] ?? '',
                    'id_layanan' => $transaksi['ID Layanan'] ?? '',
                    'nama_layanan' => $transaksi['Nama Layanan'] ?? '',
                    'jenis_pakaian' => $transaksi['Jenis Pakaian'] ?? '',
                    'berat_kg' => $this->parseBerat($transaksi['Berat (Kg)'] ?? 0), // Float murni agar input number tampil dengan titik
                    'harga_per_kg' => $this->parseHarga($transaksi['Harga per Kg'] ?? 0),
                    'subtotal' => $this->parseHarga($transaksi['Subtotal'] ?? 0),
                    'diskon' => $this->parseHarga($transaksi['Diskon'] ?? 0),
                    'total' => $this->parseHarga($transaksi['Total'] ?? 0),
                    'metode_pembayaran' => $transaksi['Metode Pembayaran'] ?? 'Tunai',
                    'tanggal_selesai' => $this->convertToDatetimeLocal($transaksi['Tanggal Selesai'] ?? ''),
                    'status' => $transaksi['Status'] ?? 'Menunggu',
                    'catatan' => $transaksi['Catatan'] ?? '',
                ];
            } else {
                $this->error('Transaksi tidak ditemukan', position: 'toast-bottom');
                return $this->redirect('/transaksi', navigate: true);
            }
        } catch (\Exception $e) {
            $this->error('Gagal memuat data: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function updatedFormDataIdPelanggan($value)
    {
        $pelanggan = $this->pelangganList->firstWhere('id', $value);
        if ($pelanggan) {
            $this->formData['nama_pelanggan'] = $pelanggan['name'];
        }
    }

    public function updatedFormDataIdLayanan($value)
    {
        $layanan = $this->layananList->firstWhere('id', $value);
        if ($layanan) {
            $this->formData['nama_layanan'] = explode(' - ', $layanan['name'])[0];
            $this->formData['harga_per_kg'] = $layanan['harga'];
            $this->calculateTotal();
        }
    }

    public function updatedFormDataBeratKg()
    {
        $this->calculateTotal();
    }

    public function updatedFormDataDiskon()
    {
        $this->calculateTotal();
    }

    protected function calculateTotal()
    {
        $berat = (float) ($this->formData['berat_kg'] ?? 0);
        $harga = (float) ($this->formData['harga_per_kg'] ?? 0);
        $diskon = (float) ($this->formData['diskon'] ?? 0);

        $this->formData['subtotal'] = $berat * $harga;
        $this->formData['total'] = $this->formData['subtotal'] - $diskon;
    }

    protected function convertToDatetimeLocal($datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        try {
            // Parse datetime dari berbagai format dan convert ke Y-m-d\TH:i untuk datetime-local input
            $parsed = \Carbon\Carbon::parse($datetime);
            return $parsed->format('Y-m-d\TH:i');
        } catch (\Exception $e) {
            return '';
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

    public function printReceipt()
    {
        // Redirect ke halaman print receipt di tab baru
        $this->dispatch('open-print-window', url: route('receipt.print', ['id' => $this->idTransaksi]));
    }

    public function save()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');

            // Normalize berat - convert koma ke titik jika ada
            $beratNormalized = str_replace(',', '.', $this->formData['berat_kg']);

            $data = [
                'ID Transaksi' => $this->formData['id_transaksi'],
                'Tanggal Masuk' => $this->formData['tanggal_masuk'],
                'ID Pelanggan' => $this->formData['id_pelanggan'],
                'Nama Pelanggan' => $this->formData['nama_pelanggan'],
                'ID Layanan' => $this->formData['id_layanan'],
                'Nama Layanan' => $this->formData['nama_layanan'],
                'Jenis Pakaian' => $this->formData['jenis_pakaian'],
                'Berat (Kg)' => "'" . $beratNormalized, // Prefix dengan ' agar Google Sheets treat sebagai text
                'Harga per Kg' => "'" . $this->formData['harga_per_kg'],
                'Subtotal' => "'" . $this->formData['subtotal'],
                'Diskon' => "'" . $this->formData['diskon'],
                'Total' => "'" . $this->formData['total'],
                'Metode Pembayaran' => $this->formData['metode_pembayaran'],
                'Tanggal Selesai' => $this->formData['tanggal_selesai'],
                'Status' => $this->formData['status'],
                'Catatan' => $this->formData['catatan'],
            ];

            $sheetdb->update('ID Transaksi', $this->formData['id_transaksi'], $data);
            $this->purgeCache();
            $this->success('Transaksi berhasil diupdate!', position: 'toast-bottom');

            return $this->redirect('/transaksi', navigate: true);
        } catch (\Exception $e) {
            $this->error('Gagal menyimpan transaksi: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.transaksi.edit');
    }
}
