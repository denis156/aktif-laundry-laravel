<?php

namespace App\Livewire;

use Mary\Traits\Toast;
use Livewire\Component;
use Illuminate\Support\Collection;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;
use App\Traits\HasSettings;
use App\Traits\HasDataFormatting;

class Kasir extends Component
{
    use Toast, HasSettings, HasDataFormatting;

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
    public string $lastTransactionId = '';
    public bool $showReceipt = false;

    // Toggle antara pilih pelanggan existing atau input pelanggan baru
    public bool $isPelangganBaru = false;

    // Form data untuk pelanggan baru
    public array $pelangganBaru = [
        'nama' => '',
        'no_hp' => '',
        'alamat' => '',
        'email' => '',
    ];

    // Listener untuk event dari component JenisPakaianInput
    protected $listeners = ['jenisPakaianUpdated'];

    public function jenisPakaianUpdated($value)
    {
        $this->formData['jenis_pakaian'] = $value;
    }

    public function mount()
    {
        $this->resetForm();
        $this->loadPelangganList();
        $this->loadLayananList();
    }

    protected function resetForm()
    {
        $this->formData = [
            'id_transaksi' => $this->generateId(),
            'tanggal_masuk' => date('Y-m-d\TH:i'),
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
                        'no_hp' => $item['No. HP'] ?? '',
                        'email' => $item['Email'] ?? '',
                        'alamat' => $item['Alamat'] ?? '',
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
                    $durasi = $this->parseHarga($item['Durasi (Jam)'] ?? 0);
                    return [
                        'id' => $item['ID Layanan'] ?? '',
                        'name' => $item['Nama Layanan'],
                        'harga' => $harga,
                        'durasi' => (int) $durasi,
                    ];
                });
        } catch (\Exception $e) {
            $this->layananList = collect([]);
        }
    }

    public function updatedFormDataIdPelanggan($value)
    {
        $pelanggan = $this->pelangganList->firstWhere('id', $value);
        if ($pelanggan) {
            $this->formData['nama_pelanggan'] = $pelanggan['name'];

            // Auto-fill form pelanggan dengan data yang dipilih (untuk ditampilkan di disabled fields)
            $this->pelangganBaru['nama'] = $pelanggan['name'];
            $this->pelangganBaru['no_hp'] = $pelanggan['no_hp'];
            $this->pelangganBaru['email'] = $pelanggan['email'];
            $this->pelangganBaru['alamat'] = $pelanggan['alamat'];
        }
    }

    public function updatedIsPelangganBaru($value)
    {
        if ($value) {
            // Saat toggle ke mode "Pelanggan Baru", clear form pelanggan
            $this->pelangganBaru = [
                'nama' => '',
                'no_hp' => '',
                'alamat' => '',
                'email' => '',
            ];
            // Clear juga pilihan pelanggan
            $this->formData['id_pelanggan'] = '';
            $this->formData['nama_pelanggan'] = '';
        } else {
            // Saat toggle ke mode "Pilih Pelanggan", clear form pelanggan juga
            $this->pelangganBaru = [
                'nama' => '',
                'no_hp' => '',
                'alamat' => '',
                'email' => '',
            ];
        }
    }

    public function updatedFormDataIdLayanan($value)
    {
        $layanan = $this->layananList->firstWhere('id', $value);
        if ($layanan) {
            $this->formData['nama_layanan'] = $layanan['name'];
            $this->formData['harga_per_kg'] = $layanan['harga'];

            // Auto-fill tanggal selesai berdasarkan durasi layanan
            $this->calculateTanggalSelesai($layanan['durasi']);

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

    protected function calculateTanggalSelesai($durasiJam)
    {
        if (!empty($this->formData['tanggal_masuk']) && $durasiJam > 0) {
            try {
                $tanggalMasuk = \Carbon\Carbon::parse($this->formData['tanggal_masuk']);
                $tanggalSelesai = $tanggalMasuk->addHours($durasiJam);
                $this->formData['tanggal_selesai'] = $tanggalSelesai->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
                $this->formData['tanggal_selesai'] = '';
            }
        }
    }

    protected function generateId(): string
    {
        $prefix = $this->getSetting('format_id_transaksi', 'TRX');
        $prefixLength = strlen($prefix);

        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Transaksi');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $maxId = $data->map(function ($item) use ($prefix, $prefixLength) {
                $id = $item['ID Transaksi'] ?? $prefix . '000';
                return (int) substr($id, $prefixLength);
            })->max();

            $newIdNumber = $maxId + 1;
            return $prefix . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            return $prefix . '001';
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
        // Jika mode pelanggan baru, simpan pelanggan dulu
        if ($this->isPelangganBaru) {
            // Validasi pelanggan baru
            if (empty($this->pelangganBaru['nama'])) {
                $this->error('Nama pelanggan wajib diisi!', position: 'toast-bottom');
                return;
            }

            if (empty($this->pelangganBaru['no_hp'])) {
                $this->error('Nomor HP wajib diisi!', position: 'toast-bottom');
                return;
            }

            // Simpan pelanggan baru terlebih dahulu
            $this->savePelangganBaru();

            // Jika gagal simpan pelanggan, stop proses transaksi
            if ($this->isPelangganBaru) {
                return; // Masih dalam mode pelanggan baru = gagal simpan
            }
        }

        // Validasi transaksi
        if (empty($this->formData['id_pelanggan'])) {
            $this->error('Pilih pelanggan terlebih dahulu!', position: 'toast-bottom');
            return;
        }

        if (empty($this->formData['id_layanan'])) {
            $this->error('Pilih layanan terlebih dahulu!', position: 'toast-bottom');
            return;
        }

        if (empty($this->formData['berat_kg']) || $this->formData['berat_kg'] < 0.5) {
            $this->error('Berat minimal 0.5 kg!', position: 'toast-bottom');
            return;
        }

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

            $sheetdb->create($data);
            $this->purgeCache();

            // Save last transaction ID untuk print struk
            $this->lastTransactionId = $this->formData['id_transaksi'];

            $this->success('Transaksi berhasil disimpan!', position: 'toast-bottom');

            // Reset form untuk transaksi baru
            $this->resetForm();

            // Show receipt option
            $this->showReceipt = true;

        } catch (\Exception $e) {
            $this->error('Gagal menyimpan transaksi: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function printReceipt($transactionId = null)
    {
        $id = $transactionId ?? $this->lastTransactionId;
        if (!empty($id)) {
            $this->dispatch('open-print-window', url: route('receipt.print', ['id' => $id]));
            $this->showReceipt = false;
        }
    }

    public function batalTransaksi()
    {
        $this->resetForm();
        $this->success('Transaksi dibatalkan', position: 'toast-bottom');
    }

    public function savePelangganBaru()
    {
        // Validasi sederhana
        if (empty($this->pelangganBaru['nama'])) {
            $this->error('Nama pelanggan wajib diisi!', position: 'toast-bottom');
            return;
        }

        if (empty($this->pelangganBaru['no_hp'])) {
            $this->error('Nomor HP wajib diisi!', position: 'toast-bottom');
            return;
        }

        try {
            // Generate ID pelanggan baru
            $prefix = $this->getSetting('format_id_pelanggan', 'PLG');
            $prefixLength = strlen($prefix);

            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Pelanggan');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $maxId = $data->map(function ($item) use ($prefix, $prefixLength) {
                $id = $item['ID Pelanggan'] ?? $prefix . '000';
                return (int) substr($id, $prefixLength);
            })->max();

            $newIdNumber = $maxId + 1;
            $newIdPelanggan = $prefix . str_pad($newIdNumber, 3, '0', STR_PAD_LEFT);

            // Simpan pelanggan baru
            $dataPelanggan = [
                'ID Pelanggan' => $newIdPelanggan,
                'Nama' => $this->pelangganBaru['nama'],
                'No. HP' => $this->pelangganBaru['no_hp'],
                'Alamat' => $this->pelangganBaru['alamat'],
                'Email' => $this->pelangganBaru['email'],
                'Tanggal Daftar' => date('Y-m-d'),
                'Total Transaksi' => '0',
                'Status' => 'Aktif',
            ];

            $sheetdb->create($dataPelanggan);
            $this->purgeCache();

            $this->success("Pelanggan {$this->pelangganBaru['nama']} berhasil ditambahkan!", position: 'toast-bottom');

            // Reload list pelanggan
            $this->loadPelangganList();

            // Auto-select pelanggan yang baru ditambahkan
            $this->formData['id_pelanggan'] = $newIdPelanggan;
            $this->formData['nama_pelanggan'] = $this->pelangganBaru['nama'];

            // Reset form pelanggan baru
            $this->pelangganBaru = [
                'nama' => '',
                'no_hp' => '',
                'alamat' => '',
                'email' => '',
            ];

            // Switch kembali ke mode pilih pelanggan existing
            $this->isPelangganBaru = false;

        } catch (\Exception $e) {
            $this->error('Gagal menyimpan pelanggan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.kasir');
    }
}
