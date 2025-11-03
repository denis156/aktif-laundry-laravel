<div>
    <x-header title="Tambah Transaksi Baru" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Kembali" link="/transaksi" icon="o-arrow-left" class="btn-outline" />
        </x-slot:actions>
    </x-header>

    <x-card class="max-w-6xl mx-auto shadow-sm">
        <x-form wire:submit="save">
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="ID Transaksi" wire:model="formData.id_transaksi" readonly hint="ID dibuat otomatis"
                        icon="o-hashtag" />

                    <x-datetime label="Tanggal Masuk" wire:model="formData.tanggal_masuk" type="datetime-local"
                        required />

                    <!-- Pelanggan & Layanan -->
                    <x-select label="Pilih Pelanggan" wire:model.live="formData.id_pelanggan" :options="$pelangganList"
                        option-value="id" option-label="name" icon="o-user" placeholder="Pilih pelanggan..."
                        required />

                    <x-select label="Pilih Layanan" wire:model.live="formData.id_layanan" :options="$layananList"
                        option-value="id" option-label="name" icon="o-sparkles" placeholder="Pilih layanan..."
                        required />
                </div>

                <!-- Jenis Pakaian Component -->
                <livewire:components.jenis-pakaian-input :value="$formData['jenis_pakaian']" :key="'create-jenis-pakaian'" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Berat & Harga -->
                    <x-input label="Berat (Kg)" type="number" step="0.1" min="0"
                        wire:model.live="formData.berat_kg" placeholder="Contoh: 8.5" suffix="Kg"
                        hint="Gunakan titik untuk desimal (8.5)" required />

                    <x-input label="Harga per Kg" type="number" wire:model="formData.harga_per_kg" readonly
                        prefix="Rp" hint="Otomatis dari layanan" />

                    <x-input label="Subtotal" type="number" wire:model="formData.subtotal" readonly prefix="Rp"
                        hint="Otomatis dihitung" />

                    <!-- Diskon & Total -->
                    <x-input label="Diskon" type="number" wire:model.live="formData.diskon" placeholder="0"
                        prefix="Rp" hint="Opsional" />

                    <x-input label="Total Bayar" type="number" wire:model="formData.total" readonly prefix="Rp"
                        hint="Subtotal - Diskon" class="font-bold text-lg" />

                    <!-- Metode Pembayaran & Tanggal Selesai -->
                    <x-select label="Metode Pembayaran" wire:model="formData.metode_pembayaran" icon="o-credit-card"
                        :options="[
                            ['id' => 'Tunai', 'name' => 'Tunai'],
                            ['id' => 'Transfer', 'name' => 'Transfer'],
                            ['id' => 'QRIS', 'name' => 'QRIS'],
                            ['id' => 'Debit', 'name' => 'Debit'],
                        ]" option-value="id" option-label="name" required />

                    <x-input label="Tanggal Selesai" type="datetime-local" wire:model="formData.tanggal_selesai"
                        icon="o-calendar" hint="Opsional - Estimasi selesai" />

                    <!-- Status -->
                    <x-select label="Status" wire:model="formData.status" icon="o-check-circle" :options="[
                        ['id' => 'Menunggu', 'name' => 'Menunggu'],
                        ['id' => 'Proses', 'name' => 'Proses'],
                        ['id' => 'Selesai', 'name' => 'Selesai'],
                        ['id' => 'Diambil', 'name' => 'Diambil'],
                        ['id' => 'Batal', 'name' => 'Batal'],
                    ]"
                        option-value="id" option-label="name" required />

                    <!-- Catatan - Full Width -->
                    <div class="col-span-1 md:col-span-2">
                        <x-textarea label="Catatan" wire:model="formData.catatan"
                            placeholder="Catatan tambahan (opsional)..." rows="2" />
                    </div>
                </div>

                <!-- Summary Box -->
                <div class="alert alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-bold">Ringkasan Transaksi</h3>
                        <div class="text-xs">
                            <span class="font-semibold">{{ $formData['nama_pelanggan'] ?: 'Pilih pelanggan' }}</span> -
                            <span class="font-semibold">{{ $formData['nama_layanan'] ?: 'Pilih layanan' }}</span>
                            <br>
                            Berat: {{ number_format((float) ($formData['berat_kg'] ?: 0), 1, '.', '') }} Kg × Rp
                            {{ number_format((float) ($formData['harga_per_kg'] ?? 0), 0, ',', '.') }} =
                            <span class="font-bold text-primary">Rp
                                {{ number_format((float) ($formData['total'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Batal" link="/transaksi" class="btn-ghost" />
                <x-button label="Simpan" type="submit" spinner="save" class="btn-primary" icon="o-check" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
