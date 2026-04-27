<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compliance — Gestión de Riesgos</title>
    <meta name="description" content="Plataforma integral de evaluación de riesgos y compliance. Identifica, analiza y mitiga riesgos regulatorios con precisión y eficiencia.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/welcome.css'])
</head>

<body>

    <!-- ── TOPBAR ──────────────────────────────────────────── -->
    <nav class="topbar">

        <!-- Logo -->
        <a href="/" class="logo" aria-label="Compliance inicio">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Compliance"
                class="logo-light">
            <img
                src="{{ asset('images/LogoOnDark.png') }}"
                alt="Compliance"
                class="logo-dark">
        </a>

        <!-- Theme toggle -->
        <button class="theme-toggle" id="themeToggle" aria-label="Cambiar tema" title="Cambiar tema claro/oscuro">
            <span class="toggle-pill" id="togglePill"></span>
        </button>
    </nav>

    <!-- ── HERO ───────────────────────────────────────────── -->
    <section class="hero">
        <h1>
            Gestiona tus <span class="accent">Riesgos</span><br>con confianza
        </h1>

        <p class="subtitle">
            Plataforma integral de evaluación de riesgos y compliance diseñada para ayudar a tu organización a identificar, analizar y mitigar riesgos regulatorios con precisión y eficiencia.
        </p>

        <a href="/admin" class="btn-cta" id="accessBtn">Acceder al Panel</a>

        <p class="badge-line">
            Transforma la incertidumbre en una ventaja competitiva real. Diseñado para liderar el crecimiento de tu organización con total claridad
        </p>
    </section>

    <!-- ── CARDS ──────────────────────────────────── -->
    <div class="features">

        <div class="card">
            <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2" />
                <path d="M9 9h6M9 12h6M9 15h4" />
            </svg>
            <h3>Evaluación de Riesgos</h3>
            <p>Identifica, clasifica y pondera riesgos regulatorios en tiempo real con metodologías estandarizadas.</p>
        </div>

        <div class="card">
            <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C18.25 22.15 22 17.25 22 12V7L12 2z" />
                <path d="M9 12l2 2 4-4" />
            </svg>
            <h3>Gestión de Compliance</h3>
            <p>Controla el cumplimiento normativo de tu organización con flujos de trabajo auditables y trazables.</p>
        </div>

        <div class="card">
            <svg class="card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <h3>Alertas &amp; Seguimiento</h3>
            <p>Monitorea indicadores de riesgo y recibe alertas automáticas ante desviaciones críticas.</p>
        </div>

    </div>

    <!-- ── THEME SCRIPT ───────────────────────────────────── -->
    <script>
        (function() {
            const html = document.documentElement;
            const btn = document.getElementById('themeToggle');

            // On load: restore saved preference, or default to 'light'
            const saved = localStorage.getItem('theme') || 'light';
            html.className = saved;

            btn.addEventListener('click', function() {
                const isDark = html.classList.contains('dark');
                const next = isDark ? 'light' : 'dark';
                html.className = next;
                localStorage.setItem('theme', next);
            });
        })();
    </script>

</body>

</html>