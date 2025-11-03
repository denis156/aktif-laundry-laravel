<?php

namespace App\Livewire;

use Mary\Traits\Toast;
use Livewire\Component;
use SheetDB\SheetDB;
use Illuminate\Support\Facades\Http;

class Pengaturan extends Component
{
    use Toast;

    public array $settings = [];

    public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Setting');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            // Transform data menjadi associative array dengan key sebagai index
            $this->settings = $data->mapWithKeys(function ($item) {
                return [$item['Key'] ?? '' => [
                    'key' => $item['Key'] ?? '',
                    'value' => $item['Value'] ?? '',
                    'deskripsi' => $item['Deskripsi'] ?? '',
                ]];
            })->toArray();

        } catch (\Exception $e) {
            $this->error('Gagal memuat pengaturan: ' . $e->getMessage(), position: 'toast-bottom');
            $this->settings = [];
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
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Setting');

            // Update setiap setting
            foreach ($this->settings as $key => $setting) {
                if (!empty($key)) {
                    $data = [
                        'Key' => $setting['key'],
                        'Value' => $setting['value'],
                        'Deskripsi' => $setting['deskripsi'],
                    ];

                    $sheetdb->update('Key', $key, $data);
                }
            }

            $this->purgeCache();
            $this->success('Pengaturan berhasil disimpan!', position: 'toast-bottom');

        } catch (\Exception $e) {
            $this->error('Gagal menyimpan pengaturan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function render()
    {
        return view('livewire.pengaturan');
    }
}
