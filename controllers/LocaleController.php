<?php

class LocaleController extends Controller
{
    public function switch(): void
    {
        $this->startSession();

        $supported = Localization::getSupportedLocales();
        $default = $GLOBALS['config']['app']['locale'] ?? array_key_first($supported);

        $requested = $this->getGet('lang', $this->getPost('lang', $default));
        if (!is_string($requested)) {
            $requested = $default;
        }

        $requested = strtolower(trim($requested));
        if (!array_key_exists($requested, $supported)) {
            $requested = $default;
        }

        $_SESSION['locale'] = $requested;

        if (!headers_sent()) {
            $cookiePath = $this->basePath() ?: '/';
            setcookie('locale', $requested, time() + (60 * 60 * 24 * 30), $cookiePath, '', !IS_DEVELOPMENT, true);
        }

        $redirect = $this->sanitizeRedirect($_SERVER['HTTP_REFERER'] ?? '') ?? ($this->basePath() . '/');

        $this->redirect($redirect);
    }

    private function sanitizeRedirect(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return null;
        }

        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (!empty($parsed['host']) && $parsed['host'] !== $host) {
            return null;
        }

        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';

        $basePath = $this->basePath();
        if ($basePath && !str_starts_with($path, $basePath)) {
            $path = $basePath . $path;
        }

        return $path . $query;
    }
}
