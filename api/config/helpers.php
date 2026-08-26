<?php
// api/config/helpers.php
// Funções auxiliares globais

if (!function_exists('formatDateAO')) {
    function formatDateAO(?string $date): string {
        if (!$date) return '';
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d ? $d->format('d/m/Y') : '';
    }
}

if (!function_exists('formatDateTimeAO')) {
    function formatDateTimeAO(?string $datetime): string {
        if (!$datetime) return '';
        $d = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        return $d ? $d->format('d/m/Y H:i') : '';
    }
}

if (!function_exists('formatDateBR')) {
    function formatDateBR(?string $date): string {
        return formatDateAO($date);
    }
}

if (!function_exists('formatDateTimeBR')) {
    function formatDateTimeBR(?string $datetime): string {
        return formatDateTimeAO($datetime);
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = ''): string {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('error')) {
    function error(string $key): ?string {
        return $_SESSION['errors'][$key] ?? null;
    }
}

if (!function_exists('hasError')) {
    function hasError(string $key): bool {
        return isset($_SESSION['errors'][$key]);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_meta')) {
    function csrf_meta(): string {
        return '<meta name="csrf-token" content="' . htmlspecialchars(csrf_token()) . '">';
    }
}

if (!function_exists('base_url')) {
    function base_url(): string {
        if (defined('BASE_URL')) return BASE_URL;
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $proto . '://' . $host;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        return base_url() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('money')) {
    function money(float $value): string {
        return number_format($value, 2, ',', '.') . ' Kz';
    }
}

if (!function_exists('statusBadge')) {
    function statusBadge(float $pendente, float $pago, float $total): string {
        if ($pendente <= 0 && $pago >= $total && $total > 0) {
            return '<span class="badge bg-paid"><i class="bi bi-check-circle-fill me-1"></i> Quitado</span>';
        } elseif ($pago > 0 && $pendente > 0) {
            return '<span class="badge bg-partial"><i class="bi bi-hourglass-split me-1"></i> Parcial</span>';
        } else {
            return '<span class="badge bg-pending"><i class="bi bi-exclamation-triangle-fill me-1"></i> Pendente</span>';
        }
    }
}
