<?php
// app/views/auth.php

$pageTitle = __('auth.meta.title');
$metaDescription = __('auth.meta.description');
$headerContext = 'public';

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($basePath === '/' || $basePath === '\\') { $basePath = ''; }

$flashData = is_array($flash ?? null) ? $flash : [];
$formatFlash = static function ($entry) {
    if (is_array($entry)) {
        $key = $entry['key'] ?? '';
        $replace = $entry['replace'] ?? [];
        if ($key !== '') {
            return __($key, $replace);
        }
    }

    return is_string($entry) ? $entry : '';
};

$errorMessage = $formatFlash($flashData['error'] ?? null);
$successMessage = $formatFlash($flashData['success'] ?? null);

$isRtlLayout = ($lang['dir'] ?? (($isRtl ?? false) ? 'rtl' : 'ltr')) === 'rtl';
$iconMarginClass = $isRtlLayout ? 'ml-3' : 'mr-3';
$iconMarginTightClass = $isRtlLayout ? 'ml-2' : 'mr-2';
?>

<section class="relative bg-gradient-to-br from-primary to-secondary min-h-[70vh] py-16">
    <div class="absolute inset-0 opacity-20 mix-blend-overlay"
         style="background-image: url('https://readdy.ai/api/search-image?query=cybersecurity%20network%20shield%20pattern%20background&orientation=landscape&width=1600&height=900&seq=authhero');"></div>
    <div class="relative container mx-auto px-4">
        <div class="grid gap-10 lg:grid-cols-[1.1fr,1fr] items-stretch">
            <div class="bg-white/10 border border-white/20 rounded-3xl p-10 text-white backdrop-blur shadow-xl flex flex-col justify-between">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mb-8">
                        <i class="ri-shield-keyhole-line text-3xl"></i>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-5 leading-snug">
                        <?= htmlspecialchars(__('auth.hero.title')) ?>
                    </h1>
                    <p class="text-white/85 text-base md:text-lg leading-relaxed">
                        <?= htmlspecialchars(__('auth.hero.subtitle')) ?>
                    </p>
                </div>

                <ul class="space-y-4 mt-10">
                    <?php
                    $features = [
                        ['icon' => 'ri-shield-check-line', 'text' => __('auth.hero.features.awareness')],
                        ['icon' => 'ri-calendar-event-line', 'text' => __('auth.hero.features.campaigns')],
                        ['icon' => 'ri-line-chart-line', 'text' => __('auth.hero.features.progress')],
                    ];
                    foreach ($features as $feature):
                    ?>
                        <li class="flex items-start">
                            <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white <?= $iconMarginClass ?>">
                                <i class="<?= htmlspecialchars($feature['icon']) ?> text-lg"></i>
                            </span>
                            <span class="text-sm md:text-base leading-relaxed flex-1">
                                <?= htmlspecialchars($feature['text']) ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="px-8 py-10">
                    <div class="flex border-b border-gray-200 mb-8" role="tablist">
                        <button id="loginTab" type="button"
                                class="py-3 px-6 font-medium border-b-2 border-primary text-primary"
                                aria-controls="loginForm" aria-selected="true" role="tab">
                            <?= htmlspecialchars(__('auth.tabs.login')) ?>
                        </button>
                        <button id="registerTab" type="button"
                                class="py-3 px-6 font-medium text-gray-500 border-b-2 border-transparent"
                                aria-controls="registerForm" aria-selected="false" role="tab">
                            <?= htmlspecialchars(__('auth.tabs.register')) ?>
                        </button>
                    </div>

                    <?php if ($errorMessage): ?>
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                            <?= htmlspecialchars($errorMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($successMessage): ?>
                        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 px-4 py-3 text-sm">
                            <?= htmlspecialchars($successMessage) ?>
                        </div>
                    <?php endif; ?>

                    <form id="loginForm" class="space-y-5" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/login" role="tabpanel" aria-labelledby="loginTab">
                        <div>
                            <label for="loginEmail" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.login.email_label')) ?>
                            </label>
                            <input type="email" id="loginEmail" name="email"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.login.email_placeholder')) ?>" required>
                        </div>
                        <div>
                            <label for="loginPassword" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.login.password_label')) ?>
                            </label>
                            <input type="password" id="loginPassword" name="password"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.login.password_placeholder')) ?>" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <label for="rememberMe" class="flex items-center text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" id="rememberMe" name="remember"
                                       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="<?= $iconMarginTightClass ?> block">
                                    <?= htmlspecialchars(__('auth.forms.login.remember_me')) ?>
                                </span>
                            </label>
                            <a href="#" class="text-sm text-primary hover:text-primary/80">
                                <?= htmlspecialchars(__('auth.forms.login.forgot_password')) ?>
                            </a>
                        </div>
                        <button type="submit"
                                class="w-full bg-primary text-white py-3 rounded-xl font-semibold shadow-md hover:bg-primary/90 transition">
                            <?= htmlspecialchars(__('auth.forms.login.submit')) ?>
                        </button>
                    </form>

                    <form id="registerForm" class="space-y-5 hidden" method="post" action="<?= htmlspecialchars($basePath) ?>/auth/register" role="tabpanel" aria-labelledby="registerTab">
                        <div>
                            <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.register.full_name_label')) ?>
                            </label>
                            <input type="text" id="fullName" name="name"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.register.full_name_placeholder')) ?>" required>
                        </div>
                        <div>
                            <label for="registerEmail" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.register.email_label')) ?>
                            </label>
                            <input type="email" id="registerEmail" name="email"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.register.email_placeholder')) ?>" required>
                        </div>
                        <div>
                            <label for="registerPassword" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.register.password_label')) ?>
                            </label>
                            <input type="password" id="registerPassword" name="password"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.register.password_placeholder')) ?>" required>
                            <p class="text-xs text-gray-500 mt-1">
                                <?= htmlspecialchars(__('auth.forms.register.password_hint')) ?>
                            </p>
                        </div>
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">
                                <?= htmlspecialchars(__('auth.forms.register.confirm_password_label')) ?>
                            </label>
                            <input type="password" id="confirmPassword" name="confirm_password"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/30 transition"
                                   placeholder="<?= htmlspecialchars(__('auth.forms.register.confirm_password_placeholder')) ?>" required>
                        </div>
                        <label for="agreeTerms" class="flex items-start text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" id="agreeTerms" name="terms"
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary mt-0.5" required>
                            <span class="<?= $iconMarginTightClass ?> leading-relaxed">
                                <?= __('auth.forms.register.terms_agreement') ?>
                            </span>
                        </label>
                        <button type="submit"
                                class="w-full bg-primary text-white py-3 rounded-xl font-semibold shadow-md hover:bg-primary/90 transition">
                            <?= htmlspecialchars(__('auth.forms.register.submit')) ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-12 bg-white">
    <div class="container mx-auto px-4 text-center">
        <p class="text-gray-500 text-sm">
            <?= htmlspecialchars(__('auth.footer.tagline')) ?>
        </p>
        <div class="mt-4 flex justify-center gap-4 text-sm text-primary font-medium">
            <a href="#" class="hover:underline">
                <?= htmlspecialchars(__('auth.footer.terms')) ?>
            </a>
            <span class="text-gray-300">•</span>
            <a href="#" class="hover:underline">
                <?= htmlspecialchars(__('auth.footer.privacy')) ?>
            </a>
            <span class="text-gray-300">•</span>
            <a href="#" class="hover:underline">
                <?= htmlspecialchars(__('auth.footer.contact')) ?>
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        function activateTab(target) {
            const isLogin = target === 'login';
            loginTab.classList.toggle('text-primary', isLogin);
            loginTab.classList.toggle('border-primary', isLogin);
            loginTab.classList.toggle('text-gray-500', !isLogin);
            registerTab.classList.toggle('text-primary', !isLogin);
            registerTab.classList.toggle('border-primary', !isLogin);
            registerTab.classList.toggle('text-gray-500', isLogin);

            loginForm.classList.toggle('hidden', !isLogin);
            registerForm.classList.toggle('hidden', isLogin);

            loginTab.setAttribute('aria-selected', isLogin ? 'true' : 'false');
            registerTab.setAttribute('aria-selected', !isLogin ? 'true' : 'false');
        }

        loginTab.addEventListener('click', function () {
            activateTab('login');
        });

        registerTab.addEventListener('click', function () {
            activateTab('register');
        });

        if (window.location.hash === '#register') {
            activateTab('register');
        }
    });
</script>
