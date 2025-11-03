<?php

use App\Livewire\Kasir;
use App\Livewire\Login;
use App\Livewire\Welcome;
use App\Livewire\Pengaturan;
use App\Livewire\Components\Receipt;
use Illuminate\Support\Facades\Route;
use App\Livewire\Layanan\Edit as LayananEdit;
use App\Livewire\Layanan\Index as LayananIndex;
use App\Livewire\Layanan\Create as LayananCreate;
use App\Livewire\Pelanggan\Edit as PelangganEdit;
use App\Livewire\Transaksi\Edit as TransaksiEdit;
use App\Livewire\Pelanggan\Index as PelangganIndex;
use App\Livewire\Transaksi\Index as TransaksiIndex;
use App\Livewire\Pelanggan\Create as PelangganCreate;
use App\Livewire\Transaksi\Create as TransaksiCreate;

// Login Route
Route::get('/login', Login::class)->name('login');

// Logout Route
Route::get('/logout', function() {
    session()->flush();
    return redirect('/login');
})->name('logout');

// Protected Routes - Require Authentication
Route::middleware('auth.custom')->group(function () {
    Route::get('/', Welcome::class)->name('welcome');

    // Layanan Routes
    Route::get('/layanan', LayananIndex::class)->name('layanan');
    Route::get('/layanan/create', LayananCreate::class)->name('layanan.create');
    Route::get('/layanan/edit/{id}', LayananEdit::class)->name('layanan.edit');

    // Jenis Pakaian Routes
    Route::get('/jenis-pakaian', \App\Livewire\JenisPakaian\Index::class)->name('jenis-pakaian');
    Route::get('/jenis-pakaian/create', \App\Livewire\JenisPakaian\Create::class)->name('jenis-pakaian.create');
    Route::get('/jenis-pakaian/edit/{id}', \App\Livewire\JenisPakaian\Edit::class)->name('jenis-pakaian.edit');

    // Pelanggan Routes
    Route::get('/pelanggan', PelangganIndex::class)->name('pelanggan');
    Route::get('/pelanggan/create', PelangganCreate::class)->name('pelanggan.create');
    Route::get('/pelanggan/edit/{id}', PelangganEdit::class)->name('pelanggan.edit');

    // Transaksi Routes
    Route::get('/transaksi', TransaksiIndex::class)->name('transaksi');
    Route::get('/transaksi/create', TransaksiCreate::class)->name('transaksi.create');
    Route::get('/transaksi/edit/{id}', TransaksiEdit::class)->name('transaksi.edit');

    // Kasir
    Route::get('/kasir', Kasir::class)->name('kasir');

    // Pengaturan
    Route::get('/pengaturan', Pengaturan::class)->name('pengaturan');

    // Receipt Print
    Route::get('/receipt/print/{id}', function($id) {
        $receipt = new Receipt();
        $receipt->mount($id);

        // Format nomor HP dengan prefix 0 jika belum ada
        $noHp = $receipt->pelangganNoHp;
        if (!empty($noHp) && !str_starts_with($noHp, '0')) {
            $noHp = '0' . $noHp;
        }

        return view('receipt-print', [
            'transaksiData' => $receipt->transaksiData,
            'setting' => $receipt->setting,
            'pelangganAlamat' => $receipt->pelangganAlamat,
            'pelangganNoHp' => $noHp,
        ]);
    })->name('receipt.print');
});
