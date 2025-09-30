<?php

class Localization
{
    private array $translations = [];
    private array $fallbackTranslations = [];
    private string $locale;
    private string $fallbackLocale;
    private static ?Localization $instance = null;

    private const SUPPORTED_LOCALES = [
        'en' => 'English',
        'ar' => 'العربية',
    ];

    private const RTL_LOCALES = ['ar'];

    public function __construct(?string $locale = null)
    {
        $config = $GLOBALS['config']['app'] ?? [];
        $this->fallbackLocale = $config['locale'] ?? 'en';
        $this->setLocale($locale ?? $this->fallbackLocale);
    }

    public function setLocale(?string $locale): void
    {
        $normalized = $this->normalizeLocale($locale);
        if (!array_key_exists($normalized, self::SUPPORTED_LOCALES)) {
            $normalized = $this->fallbackLocale;
        }

        $this->locale = $normalized;
        $this->translations = $this->loadTranslations($normalized);
        $this->fallbackTranslations = $this->loadTranslations($this->fallbackLocale);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    public function translate(string $key, array $replace = []): string
    {
        $value = $this->getValue($this->translations, $key);

        if ($value === null || $value === '') {
            $value = $this->getValue($this->fallbackTranslations, $key);
        }

        if ($value === null || $value === '') {
            $value = $key;
        }

        if (!empty($replace)) {
            foreach ($replace as $placeholder => $replacement) {
                $value = str_replace(':' . $placeholder, (string)$replacement, $value);
            }
        }

        return $value;
    }

    public function getDirection(): string
    {
        return in_array($this->locale, self::RTL_LOCALES, true) ? 'rtl' : 'ltr';
    }

    public function isRtl(): bool
    {
        return $this->getDirection() === 'rtl';
    }

    public function getLanguageName(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale ?? $this->locale);
        return self::SUPPORTED_LOCALES[$locale] ?? self::SUPPORTED_LOCALES[$this->fallbackLocale] ?? 'English';
    }

    public static function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    public static function setInstance(Localization $localization): void
    {
        self::$instance = $localization;
    }

    public static function getInstance(): Localization
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function translateGlobal(string $key, array $replace = []): string
    {
        return self::getInstance()->translate($key, $replace);
    }

    private function normalizeLocale(?string $locale): string
    {
        $locale = strtolower(trim((string)$locale));
        if ($locale === '') {
            return $this->fallbackLocale ?? 'en';
        }

        return preg_replace('/[^a-z_\-]/', '', $locale) ?: ($this->fallbackLocale ?? 'en');
    }

    private function loadTranslations(string $locale): array
    {
        $file = APP_PATH . '/lang/' . $locale . '.php';
        if (is_file($file)) {
            $translations = include $file;
            return is_array($translations) ? $translations : [];
        }

        return [];
    }

    private function getValue(array $translations, string $key)
    {
        if (array_key_exists($key, $translations)) {
            return $translations[$key];
        }

        $segments = explode('.', $key);
        $current = $translations;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return null;
            }

            $current = $current[$segment];
        }

        return $current;
    }
}

if (!function_exists('__')) {
    function __(string $key, array $replace = []): string
    {
        return Localization::translateGlobal($key, $replace);
    }
}
