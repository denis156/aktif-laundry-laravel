<div>
    <!-- HERO SECTION -->
    <div class="hero bg-gradient-to-br from-primary/10 via-base-100 to-accent/10 rounded-2xl shadow-lg mb-8">
        <div class="hero-content text-center py-16">
            <div class="max-w-3xl">
                <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-primary to-accent bg-clip-text text-transparent">
                    Selamat Datang di Aktif Laundry
                </h1>
                <p class="text-lg mb-8 text-base-content/70">
                    Sistem manajemen laundry modern yang memudahkan pengelolaan transaksi, pelanggan, dan layanan Anda secara real-time
                </p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <x-button label="Mulai Transaksi" link="/kasir" icon="o-calculator" class="btn-primary btn-lg" />
                    <x-button label="Lihat Transaksi" link="/transaksi" icon="o-queue-list" class="btn-outline btn-lg" />
                </div>
            </div>
        </div>
    </div>

    <!-- FITUR UNGGULAN -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold mb-6 text-center">Fitur Unggulan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Kasir -->
            <x-card class="shadow-md hover:shadow-xl transition-shadow cursor-pointer" link="/kasir">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center">
                        <x-icon name="o-calculator" class="w-8 h-8 text-primary" />
                    </div>
                    <h3 class="font-bold text-lg mb-2">Point of Sale (Kasir)</h3>
                    <p class="text-sm text-base-content/70">
                        Catat transaksi dengan cepat, tambah pelanggan baru, dan cetak struk otomatis
                    </p>
                </div>
            </x-card>

            <!-- Transaksi -->
            <x-card class="shadow-md hover:shadow-xl transition-shadow cursor-pointer" link="/transaksi">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-success/10 rounded-full flex items-center justify-center">
                        <x-icon name="o-queue-list" class="w-8 h-8 text-success" />
                    </div>
                    <h3 class="font-bold text-lg mb-2">Kelola Transaksi</h3>
                    <p class="text-sm text-base-content/70">
                        Monitor semua transaksi, update status, dan filter berdasarkan tanggal atau status
                    </p>
                </div>
            </x-card>

            <!-- Pelanggan -->
            <x-card class="shadow-md hover:shadow-xl transition-shadow cursor-pointer" link="/pelanggan">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-info/10 rounded-full flex items-center justify-center">
                        <x-icon name="o-user-group" class="w-8 h-8 text-info" />
                    </div>
                    <h3 class="font-bold text-lg mb-2">Database Pelanggan</h3>
                    <p class="text-sm text-base-content/70">
                        Kelola data pelanggan, riwayat transaksi, dan kontak WhatsApp langsung
                    </p>
                </div>
            </x-card>

            <!-- Layanan -->
            <x-card class="shadow-md hover:shadow-xl transition-shadow cursor-pointer" link="/layanan">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-warning/10 rounded-full flex items-center justify-center">
                        <x-icon name="o-sparkles" class="w-8 h-8 text-warning" />
                    </div>
                    <h3 class="font-bold text-lg mb-2">Paket Layanan</h3>
                    <p class="text-sm text-base-content/70">
                        Atur berbagai paket layanan dengan harga dan durasi yang fleksibel
                    </p>
                </div>
            </x-card>
        </div>
    </div>

    <!-- KEUNGGULAN SISTEM -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold mb-6 text-center">Kenapa Menggunakan Sistem Ini?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-bolt" class="w-6 h-6 text-primary" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Mudah & Cepat</h4>
                        <p class="text-sm text-base-content/70">
                            Interface yang intuitif membuat proses transaksi menjadi sangat cepat dan efisien
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-cloud" class="w-6 h-6 text-success" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Real-time Cloud</h4>
                        <p class="text-sm text-base-content/70">
                            Data tersimpan di cloud dengan Google Sheets, bisa diakses kapan saja dari mana saja
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-info/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-chart-bar" class="w-6 h-6 text-info" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Laporan Lengkap</h4>
                        <p class="text-sm text-base-content/70">
                            Filter dan sorting yang powerful untuk analisa bisnis yang lebih baik
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-printer" class="w-6 h-6 text-warning" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Cetak Struk</h4>
                        <p class="text-sm text-base-content/70">
                            Cetak struk transaksi otomatis dengan design profesional untuk pelanggan
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-device-phone-mobile" class="w-6 h-6 text-accent" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Responsive Design</h4>
                        <p class="text-sm text-base-content/70">
                            Tampilan optimal di desktop, tablet, maupun smartphone untuk kemudahan akses
                        </p>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-error/10 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-icon name="o-shield-check" class="w-6 h-6 text-error" />
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Aman & Terpercaya</h4>
                        <p class="text-sm text-base-content/70">
                            Data pelanggan dan transaksi tersimpan aman dengan backup otomatis
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- QUICK START -->
    <x-card class="shadow-lg bg-gradient-to-br from-primary/5 to-accent/5">
        <div class="text-center py-8">
            <h2 class="text-2xl font-bold mb-4">Siap Memulai?</h2>
            <p class="text-base-content/70 mb-6 max-w-2xl mx-auto">
                Mulai kelola bisnis laundry Anda dengan lebih efisien. Buat transaksi pertama Anda sekarang!
            </p>
            <div class="flex gap-4 justify-center flex-wrap">
                <x-button label="Buka Kasir" link="/kasir" icon="o-calculator" class="btn-primary btn-lg" />
                <x-button label="Kelola Pelanggan" link="/pelanggan" icon="o-user-group" class="btn-outline btn-lg" />
                <x-button label="Atur Layanan" link="/layanan" icon="o-sparkles" class="btn-outline btn-lg" />
            </div>
        </div>
    </x-card>

    <!-- FOOTER INFO -->
    <div class="mt-8 text-center text-sm text-base-content/50">
        <p>Aktif Laundry Management System - Built with Laravel, Livewire & MaryUI</p>
        <p class="mt-1">Powered by SheetDB for real-time cloud data storage</p>
        <p class="mt-2 font-semibold text-base-content/70">Developed by Denis Djodian Ardika</p>
    </div>
</div>
