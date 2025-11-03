<?php

namespace App\Livewire\Components;

use Livewire\Component;
use SheetDB\SheetDB;
use App\Traits\HasDataFormatting;
use App\Traits\HasSettings;

class Receipt extends Component
{
    use HasDataFormatting, HasSettings;

    public string $id;
    public array $transaksiData = [];
    public array $setting = [];
    public string $pelangganAlamat = '';
    public string $pelangganNoHp = '';

    public function mount($id)
    {
        $this->id = $id;
        $this->loadReceiptData();
    }

    protected function loadReceiptData()
    {
        try {
            // Get Transaksi Data
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $transaksi = $data->first(function ($item) {
                return ($item['ID Transaksi'] ?? '') === $this->id;
            });

            if (!$transaksi) {
                abort(404, 'Transaksi tidak ditemukan');
            }

            // Parse data dengan helper function untuk handle format dari Google Sheets
            $this->transaksiData = [
                'id_transaksi' => $transaksi['ID Transaksi'] ?? '',
                'tanggal_masuk' => $transaksi['Tanggal Masuk'] ?? '',
                'id_pelanggan' => $transaksi['ID Pelanggan'] ?? '',
                'nama_pelanggan' => $transaksi['Nama Pelanggan'] ?? '',
                'id_layanan' => $transaksi['ID Layanan'] ?? '',
                'nama_layanan' => $transaksi['Nama Layanan'] ?? '',
                'jenis_pakaian' => $transaksi['Jenis Pakaian'] ?? '',
                'berat_kg' => $this->parseBerat($transaksi['Berat (Kg)'] ?? 0),
                'harga_per_kg' => $this->parseHarga($transaksi['Harga per Kg'] ?? 0),
                'subtotal' => $this->parseHarga($transaksi['Subtotal'] ?? 0),
                'diskon' => $this->parseHarga($transaksi['Diskon'] ?? 0),
                'total' => $this->parseHarga($transaksi['Total'] ?? 0),
                'metode_pembayaran' => $transaksi['Metode Pembayaran'] ?? '',
                'tanggal_selesai' => $transaksi['Tanggal Selesai'] ?? '',
                'status' => $transaksi['Status'] ?? '',
                'catatan' => $transaksi['Catatan'] ?? '',
            ];

            // Get Setting Data
            $this->setting = [
                'nama_toko' => $this->getSetting('nama_toko', 'Aktif Laundry'),
                'alamat' => $this->getSetting('alamat', ''),
                'telepon' => $this->getSetting('telepon', ''),
                'whatsapp' => $this->getSetting('whatsapp', ''),
                'email' => $this->getSetting('email', ''),
            ];

            // Get Pelanggan Data (Alamat & No HP)
            // Initialize with default empty values
            $this->pelangganAlamat = '';
            $this->pelangganNoHp = '';

            try {
                $sheetdbPelanggan = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
                $responsePelanggan = $sheetdbPelanggan->get();
                $dataPelanggan = collect(json_decode(json_encode($responsePelanggan), true));
                $pelanggan = $dataPelanggan->firstWhere('ID Pelanggan', $this->transaksiData['id_pelanggan']);

                if ($pelanggan) {
                    $this->pelangganAlamat = $pelanggan['Alamat'] ?? '';
                    // Try both possible key formats for No HP
                    $this->pelangganNoHp = $pelanggan['No. HP'] ?? $pelanggan['No HP'] ?? '';
                }
            } catch (\Exception $e) {
                // Silent fail - use default empty values
            }

        } catch (\Exception $e) {
            abort(500, 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('receipt-print', [
            'transaksiData' => $this->transaksiData,
            'setting' => $this->setting,
            'pelangganAlamat' => $this->pelangganAlamat,
            'pelangganNoHp' => $this->pelangganNoHp,
        ]);
    }
}
