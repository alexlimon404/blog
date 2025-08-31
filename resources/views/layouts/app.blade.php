<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Blog')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .theme-toggle {
            background: none;
            border: none;
            color: inherit;
            font-size: 1.2rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
        }

        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        [data-bs-theme="dark"] .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .theme-icon {
            transition: transform 0.2s ease-in-out;
        }

        .theme-toggle:hover .theme-icon {
            transform: rotate(20deg);
        }

        /* Custom dark theme enhancements */
        [data-bs-theme="dark"] {
            --bs-body-bg: #0d1117;
            --bs-body-color: #e6edf3;
        }

        [data-bs-theme="dark"] .card {
            background-color: #21262d;
            border-color: #30363d;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: #30363d;
            border-color: #30363d;
        }

        [data-bs-theme="dark"] .badge {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #8b949e !important;
        }

        [data-bs-theme="dark"] .navbar-dark {
            background-color: #21262d !important;
        }

        [data-bs-theme="dark"] footer {
            background-color: #21262d !important;
        }

        [data-bs-theme="dark"] .pagination .page-link {
            background-color: #21262d;
            border-color: #30363d;
            color: #e6edf3;
        }

        [data-bs-theme="dark"] .pagination .page-link:hover {
            background-color: #30363d;
            border-color: #30363d;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .pagination .page-item.active .page-link {
            background-color: #0969da;
            border-color: #0969da;
            color: #ffffff;
        }

        [data-bs-theme="dark"] .pagination .page-item.disabled .page-link {
            background-color: #0d1117;
            border-color: #30363d;
            color: #6e7681;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Blog</a>
        <div class="navbar-nav ms-auto">
            <button class="theme-toggle" id="themeToggle" title="Toggle theme">
                <span class="theme-icon">🌙</span>
            </button>
        </div>
    </div>
</nav>

<main class="container mt-4">
    @yield('content')
</main>

<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <p>&copy; {{ date('Y') }} Blog. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme switching functionality
    (function () {
        'use strict';

        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('.theme-icon');
        const htmlElement = document.documentElement;

        // Get stored theme or default to light
        const getStoredTheme = () => localStorage.getItem('theme');
        const setStoredTheme = theme => localStorage.setItem('theme', theme);

        // Get preferred theme
        const getPreferredTheme = () => {
            const storedTheme = getStoredTheme();
            if (storedTheme) {
                return storedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        // Set theme
        const setTheme = theme => {
            htmlElement.setAttribute('data-bs-theme', theme);
            themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
        };

        // Toggle theme
        const toggleTheme = () => {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
            setStoredTheme(newTheme);
        };

        // Initialize theme
        setTheme(getPreferredTheme());

        // Listen for theme toggle
        themeToggle.addEventListener('click', toggleTheme);

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const storedTheme = getStoredTheme();
            if (!storedTheme) {
                setTheme(getPreferredTheme());
            }
        });
    })();
</script>
</body>
</html>