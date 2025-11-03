<div>
    <x-header title="Pengaturan Sistem" subtitle="Kelola informasi toko dan konfigurasi sistem" separator progress-indicator>
    </x-header>

    <x-form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Informasi Toko -->
            <x-card title="Informasi Toko" shadow separator>
                <div class="space-y-4">
                    @if(isset($settings['nama_toko']))
                    <x-input
                        label="{{ $settings['nama_toko']['deskripsi'] }}"
                        wire:model="settings.nama_toko.value"
                        placeholder="Nama toko"
                        icon="o-building-storefront"
                    />
                    @endif

                    @if(isset($settings['alamat']))
                    <x-textarea
                        label="{{ $settings['alamat']['deskripsi'] }}"
                        wire:model="settings.alamat.value"
                        placeholder="Alamat lengkap"
                        rows="2"
                    />
                    @endif

                    @if(isset($settings['telepon']))
                    <x-input
                        label="{{ $settings['telepon']['deskripsi'] }}"
                        wire:model="settings.telepon.value"
                        placeholder="021-12345678"
                        icon="o-phone"
                    />
                    @endif

                    @if(isset($settings['whatsapp']))
                    <x-input
                        label="{{ $settings['whatsapp']['deskripsi'] }}"
                        wire:model="settings.whatsapp.value"
                        placeholder="81234567890"
                        icon="o-chat-bubble-left-right"
                        hint="Format: 8xxx tanpa 0 di depan"
                    />
                    @endif

                    @if(isset($settings['email']))
                    <x-input
                        label="{{ $settings['email']['deskripsi'] }}"
                        type="email"
                        wire:model="settings.email.value"
                        placeholder="info@toko.com"
                        icon="o-envelope"
                    />
                    @endif
                </div>
            </x-card>

            <!-- Format ID & Operasional -->
            <x-card title="Format ID & Operasional" shadow separator>
                <div class="space-y-4">
                    @if(isset($settings['format_id_transaksi']))
                    <x-input
                        label="{{ $settings['format_id_transaksi']['deskripsi'] }}"
                        wire:model="settings.format_id_transaksi.value"
                        placeholder="TRX"
                        icon="o-hashtag"
                        hint="Contoh: TRX → TRX001, TRX002"
                    />
                    @endif

                    @if(isset($settings['format_id_pelanggan']))
                    <x-input
                        label="{{ $settings['format_id_pelanggan']['deskripsi'] }}"
                        wire:model="settings.format_id_pelanggan.value"
                        placeholder="PLG"
                        icon="o-hashtag"
                        hint="Contoh: PLG → PLG001, PLG002"
                    />
                    @endif

                    @if(isset($settings['format_id_layanan']))
                    <x-input
                        label="{{ $settings['format_id_layanan']['deskripsi'] }}"
                        wire:model="settings.format_id_layanan.value"
                        placeholder="LYN"
                        icon="o-hashtag"
                        hint="Contoh: LYN → LYN001, LYN002"
                    />
                    @endif

                    @if(isset($settings['format_id_jenis_pakaian']))
                    <x-input
                        label="{{ $settings['format_id_jenis_pakaian']['deskripsi'] }}"
                        wire:model="settings.format_id_jenis_pakaian.value"
                        placeholder="JNS"
                        icon="o-hashtag"
                        hint="Contoh: JNS → JNS001, JNS002"
                    />
                    @endif

                    @if(isset($settings['jam_buka']))
                    <x-input
                        label="{{ $settings['jam_buka']['deskripsi'] }}"
                        type="time"
                        wire:model="settings.jam_buka.value"
                        icon="o-clock"
                    />
                    @endif

                    @if(isset($settings['jam_tutup']))
                    <x-input
                        label="{{ $settings['jam_tutup']['deskripsi'] }}"
                        type="time"
                        wire:model="settings.jam_tutup.value"
                        icon="o-clock"
                    />
                    @endif
                </div>
            </x-card>
        </div>

        <x-slot:actions>
            <x-button label="Reset" wire:click="loadSettings" class="btn-ghost" icon="o-arrow-path" />
            <x-button label="Simpan Pengaturan" type="submit" spinner="save" class="btn-primary" icon="o-check" />
        </x-slot:actions>
    </x-form>

    <!-- Info Box -->
    <div class="mt-6">
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <h3 class="font-bold">Catatan Penting</h3>
                <div class="text-xs">
                    <ul class="list-disc list-inside mt-2">
                        <li>Format ID akan digunakan untuk generate ID otomatis</li>
                        <li>Perubahan format ID tidak akan mempengaruhi data yang sudah ada</li>
                        <li>Data disimpan di Google Sheets pada tab "Setting"</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
