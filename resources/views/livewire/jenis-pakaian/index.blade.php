<div>
    <!-- HEADER -->
    <x-header title="Daftar Jenis Pakaian" separator progress-indicator>
        <x-slot:middle class="justify-end">
            <x-input placeholder="Cari jenis pakaian..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if($search || $statusFilter)
                <x-badge value="Filter Aktif" class="badge-warning" />
            @endif
            <x-button label="Tambah Jenis" link="/jenis-pakaian/create" responsive icon="o-plus" class="btn-success" />
            <x-button label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card class="shadow-sm">
        <x-table :headers="$headers" :rows="$jenisPakaian" :sort-by="$sortBy" striped with-pagination per-page="perPage" :per-page-values="[5, 10, 25, 50]">
            <x-slot:empty>
                <x-icon name="o-cube" label="Tidak ada data jenis pakaian." />
            </x-slot:empty>

            @scope('cell_status', $item)
            @if($item['status'] == 'Aktif' || $item['status'] == 'aktif')
                <x-badge value="{{ $item['status'] }}" class="badge-success badge-sm" />
            @else
                <x-badge value="{{ $item['status'] }}" class="badge-error badge-sm truncate max-w-24" />
            @endif
            @endscope

            @scope('actions', $item)
            <div class="flex items-center justify-end gap-2">
                <x-button
                    label="Edit"
                    icon="o-pencil"
                    link="/jenis-pakaian/edit/{{ $item['id_jenis'] }}"
                    class="btn-sm btn-ghost hover:btn-info"
                />
                <x-button
                    label="Hapus"
                    icon="o-trash"
                    wire:click="confirmDelete('{{ $item['id_jenis'] }}', '{{ $item['nama_jenis'] }}')"
                    class="btn-sm btn-ghost hover:btn-error"
                />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filter Jenis Pakaian" subtitle="Saring data sesuai kebutuhan" right separator with-close-button class="lg:w-1/3">
        <div class="space-y-5">
            <!-- Search -->
            <x-input
                label="Pencarian"
                placeholder="Cari nama, ID, atau keterangan..."
                wire:model.live.debounce="search"
                icon="o-magnifying-glass"
                hint="Pencarian otomatis saat Anda mengetik"
            />

            <!-- Status Filter -->
            <x-select
                label="Status Jenis Pakaian"
                wire:model.live="statusFilter"
                icon="o-funnel"
                :options="[
                    ['id' => '', 'name' => 'Semua Status'],
                    ['id' => 'Aktif', 'name' => 'Aktif'],
                    ['id' => 'Tidak Aktif', 'name' => 'Tidak Aktif']
                ]"
                option-value="id"
                option-label="name"
            />
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
                <p class="text-sm"><strong>Jenis Pakaian:</strong> {{ $deleteName }}</p>
                <p class="text-sm"><strong>ID:</strong> {{ $deleteId }}</p>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.deleteModal = false" class="btn-ghost" />
            <x-button label="Hapus" wire:click="delete" spinner class="btn-error" icon="o-trash" />
        </x-slot:actions>
    </x-modal>
</div>
