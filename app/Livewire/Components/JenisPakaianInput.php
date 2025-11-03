<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Collection;
use SheetDB\SheetDB;

class JenisPakaianInput extends Component
{
    public array $items = [];
    public Collection $jenisPakaianOptions;
    public string $outputString = '';

    public function mount($value = '')
    {
        $this->loadJenisPakaianOptions();

        // Parse initial value jika ada (format: "Kemeja (3), Celana (2)")
        if (!empty($value)) {
            $this->parseInitialValue($value);
        } else {
            // Default: 1 baris kosong
            $this->addRow();
        }
    }

    protected function loadJenisPakaianOptions()
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'JenisPakaian');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $this->jenisPakaianOptions = $data
                ->where('Status', 'Aktif')
                ->map(function ($item) {
                    return [
                        'id' => $item['ID Jenis'] ?? '',
                        'name' => $item['Nama Jenis'] ?? '',
                    ];
                });
        } catch (\Exception $e) {
            $this->jenisPakaianOptions = collect([]);
        }
    }

    protected function parseInitialValue($value)
    {
        // Parse "Kemeja (3), Celana (2)" menjadi array
        $items = explode(',', $value);

        foreach ($items as $item) {
            $item = trim($item);

            // Extract nama dan jumlah dengan regex
            if (preg_match('/^(.+?)\s*\((\d+)\)$/', $item, $matches)) {
                $nama = trim($matches[1]);
                $jumlah = (int) $matches[2];

                // Cari ID jenis dari nama
                $jenis = $this->jenisPakaianOptions->firstWhere('name', $nama);

                $this->items[] = [
                    'jenis_id' => $jenis['id'] ?? '',
                    'nama' => $nama,
                    'jumlah' => $jumlah,
                ];
            }
        }

        // Jika parsing gagal atau kosong, tambah 1 baris
        if (empty($this->items)) {
            $this->addRow();
        }
    }

    public function addRow()
    {
        $this->items[] = [
            'jenis_id' => '',
            'nama' => '',
            'jumlah' => 1,
        ];
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
        $this->updateOutput();
    }

    public function updatedItems($value, $key)
    {
        // When jenis_id changes, update nama
        if (str_contains($key, 'jenis_id')) {
            $index = (int) explode('.', $key)[0];
            $jenisId = $this->items[$index]['jenis_id'] ?? '';

            $jenis = $this->jenisPakaianOptions->firstWhere('id', $jenisId);
            if ($jenis) {
                $this->items[$index]['nama'] = $jenis['name'];
            }
        }

        $this->updateOutput();
    }

    protected function updateOutput()
    {
        // Filter items yang sudah terisi
        $validItems = array_filter($this->items, function ($item) {
            return !empty($item['jenis_id']) && !empty($item['nama']) && $item['jumlah'] > 0;
        });

        // Format: "Kemeja (3), Celana (2)"
        $formatted = array_map(function ($item) {
            return $item['nama'] . ' (' . $item['jumlah'] . ')';
        }, $validItems);

        $this->outputString = implode(', ', $formatted);

        // Emit event ke parent component
        $this->dispatch('jenisPakaianUpdated', $this->outputString);
    }

    public function render()
    {
        return view('livewire.components.jenis-pakaian-input');
    }
}
