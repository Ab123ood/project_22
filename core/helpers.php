<?php
if (!function_exists('app_locale')) {
    function app_locale(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        if (!empty($_SESSION['locale'])) {
            return (string)$_SESSION['locale'];
        }
        if (!empty($_COOKIE['locale'])) {
            return (string)$_COOKIE['locale'];
        }
        return $GLOBALS['config']['app']['locale'] ?? 'en';
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = [], ?string $locale = null): string
    {
        static $translations = [];
        $locale = $locale ?: app_locale();
        if (!isset($translations[$locale])) {
            $path = APP_PATH . '/lang/' . $locale . '.php';
            $translations[$locale] = file_exists($path) ? include $path : [];
        }

        $segments = explode('.', $key);
        $value = $translations[$locale];
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                $value = $key;
                break;
            }
        }

        if (!is_string($value)) {
            $value = $key;
        }

        foreach ($replace as $search => $replacement) {
            $value = str_replace(':' . $search, (string)$replacement, $value);
        }

        return $value;
    }
}
