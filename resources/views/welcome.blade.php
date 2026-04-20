<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MedConnect') }} - La Sante, Reinventee avec Clarte</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|instrument-serif:400i" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            teal:  { DEFAULT: '#1a7b72', dark: '#16645E', light: '#e8f5f4' },
                        },
                        fontFamily: {
                            sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui'],
                        },
                    },
                },
            };
        </script>
        <style>
            .hero-img { background: linear-gradient(135deg, #88cec8 0%, #f0faf9 100%); }
            details summary::-webkit-details-marker { display: none; }
        </style>
    </head>

    <body class="bg-white font-sans text-slate-800 antialiased">

        <!-- ── NAVBAR ───────────────────────────────────────────────── -->
        <header class="sticky top-0 z-50 border-b border-slate-100 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <span class="text-lg font-bold tracking-tight text-slate-900">MedConnect</span>
                <nav class="hidden items-center gap-7 text-sm font-medium text-slate-600 md:flex">
                    <a href="#about"    class="hover:text-slate-900 transition">A propos</a>
                    <a href="#services" class="hover:text-slate-900 transition">Services</a>
                    <a href="#pricing"  class="hover:text-slate-900 transition">Tarifs</a>
                    <a href="#faq"      class="hover:text-slate-900 transition">FAQ</a>
                    <a href="#contact"  class="hover:text-slate-900 transition">Contact</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-md bg-[#1a7b72] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0f5c55] transition">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900 transition">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-[#1a7b72] px-4 py-2 text-sm font-semibold text-white hover:bg-[#0f5c55] transition">Rejoindre</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <!-- ── HERO ──────────────────────────────────────────────────── -->
        <section class="bg-[#f4faf9]" id="about">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 py-20 lg:grid-cols-2 lg:items-center lg:px-8">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-[#1a7b72]/30 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-widest text-[#1a7b72]">
                        <svg class="h-3 w-3 fill-[#1a7b72]" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"/></svg>

                        Sanctuaire Numerique de Sante Certifie
                    </span>
                    <h1 class="mt-6 text-4xl font-bold leading-tight text-slate-900 sm:text-5xl lg:text-[3.25rem]">
                        La Sante,<br>
                        <em class="not-italic text-[#1a7b72]" style="font-family:'Instrument Sans',sans-serif;font-style:italic;font-weight:700;">Reinventee</em> avec<br>
                        Clarte.
                    </h1>
                    <p class="mt-5 max-w-md text-base leading-relaxed text-slate-500">
                        MedConnect connecte les professionnels de sante, les patients et les assureurs au sein d'un ecosysteme fluide et serein.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-[#1a7b72] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#0f5c55] transition">Commencer Aujourd'hui</a>
                        @endif
                        <a href="#services" class="rounded-md border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Voir Comment Ca Fonctionne</a>
                    </div>
                </div>
                <div class="flex items-center justify-center">
                    <div class="hero-img overflow-hidden rounded-2xl shadow-lg" style="width:420px;height:320px;max-width:100%;">
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#e0f2ef] to-[#c9ebe6]">
                            <svg class="h-32 w-32 text-[#1a7b72]/40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── CONNECTING THE HEALING ECOSYSTEM ──────────────────────── -->
        <section class="bg-white px-6 py-20 lg:px-8" id="services">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 gap-16 lg:grid-cols-2 lg:items-start">
                    <!-- Left -->
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Connecter<br>l'Ecosysteme de Soin</h2>
                        <p class="mt-4 text-slate-500">Nous croyons que la sante doit etre un sanctuaire, pas une lutte. Notre plateforme harmonise les trois piliers du soin.</p>
                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center gap-3 text-slate-700">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e8f5f4]">
                                    <svg class="h-4 w-4 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg>
                                </span>
                                <span class="font-medium">Soins Centres sur le Patient</span>
                            </li>
                            <li class="flex items-center gap-3 text-slate-700">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e8f5f4]">
                                    <svg class="h-4 w-4 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                                </span>
                                <span class="font-medium">Logique Pilotee par le Medecin</span>
                            </li>
                            <li class="flex items-center gap-3 text-slate-700">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#e8f5f4]">
                                    <svg class="h-4 w-4 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </span>
                                <span class="font-medium">Integration des Assurances</span>
                            </li>
                        </ul>
                    </div>
                    <!-- Right cards -->
                    <div class="grid gap-5">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-6 shadow-sm">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#e8f5f4]">
                                <svg class="h-5 w-5 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                            </div>
                            <h3 class="font-semibold text-slate-900">Historique Complet</h3>
                            <p class="mt-1 text-sm text-slate-500">Acces a votre biographie medicale complete dans un tableau de bord securise et intuitif concu pour la clarte.</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-6 shadow-sm">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#e8f5f4]">
                                <svg class="h-5 w-5 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 14l-4-4 4-4M15 6l4 4-4 4M13 4l-2 16"/></svg>
                            </div>
                            <h3 class="font-semibold text-slate-900">Facturation Sans Friction</h3>
                            <p class="mt-1 text-sm text-slate-500">Pipeline direct entre medecins et assureurs pour reduire les delais de remboursement et les erreurs humaines.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── TAILORED SOLUTIONS ─────────────────────────────────────── -->
        <section class="bg-slate-50 px-6 py-20 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Solutions Sur Mesure</h2>
                    <p class="mt-3 text-slate-500">Des avantages specifiques concus pour les deux cotes du stethoscope.</p>
                </div>
                <!-- Main two cards -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Professionals -->
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm">
                        <div class="h-44 bg-gradient-to-br from-slate-700 to-slate-500"></div>
                        <div class="p-6">
                            <span class="text-xs font-bold uppercase tracking-widest text-[#1a7b72]">Professionnels</span>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Pratiquer avec Precision</h3>
                            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                                <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Charting Patient Assiste par IA</li>
                                <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Codage CIM-10 Automatise</li>
                                <li class="flex items-center gap-2"><svg class="h-4 w-4 shrink-0 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Orientations Multi-Disciplinaires Securisees</li>
                            </ul>
                            <a href="#" class="mt-4 inline-block text-sm font-semibold text-[#1a7b72] hover:text-[#0f5c55] transition">Decouvrir les Outils Pro →</a>
                        </div>
                    </div>
                    <!-- Patients -->
                    <div class="rounded-2xl bg-[#0f5c55] p-6 text-white shadow-sm">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#a3d9d4]">Patients</span>
                        <h3 class="mt-2 text-xl font-bold">Le Bien-Etre dans Votre Poche</h3>
                        <p class="mt-2 text-sm text-[#c0e8e4]">Prenez des rendez-vous, suivez vos constantes et contactez votre equipe soignante via une interface securisee et elegante.</p>
                        <div class="mt-6 flex gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0z"/></svg>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/10">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Three smaller cards -->
                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#e8f5f4]">
                            <svg class="h-5 w-5 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-900">Securite de Niveau Militaire</h3>
                        <p class="mt-1 text-sm text-slate-500">Conforme HIPAA & RGPD. Nous chiffrons chaque type de donnee de sante.</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#e8f5f4]">
                            <svg class="h-5 w-5 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-900">Dossiers Intelligents</h3>
                        <p class="mt-1 text-sm text-slate-500">Transferez vos dossiers instantanement entre professionnels, sans papier ni delai.</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-[#e8f5f4]">
                            <svg class="h-5 w-5 text-[#1a7b72]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 10l4.553-2.069A1 1 0 0 1 21 8.845v6.31a1 1 0 0 1-1.447.894L15 14M5 18h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-slate-900">Telesante+</h3>
                        <p class="mt-1 text-sm text-slate-500">Consultations virtuelles en haute definition avec surveillance integree des constantes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── STATS BAR ──────────────────────────────────────────────── -->
        <section class="bg-[#0f5c55] px-6 py-14 lg:px-8">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 text-center md:grid-cols-4">
                <div>
                    <div class="text-3xl font-extrabold text-white">500k+</div>
                    <div class="mt-1 text-sm font-medium text-[#a3d9d4] uppercase tracking-wide">Patients Actifs</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white">10k+</div>
                    <div class="mt-1 text-sm font-medium text-[#a3d9d4] uppercase tracking-wide">Medecins Verifies</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white">150+</div>
                    <div class="mt-1 text-sm font-medium text-[#a3d9d4] uppercase tracking-wide">Partenaires Assurance</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white">99.9%</div>
                    <div class="mt-1 text-sm font-medium text-[#a3d9d4] uppercase tracking-wide">Fiabilite Garantie</div>
                </div>
            </div>
        </section>

        <!-- ── PRICING ────────────────────────────────────────────────── -->
        <section class="bg-white px-6 py-20 lg:px-8" id="pricing">
            <div class="mx-auto max-w-5xl">
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Offres Adaptees aux Soins</h2>
                    <p class="mt-3 text-slate-500">Tarification simple et transparente pour chaque profil d'utilisateur.</p>
                </div>
                <div class="grid gap-8 md:grid-cols-2">
                    <!-- Free plan -->
                    <div class="rounded-2xl border border-slate-200 p-8">
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-slate-100">
                            <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0zM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Sanctuaire Patient</h3>
                        <p class="text-3xl font-extrabold text-slate-900 mt-3">10000 FCFA <span class="text-sm font-normal text-slate-400"></span></p>
                        <ul class="mt-6 space-y-3 text-sm text-slate-600">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Dossier Medical Personnel</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Acces au Portail de Telesante</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-[#1a7b72]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Messagerie Directe avec les Medecins</li>
                        </ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="mt-6 block w-full rounded-md border border-slate-300 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">S'inscrire Maintenant</a>
                        @endif
                    </div>
                    <!-- Pro plan -->
                    <div class="relative rounded-2xl bg-[#0f5c55] p-8 text-white shadow-lg">
                        <span class="absolute right-6 top-6 rounded-full bg-white px-3 py-0.5 text-xs font-bold text-[#0f5c55]">LE PLUS POPULAIRE</span>
                        <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/15">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold">Praticien Pro</h3>
                        <p class="text-3xl font-extrabold mt-3">15 000 FCFA <span class="text-sm font-normal text-[#a3d9d4]">/mois</span></p>
                        <ul class="mt-6 space-y-3 text-sm text-[#c0e8e4]">
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Gestion Complete du Cabinet</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Passerelle d'Integration des Assurances</li>
                            <li class="flex items-center gap-2"><svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 0 1 0 1.414l-8 8a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 1.414-1.414L8 12.586l7.293-7.293a1 1 0 0 1 1.414 0z"/></svg>Charting IA Illimite</li>
                        </ul>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="mt-6 block w-full rounded-md bg-white py-2.5 text-center text-sm font-bold text-[#0f5c55] hover:bg-slate-50 transition">Demarrer l'Essai Gratuit 30 Jours</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FAQ ───────────────────────────────────────────────────── -->
        <section class="bg-slate-50 px-6 py-20 lg:px-8" id="faq">
            <div class="mx-auto max-w-3xl">
                <h2 class="mb-10 text-center text-3xl font-bold text-slate-900 sm:text-4xl">Questions Frequentes</h2>
                <div class="space-y-4">
                    <details class="group rounded-xl border border-slate-200 bg-white">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4 font-medium text-slate-900 hover:text-[#1a7b72] transition">
                            Comment MedConnect protege-t-il mes donnees ?
                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="border-t border-slate-100 px-6 py-4 text-sm leading-relaxed text-slate-500">
                            Nous utilisons le chiffrement de bout en bout, des controles d'acces stricts et sommes entierement conformes HIPAA & RGPD. Vos donnees medicales ne sont jamais vendues ni partagees sans votre consentement explicite.
                        </div>
                    </details>
                    <details class="group rounded-xl border border-slate-200 bg-white">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4 font-medium text-slate-900 hover:text-[#1a7b72] transition">
                            Puis-je lier mon assurance sante actuelle ?
                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="border-t border-slate-100 px-6 py-4 text-sm leading-relaxed text-slate-500">
                            Oui ! MedConnect s'integre avec plus de 150 assureurs. Lors de l'inscription ou depuis vos parametres de profil, vous pouvez lier votre contrat pour un traitement automatique des remboursements.
                        </div>
                    </details>
                    <details class="group rounded-xl border border-slate-200 bg-white">
                        <summary class="flex cursor-pointer list-none items-center justify-between px-6 py-4 font-medium text-slate-900 hover:text-[#1a7b72] transition">
                            Y a-t-il des frais d'installation pour les medecins ?
                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="border-t border-slate-100 px-6 py-4 text-sm leading-relaxed text-slate-500">
                            Aucun frais d'installation. Les praticiens peuvent s'inscrire et demarrer immediatement avec un essai gratuit de 30 jours du forfait Praticien Pro. Apres cela, la tarification standard s'applique.
                        </div>
                    </details>
                </div>
            </div>
        </section>

        <!-- ── CONTACT ────────────────────────────────────────────────── -->
        <section class="bg-[#0f5c55] px-6 py-20 lg:px-8" id="contact">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
                <div class="text-white">
                    <h2 class="text-3xl font-bold sm:text-4xl">Besoin d'Aide ?</h2>
                    <p class="mt-4 text-[#c0e8e4]">Notre equipe support est a votre disposition pour vous accompagner dans votre parcours vers une meilleure gestion de sante.</p>
                    <div class="mt-8 space-y-4">
                        <div class="flex items-center gap-3 text-sm text-[#c0e8e4]">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z"/></svg>
                            </div>
                            support@medconnect.health
                        </div>
                        <div class="flex items-center gap-3 text-sm text-[#c0e8e4]">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 0 1 2-2h3.28a1 1 0 0 1 .948.684l1.498 4.493a1 1 0 0 1-.502 1.21l-2.257 1.13a11.042 11.042 0 0 0 5.516 5.516l1.13-2.257a1 1 0 0 1 1.21-.502l4.493 1.498a1 1 0 0 1 .684.949V19a2 2 0 0 1-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            +1 (888) MED-CONN
                        </div>
                    </div>
                </div>
                <form class="rounded-2xl bg-white p-8 shadow-lg" method="POST" action="#">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Prenom</label>
                            <input type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-[#1a7b72] focus:outline-none focus:ring-1 focus:ring-[#1a7b72]">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Nom</label>
                            <input type="text" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-[#1a7b72] focus:outline-none focus:ring-1 focus:ring-[#1a7b72]">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Adresse Email</label>
                        <input type="email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-[#1a7b72] focus:outline-none focus:ring-1 focus:ring-[#1a7b72]">
                    </div>
                    <div class="mt-4">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Message</label>
                        <textarea rows="4" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-[#1a7b72] focus:outline-none focus:ring-1 focus:ring-[#1a7b72]"></textarea>
                    </div>
                    <button type="submit" class="mt-4 w-full rounded-md bg-[#1a7b72] py-2.5 text-sm font-bold text-white hover:bg-[#0f5c55] transition">Envoyer la Demande</button>
                </form>
            </div>
        </section>

        <!-- ── FOOTER ─────────────────────────────────────────────────── -->
        <footer class="border-t border-slate-100 bg-white px-6 py-8 lg:px-8">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 text-sm text-slate-500 sm:flex-row">
                <span class="font-bold text-slate-900">MedConnect</span>
                <span>&copy; 2026 MedConnect &mdash; Le Sanctuaire Numerique de la Sante.</span>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-slate-800 transition">Politique de Confidentialite</a>
                    <a href="#" class="hover:text-slate-800 transition">Conditions d'Utilisation</a>
                    <a href="#contact" class="hover:text-slate-800 transition">Support</a>
                </div>
            </div>
        </footer>

    </body>
</html>
