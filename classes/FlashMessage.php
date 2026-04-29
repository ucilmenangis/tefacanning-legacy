<?php

/**
 * FlashMessage — one-time session messages.
 *
 * Usage:
 *   FlashMessage::set('success', 'Saved!');
 *   echo FlashMessage::render();
 */
class FlashMessage
{
    private const SESSION_KEY = 'flash';

    public static function set(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[self::SESSION_KEY] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function get(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return null;
        }
        $flash = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);
        return $flash;
    }

    public static function render(): string
    {
        $flash = self::get();
        if (!$flash) return '';

        $type = $flash['type'];
        $message = htmlspecialchars($flash['message']);

        $colors = [
            'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'error'   => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
            'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
        ];

        $icons = [
            'success' => 'ph-check-circle',
            'error'   => 'ph-x-circle',
            'warning' => 'ph-warning',
            'info'    => 'ph-info',
        ];

        $color = $colors[$type] ?? $colors['info'];
        $icon = $icons[$type] ?? $icons['info'];

        return '<div class="mb-5 rounded-xl border px-5 py-4 flex items-center gap-3 ' . $color . '">'
             . '<i class="ph-bold ' . $icon . ' text-lg flex-shrink-0"></i>'
             . '<span class="text-[13px] font-medium">' . $message . '</span>'
             . '</div>';
    }

    public static function has(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION[self::SESSION_KEY]);
    }
}
