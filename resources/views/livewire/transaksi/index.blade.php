<div>
    <!-- HEADER -->
    <x-header title="Daftar Transaksi" separator progress-indicator>
        <x-slot:middle class="justify-end">
            <x-input placeholder="Cari transaksi..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if($search || $statusFilter || $metodePembayaranFilter || $tanggalDari || $tanggalSampai)
                <x-badge value="Filter Aktif" class="badge-warning" />
            @endif
            <x-button label="Tambah Transaksi" link="/transaksi/create" responsive icon="o-plus" class="btn-success" />
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card class="shadow-sm">
        <x-table :headers="$headers" :rows="$transaksi" :sort-by="$sortBy" striped with-pagination per-page="perPage" :per-page-values="[5, 10, 25, 50]">
            <x-slot:empty>
                <x-icon name="o-cube" label="Tidak ada data transaksi." />
            </x-slot:empty>
            @scope('cell_tanggal_masuk', $item)
            <span class="text-sm">{{ date('d/m/Y H:i', strtotime($item['tanggal_masuk'])) }}</span>
            @endscope

            @scope('cell_jenis_pakaian', $item)
            <span class="text-xs text-gray-600">{{ $item['jenis_pakaian'] ?: '-' }}</span>
            @endscope

            @scope('cell_berat_kg', $item)
            <span class="badge badge-outline badge-sm">{{ $item['berat_kg'] }} Kg</span>
            @endscope

            @scope('cell_total', $item)
            <span class="font-semibold text-success">Rp {{ number_format((float) $item['total'], 0, ',', '.') }}</span>
            @endscope

            @scope('cell_metode_pembayaran', $item)
            @php
                $badgeClass = match($item['metode_pembayaran']) {
                    'Tunai' => 'badge-success',
                    'Transfer' => 'badge-info',
                    'QRIS' => 'badge-warning',
                    'Debit' => 'badge-primary',
                    default => 'badge-neutral'
                };
            @endphp
            <x-badge value="{{ $item['metode_pembayaran'] }}" class="{{ $badgeClass }} badge-sm" />
            @endscope

            @scope('cell_status', $item)
            @php
                $statusClass = match($item['status']) {
                    'Menunggu' => 'badge-warning',
                    'Proses' => 'badge-info',
                    'Selesai' => 'badge-success',
                    'Diambil' => 'badge-primary',
                    'Batal' => 'badge-error',
                    default => 'badge-neutral'
                };
            @endphp
            <x-badge value="{{ $item['status'] }}" class="{{ $statusClass }} badge-sm" />
            @endscope

            @scope('actions', $item)
            <div class="flex items-center justify-end gap-2">
                <x-button
                    label="Edit"
                    icon="o-pencil"
                    link="/transaksi/edit/{{ $item['id_transaksi'] }}"
                    class="btn-sm btn-ghost hover:btn-info"
                />
                <x-button
                    label="Hapus"
                    icon="o-trash"
                    wire:click="confirmDelete('{{ $item['id_transaksi'] }}', '{{ $item['nama_pelanggan'] }}')"
                    class="btn-sm btn-ghost hover:btn-error"
                />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filter Transaksi" subtitle="Saring data sesuai kebutuhan" right separator with-close-button class="lg:w-1/3">
        <div class="space-y-5">
            <!-- Search -->
            <x-input
                label="Pencarian"
                placeholder="Cari ID, pelanggan, atau layanan..."
                wire:model.live.debounce="search"
                icon="o-magnifying-glass"
                hint="Pencarian otomatis saat Anda mengetik"
            />

            <!-- Status Filter -->
            <x-select
                label="Status Transaksi"
                wire:model.live="statusFilter"
                icon="o-funnel"
                :options="[
                    ['id' => '', 'name' => 'Semua Status'],
                    ['id' => 'Menunggu', 'name' => 'Menunggu'],
                    ['id' => 'Proses', 'name' => 'Proses'],
                    ['id' => 'Selesai', 'name' => 'Selesai'],
                    ['id' => 'Diambil', 'name' => 'Diambil'],
                    ['id' => 'Batal', 'name' => 'Batal']
                ]"
                option-value="id"
                option-label="name"
            />

            <!-- Metode Pembayaran Filter -->
            <x-select
                label="Metode Pembayaran"
                wire:model.live="metodePembayaranFilter"
                icon="o-credit-card"
                :options="[
                    ['id' => '', 'name' => 'Semua Metode'],
                    ['id' => 'Tunai', 'name' => 'Tunai'],
                    ['id' => 'Transfer', 'name' => 'Transfer'],
                    ['id' => 'QRIS', 'name' => 'QRIS'],
                    ['id' => 'Debit', 'name' => 'Debit']
                ]"
                option-value="id"
                option-label="name"
            />

            <!-- Tanggal Range Filter -->
            <div class="space-y-3">
                <label class="block text-sm font-semibold">Range Tanggal Masuk</label>
                <div class="grid grid-cols-2 gap-3">
                    <x-input
                        label="Dari"
                        type="date"
                        wire:model.live="tanggalDari"
                    />
                    <x-input
                        label="Sampai"
                        type="date"
                        wire:model.live="tanggalSampai"
                    />
                </div>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Reset Filter" icon="o-x-mark" wire:click="clear" spinner class="btn-outline btn-error" />
            <x-button label="Terapkan" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    <!-- DELETE CONFIRMATION MODAL -->
    <x-modal wire:model="deleteModal" title="Konfirmasi Hapus" subtitle="Apakah Anda yakin ingin menghapus data ini?" separator>
        <div class="space-y-4">
            <div class="alert alert-warning">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div>
                    <h3 class="font-bold">Perhatian!</h3>
                    <div class="text-xs">Data yang sudah dihapus tidak dapat dikembalikan.</div>
                </div>
            </div>

            <div class="p-4 bg-base-200 rounded-lg">
                <p class="text-sm"><strong>Pelanggan:</strong> {{ $deleteName }}</p>
                <p class="text-sm"><strong>ID Transaksi:</strong> {{ $deleteId }}</p>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" class="btn-ghost" />
            <x-button label="Hapus" wire:click="delete" spinner class="btn-error" icon="o-trash" />
        </x-slot:actions>
    </x-modal>
</div>
