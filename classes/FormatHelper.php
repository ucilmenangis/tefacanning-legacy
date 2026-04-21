<?php

/**
 * FormatHelper — reusable formatting utilities.
 *
 * Static methods so you call them directly: FormatHelper::rupiah(25000)
 * No need to instantiate with `new`.
 */
class FormatHelper
{
    /**
     * Format number to Rupiah string.
     * FormatHelper::rupiah(25000) → "Rp 25.000"
     */
    public static function rupiah(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format datetime to Indonesian-friendly string.
     * FormatHelper::tanggal('2026-02-15 18:34:00') → "15 Feb 2026, 18:34"
     */
    public static function tanggal(string $datetime): string
    {
        return date('d M Y, H:i', strtotime($datetime));
    }

    /**
     * Map order status to label + badge CSS class.
     * Returns ['label' => 'Menunggu', 'badge' => 'badge-amber', 'icon' => 'ph-clock']
     */
    public static function orderStatus(string $status): array
    {
        $map = [
            'pending'    => ['label' => 'Menunggu',     'badge' => 'badge-amber', 'icon' => 'ph-clock'],
            'processing' => ['label' => 'Diproses',     'badge' => 'badge-blue',  'icon' => 'ph-gear'],
            'ready'      => ['label' => 'Siap Diambil', 'badge' => 'badge-green', 'icon' => 'ph-package'],
            'picked_up'  => ['label' => 'Diambil',      'badge' => 'badge-gray',  'icon' => 'ph-check-circle'],
        ];

        return $map[$status] ?? ['label' => ucfirst($status), 'badge' => 'badge-gray', 'icon' => 'ph-question'];
    }

    /**
     * Map batch status to label + color.
     */
    public static function batchStatus(string $status): array
    {
        $map = [
            'open'       => ['label' => 'Buka',     'color' => 'emerald'],
            'processing' => ['label' => 'Diproses', 'color' => 'amber'],
            'ready'      => ['label' => 'Siap',     'color' => 'blue'],
            'closed'     => ['label' => 'Tutup',    'color' => 'gray'],
        ];

        return $map[$status] ?? ['label' => ucfirst($status), 'color' => 'gray'];
    }
}
