<div>
    <x-header title="Tambah Pelanggan Baru" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Kembali" link="/pelanggan" icon="o-arrow-left" class="btn-outline" />
        </x-slot:actions>
    </x-header>

    <x-card class="max-w-4xl mx-auto shadow-sm">
        <x-form wire:submit="save">
            <div class="space-y-5">
                <!-- ID & Nama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="ID Pelanggan"
                        wire:model="formData.id_pelanggan"
                        placeholder="Auto Generate"
                        readonly
                        hint="ID dibuat otomatis"
                        icon="o-hashtag"
                    />

                    <x-input
                        label="Nama Lengkap"
                        wire:model="formData.nama"
                        placeholder="Contoh: Budi Santoso"
                        icon="o-user"
                        required
                    />
                </div>

                <!-- No HP & Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="No. HP"
                        wire:model="formData.no_hp"
                        placeholder="Contoh: 81234567890"
                        hint="Format: 8xxx tanpa 0 di depan"
                        icon="o-phone"
                        required
                    />

                    <x-input
                        label="Email"
                        type="email"
                        wire:model="formData.email"
                        placeholder="Contoh: budi@email.com"
                        icon="o-envelope"
                    />
                </div>

                <!-- Alamat -->
                <x-textarea
                    label="Alamat"
                    wire:model="formData.alamat"
                    placeholder="Alamat lengkap pelanggan..."
                    rows="2"
                    required
                />

                <!-- Tanggal Daftar & Total Transaksi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="Tanggal Daftar"
                        type="date"
                        wire:model="formData.tanggal_daftar"
                        icon="o-calendar"
                        required
                    />

                    <x-input
                        label="Total Transaksi"
                        type="number"
                        wire:model="formData.total_transaksi"
                        placeholder="0"
                        readonly
                        hint="Otomatis dari sistem"
                    />
                </div>

                <!-- Status -->
                <x-select
                    label="Status"
                    wire:model="formData.status"
                    icon="o-check-circle"
                    :options="[
                        ['id' => 'Aktif', 'name' => 'Aktif'],
                        ['id' => 'Tidak Aktif', 'name' => 'Tidak Aktif']
                    ]"
                    option-value="id"
                    option-label="name"
                    required
                />
            </div>

            <x-slot:actions>
                <x-button label="Batal" link="/pelanggan" class="btn-ghost" />
                <x-button label="Simpan" type="submit" spinner="save" class="btn-primary" icon="o-check" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
