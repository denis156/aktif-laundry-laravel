<?php

namespace App\Traits;

trait HasDataFormatting
{
    /**
     * Parse harga dari format "Rp X.XXX" ke float
     * HANYA UNTUK HARGA - tidak untuk berat!
     */
    protected function parseHarga($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Hapus "Rp", spasi, dan titik pemisah ribuan
        $cleaned = str_replace(['Rp ', 'Rp', '.', ' '], '', $value);

        // Ganti koma dengan titik untuk desimal
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    /**
     * Parse berat dari berbagai format ke float
     * Handle: "2.8", "2,8", "28", dll
     * KHUSUS untuk berat yang menggunakan titik sebagai desimal
     */
    protected function parseBerat($value): float
    {
        if (empty($value)) {
            return 0;
        }

        // Convert to string jika number
        $value = (string) $value;

        // Hapus spasi
        $cleaned = str_replace(' ', '', $value);

        // Jika ada koma, anggap sebagai desimal (ganti ke titik)
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    /**
     * Parse tanggal dari berbagai format ke Y-m-d atau Y-m-d H:i
     */
    protected function parseDate($date, $includeTime = false): string
    {
        if (empty($date)) {
            return '';
        }

        // Jika sudah dalam format Y-m-d H:i (datetime)
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}/', $date)) {
            return $includeTime ? $date : substr($date, 0, 10);
        }

        // Jika sudah dalam format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Parse dari format d/m/Y H:i (dari display)
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{2}):(\d{2})/', $date, $matches)) {
            $formatted = sprintf('%04d-%02d-%02d %02d:%02d', $matches[3], $matches[2], $matches[1], $matches[4], $matches[5]);
            return $includeTime ? $formatted : substr($formatted, 0, 10);
        }

        // Parse dari format d/m/Y (dari Google Sheets)
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $date, $matches)) {
            return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
        }

        // Try to parse date dengan strtotime
        try {
            $timestamp = strtotime($date);
            if ($timestamp !== false) {
                return $includeTime ? date('Y-m-d H:i', $timestamp) : date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return $date; // Return as is jika tidak bisa parse
    }

    /**
     * Parse datetime dari berbagai format ke Y-m-d H:i
     */
    protected function parseDateTime($datetime): string
    {
        return $this->parseDate($datetime, true);
    }

    /**
     * Format harga untuk display (dengan Rp dan pemisah ribuan)
     * Note: Ini untuk display di view, jangan simpan hasil ini ke database
     */
    protected function formatHarga($number): string
    {
        return 'Rp ' . number_format((float) $number, 0, ',', '.');
    }

    /**
     * Format tanggal untuk display
     */
    protected function formatDate($date, $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Format datetime untuk display (dengan waktu)
     */
    protected function formatDateTime($datetime, $format = 'd/m/Y H:i'): string
    {
        return $this->formatDate($datetime, $format);
    }
}
