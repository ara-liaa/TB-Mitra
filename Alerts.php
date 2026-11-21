<?php
/**
 * Komponen Alert & Notifikasi - Toko Bangunan Mitra (TB Mitra)
 * Memuat helper render flash message, alert error/status halaman,
 * dan banner peringatan stok menipis untuk Admin.
 */

if (!function_exists('render_alert_box')) {
    /**
     * Merender satu kotak alert Bootstrap 5 dengan ikon dan tombol tutup opsional.
     */
    function render_alert_box(
        string $message,
        string $type = 'info',
        bool $dismissible = true,
        string $extraClasses = ''
    ): string {
        if (trim($message) === '') {
            return '';
        }

        $allowedTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
        $safeType = in_array($type, $allowedTypes, true) ?$type : 'info';

        $icon = match ($safeType) {
            'success' => 'bi-check-circle-fill',
            'danger'  => 'bi-exclamation-octagon-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'primary' => 'bi-info-circle-fill',
            default   => 'bi-bell-fill',
        };

        $textClass = in_array($safeType, ['warning', 'light'], true) ? 'text-dark' : '';
        $dismissClass =$dismissible ? 'alert-dismissible fade show' : '';
        $classes = trim("alert alert-{$safeType} {$dismissClass} d-flex align-items-start gap-2 shadow-2xs {$textClass} {$extraClasses}");

        $closeBtn =$dismissible
            ? '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>'
            : '';

        return '<div class="' . htmlspecialchars($classes, ENT_QUOTES) . '" role="alert">'
            . '<i class="bi ' . htmlspecialchars($icon, ENT_QUOTES) . ' fs-5 flex-shrink-0 mt-0"></i>'
            . '<div class="small flex-grow-1">' . htmlspecialchars($message) . '</div>'
            . $closeBtn
            . '</div>';
    }
}

if (!function_exists('render_flash_alerts')) {
    /**
     * Mengambil dan merender flash message dari session jika tersedia.
     */
    function render_flash_alerts(string $extraClasses = 'mb-4'): string
    {
        if (!function_exists('get_flash')) {
            return '';
        }

        $flash = get_flash();
        if (!is_array($flash) \vert{}\vert{} empty($flash['message'])) {
            return '';
        }

        return render_alert_box(
            (string) $flash
          
