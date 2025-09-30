<?php

return [
    'app' => [
        'name' => 'Darae – Cyber Awareness Platform',
        'description' => 'A platform dedicated to building cyber awareness and protecting data.',
    ],
    'layout' => [
        'default_title' => 'Darae – Cyber Awareness Platform',
    ],
    'languages' => [
        'en' => 'English',
        'ar' => 'Arabic',
    ],
    'header' => [
        'admin' => [
            'title' => 'Administration',
        ],
        'brand' => [
            'title' => 'Darae',
            'subtitle' => 'Cyber awareness platform',
            'home_aria' => 'Go to the homepage',
        ],
        'nav' => [
            'home' => 'Home',
            'content' => 'Awareness content',
            'exams' => 'Assessments',
            'surveys' => 'Surveys',
            'leaderboard' => 'Leaderboard',
        ],
        'cta' => [
            'account' => 'My account',
            'console' => 'Management console',
            'start_learning' => 'Start learning',
            'sign_in' => 'Sign in',
        ],
        'account' => [
            'guest' => 'User',
            'profile' => 'Profile',
            'settings' => 'Settings',
            'sign_out' => 'Sign out',
            'menu_aria' => 'Open account menu',
        ],
        'language' => [
            'menu_aria' => 'Change language',
            'switch_to' => 'Switch to :language',
        ],
        'mobile' => [
            'learn' => 'Learn',
            'menu_label' => 'Open navigation',
        ],
    ],
    'footer' => [
        'tagline' => 'A platform dedicated to building cyber awareness and protecting data.',
        'learning' => 'Learning',
        'awareness_materials' => 'Awareness materials',
        'assessments' => 'Assessments',
        'surveys' => 'Surveys',
        'quick_links' => 'Quick links',
        'sign_in' => 'Sign in',
        'create_account' => 'Create an account',
        'my_account' => 'My account',
        'sign_out' => 'Sign out',
        'leaderboard' => 'Leaderboard',
        'contact' => 'Contact',
        'email' => 'info@darae-platform.sa',
        'phone' => '+966 12 345 6789',
        'location' => 'Kingdom of Saudi Arabia',
        'social' => [
            'facebook' => 'Facebook',
            'x' => 'X',
            'linkedin' => 'LinkedIn',
        ],
        'copyright' => '© 2024 Darae Platform. All rights reserved.',
    ],
    'home' => [
        'meta' => [
            'title' => 'Darae Platform – Elevate Your Cyber Awareness',
            'description' => 'Darae is the unified cyber awareness hub that empowers employees with engaging, accessible security education.',
        ],
        'hero' => [
            'title' => 'Grow a resilient cyber-aware culture',
            'subtitle' => 'Darae delivers concise, engaging learning journeys that help every employee recognise threats and protect organisational data.',
            'browse_button' => 'Browse learning hub',
            'assessment_button' => 'Start assessments',
        ],
        'content' => [
            'title' => 'Awareness content',
            'subtitle' => 'Explore the latest articles, videos, and guides that help your team stay ahead of cyber threats.',
            'fallback_description' => 'Essential awareness content that simplifies cybersecurity for every employee.',
            'published' => 'Published: :date',
            'read_now' => 'Read now',
            'empty' => 'No learning materials are available yet.',
        ],
        'tips' => [
            'title' => 'Quick security tips',
            'subtitle' => 'Follow these simple habits to reduce everyday cyber risk across your organisation.',
            'cta' => 'Keep learning in the awareness hub',
            'items' => [
                'strong_passwords' => [
                    'title' => 'Use strong passwords',
                    'text' => 'Create long, unique passphrases and avoid reusing them across multiple services.',
                ],
                'two_factor' => [
                    'title' => 'Enable two-factor authentication',
                    'text' => 'A second verification step keeps accounts safe even if a password is exposed.',
                ],
                'suspicious_emails' => [
                    'title' => 'Inspect unexpected emails',
                    'text' => 'Watch for suspicious links or attachments and verify the sender before responding.',
                ],
                'software_updates' => [
                    'title' => 'Keep software updated',
                    'text' => 'Patching devices closes known vulnerabilities and improves protection.',
                ],
                'untrusted_wifi' => [
                    'title' => 'Avoid untrusted Wi-Fi',
                    'text' => 'Do not share sensitive data on public Wi-Fi without a trusted VPN connection.',
                ],
                'device_lock' => [
                    'title' => 'Lock your devices',
                    'text' => 'Enable screen locks and biometrics to prevent unauthorised access to mobile data.',
                ],
            ],
        ],
        'stats' => [
            'resources' => 'Awareness resources',
            'campaigns' => 'Awareness campaigns',
            'learners' => 'Empowered learners',
            'satisfaction' => 'User satisfaction',
        ],
    ],
    'auth' => [
        'meta' => [
            'title' => 'Sign in or create an account – Darae Platform',
            'description' => 'Access Darae to continue your cyber awareness journey or register a new account.',
        ],
        'hero' => [
            'title' => 'Join the Darae cyber awareness community',
            'subtitle' => 'Stay on top of organisational security campaigns, curated learning resources, and progress tracking tailored to every employee.',
            'features' => [
                'awareness' => 'Curated awareness content updated regularly',
                'campaigns' => 'Ongoing security campaigns for your teams',
                'progress' => 'Track learning progress and milestones',
            ],
        ],
        'tabs' => [
            'login' => 'Sign in',
            'register' => 'Create account',
        ],
        'forms' => [
            'login' => [
                'email_label' => 'Email address',
                'email_placeholder' => 'name@example.com',
                'password_label' => 'Password',
                'password_placeholder' => 'Enter your password',
                'remember_me' => 'Remember me',
                'forgot_password' => 'Forgot password?',
                'submit' => 'Sign in',
            ],
            'register' => [
                'full_name_label' => 'Full name',
                'full_name_placeholder' => 'Enter your full name',
                'email_label' => 'Email address',
                'email_placeholder' => 'name@example.com',
                'password_label' => 'Password',
                'password_placeholder' => 'Create a strong password',
                'password_hint' => 'Use at least 8 characters with letters, numbers, and symbols.',
                'confirm_password_label' => 'Confirm password',
                'confirm_password_placeholder' => 'Re-enter your password',
                'terms_agreement' => 'I agree to the <a href="#" class="text-primary hover:underline">Terms of service</a> and <a href="#" class="text-primary hover:underline">Privacy policy</a>.',
                'submit' => 'Create account',
            ],
        ],
        'footer' => [
            'tagline' => 'Secure your knowledge and safeguard your organisation.',
            'terms' => 'Terms of service',
            'privacy' => 'Privacy policy',
            'contact' => 'Contact us',
        ],
        'flash' => [
            'missing_credentials' => 'Please enter your email address and password.',
            'invalid_email' => 'The email address format is invalid.',
            'invalid_credentials' => 'The provided credentials do not match our records.',
            'generic_error' => 'Something went wrong. Please try again.',
            'missing_fields' => 'Please complete all required fields.',
            'password_mismatch' => 'Passwords do not match.',
            'password_length' => 'Password must contain at least :min characters.',
            'email_exists' => 'This email address is already registered.',
            'register_success' => 'Account created successfully. You can now sign in.',
        ],
    ],
    'sidebar' => [
        'aria' => [
            'open_menu' => 'Open sidebar menu',
            'close_menu' => 'Close sidebar menu',
            'panel' => 'Sidebar navigation',
        ],
        'brand' => [
            'title' => 'Darae',
            'subtitle' => 'Cyber awareness platform',
        ],
        'menu' => [
            'dashboard_admin' => 'Management dashboard',
            'content' => 'Content management',
            'exams' => 'Assessments',
            'surveys' => 'Surveys',
            'users' => 'User management',
            'reports' => 'Reports & analytics',
        ],
        'user' => [
            'default_name' => 'User',
            'department' => 'Department #:department',
            'role' => 'Role #:role',
        ],
        'logout' => 'Sign out',
    ],
];
