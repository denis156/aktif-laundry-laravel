<div>
    <x-header title="Tambah Jenis Pakaian Baru" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Kembali" link="/jenis-pakaian" icon="o-arrow-left" class="btn-outline" />
        </x-slot:actions>
    </x-header>

    <x-card class="max-w-4xl mx-auto shadow-sm">
        <x-form wire:submit="save">
            <div class="space-y-5">
                <!-- ID & Nama Jenis -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input
                        label="ID Jenis"
                        wire:model="formData.id_jenis"
                        placeholder="Auto Generate"
                        readonly
                        hint="ID dibuat otomatis"
                        icon="o-hashtag"
                    />

                    <x-input
                        label="Nama Jenis"
                        wire:model="formData.nama_jenis"
                        placeholder="Contoh: Kemeja"
                        icon="o-tag"
                        required
                    />
                </div>

                <!-- Keterangan -->
                <x-textarea
                    label="Keterangan"
                    wire:model="formData.keterangan"
                    placeholder="Jelaskan detail jenis pakaian ini..."
                    rows="3"
                    hint="Opsional, maksimal 200 karakter"
                />

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
                <x-button label="Batal" link="/jenis-pakaian" class="btn-ghost" />
                <x-button label="Simpan" type="submit" spinner="save" class="btn-primary" icon="o-check" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
