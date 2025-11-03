<?php

namespace App\Traits;

use SheetDB\SheetDB;

trait HasSettings
{
    /**
     * Get setting value from Google Sheets
     */
    protected function getSetting(string $key, $default = null)
    {
        try {
            $sheetdb = new SheetDB(config('app.api_dbsheet'), 'Setting');
            $response = $sheetdb->get();
            $data = collect(json_decode(json_encode($response), true));

            $setting = $data->firstWhere('Key', $key);

            return $setting['Value'] ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
