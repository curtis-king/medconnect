<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'MedConnect') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
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
                            brandBlue: '#2563eb',
                            brandGreen: '#059669',
                        },
                        fontFamily: {
                            sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui'],
                        },
                    },
                },
            };
        </script>
    </head>
    <body class="bg-white text-slate-900 antialiased">
        <!-- Navigation Header -->
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <img src="/meconnect.png" alt="MedConnect" class="h-9 w-9 rounded-lg object-cover ring-1 ring-slate-200">
                    <span class="bg-gradient-to-r from-brandBlue to-brandGreen bg-clip-text text-xl font-extrabold text-transparent">MedConnect</span>
                </div>
                <nav class="flex items-center gap-2 sm:gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-xl bg-gradient-to-r from-brandBlue to-brandGreen px-4 py-2 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:shadow-lg">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-brandBlue">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-brandBlue to-brandGreen px-4 py-2 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:shadow-lg">S'inscrire</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-brandBlue via-blue-700 to-brandGreen px-4 py-24 text-white sm:px-6 lg:px-8">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute -left-20 -top-20 h-80 w-80 rounded-full bg-white/30 blur-2xl"></div>
                <div class="absolute -bottom-24 -right-16 h-96 w-96 rounded-full bg-emerald-300/30 blur-2xl"></div>
            </div>
            <div class="relative z-10 mx-auto max-w-7xl text-center">
                <p class="mb-4 inline-flex items-center rounded-full bg-white/20 px-4 py-1 text-xs font-semibold uppercase tracking-wider">Plateforme sante connectee</p>
                <h1 class="mx-auto max-w-5xl text-4xl font-black leading-tight sm:text-5xl lg:text-6xl">Bienvenue sur MedConnect</h1>
                <p class="mx-auto mt-6 max-w-3xl text-base text-blue-50 sm:text-lg">
                    La plateforme de santé numérique qui connecte patients, professionnels et structures médicales pour un parcours de soins optimisé, transparent et sécurisé.
                </p>
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-7 py-3 text-sm font-bold text-brandBlue shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">Commencer maintenant</a>
                    @endif
                    <a href="#apropos" class="rounded-xl border-2 border-white/80 px-7 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-white/15">En savoir plus</a>
                </div>
            </div>
        </section>

        <!-- À Propos Section -->
        <section id="apropos" class="bg-slate-50 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-center text-3xl font-extrabold text-slate-900 sm:text-4xl">À propos de MedConnect</h2>
                <div class="mt-12 grid gap-10 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-base leading-8 text-slate-600">MedConnect est une plateforme innovante conçue pour révolutionner l'accès aux soins de santé. Elle offre une expérience transparente et sécurisée pour les patients, les professionnels de santé et les structures médicales.</p>
                        <p class="mt-4 text-base leading-8 text-slate-600">Avec un système de validation d'identité avancé, une gestion complète des dossiers médicaux, et des outils de paiement flexibles, MedConnect simplifie chaque étape de votre parcours médical.</p>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center gap-3 text-slate-700">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-brandGreen">✓</span>
                                <span>Sécurité et conformité des données</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-brandGreen">✓</span>
                                <span>Interface mobile-first intuitive</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-brandGreen">✓</span>
                                <span>Support 24/7 multilingue</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-700">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-brandGreen">✓</span>
                                <span>Paiements flexibles et sécurisés</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex h-80 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-100 to-emerald-100 p-12">
                        <svg class="h-24 w-24 text-brandBlue" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.99 5.51C6.47 5.51 2 7.48 2 10s4.47 4.49 9.99 4.49S22 12.51 22 10s-4.48-4.49-10.01-4.49zM11.99 13c-4.42 0-8-1.79-8-4s3.58-4 8-4 8 1.79 8 4-3.58 4-8 4zm0-12C6.48 1 2 3.43 2 6.46v11.08C2 20.57 6.48 23 12 23s10-2.43 10-5.46V6.46C22 3.43 17.52 1 12 1zm0 19.84c-4.42 0-8-1.79-8-4v-3.5c1.49 1.46 4.38 2.5 8 2.5s6.51-1.04 8-2.5v3.5c0 2.21-3.58 4-8 4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistiques Section -->
        <section class="bg-white px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-center text-3xl font-extrabold text-slate-900 sm:text-4xl">Nos chiffres clés</h2>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-blue-50 to-emerald-50 p-8 text-center transition hover:scale-105 hover:border-blue-200">
                        <div class="bg-gradient-to-r from-brandBlue to-brandGreen bg-clip-text text-4xl font-black text-transparent">50K+</div>
                        <p class="mt-1 text-sm text-slate-600">Utilisateurs actifs</p>
                    </div>
                    <div class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-emerald-50 to-blue-50 p-8 text-center transition hover:scale-105 hover:border-emerald-200">
                        <div class="bg-gradient-to-r from-brandBlue to-brandGreen bg-clip-text text-4xl font-black text-transparent">1200+</div>
                        <p class="mt-1 text-sm text-slate-600">Professionnels validés</p>
                    </div>
                    <div class="rounded-2xl border-2 border-blue-100 bg-gradient-to-br from-blue-50 to-emerald-50 p-8 text-center transition hover:scale-105 hover:border-blue-200">
                        <div class="bg-gradient-to-r from-brandBlue to-brandGreen bg-clip-text text-4xl font-black text-transparent">500M+</div>
                        <p class="mt-1 text-sm text-slate-600">XAF traités annuels</p>
                    </div>
                    <div class="rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-emerald-50 to-blue-50 p-8 text-center transition hover:scale-105 hover:border-emerald-200">
                        <div class="bg-gradient-to-r from-brandBlue to-brandGreen bg-clip-text text-4xl font-black text-transparent">99.9%</div>
                        <p class="mt-1 text-sm text-slate-600">Uptime garanti</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="bg-slate-50 px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-center text-3xl font-extrabold text-slate-900 sm:text-4xl">Nos services et avantages</h2>
                <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandBlue" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2V17zm4 0h-2V7h2V17zm4 0h-2v-4h2V17z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Gestion des dossiers</h3>
                        <p class="mt-2 text-slate-600">Dossiers médicaux numériques complets avec contrôle d'accès sécurisé et historique médical centralisé.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandGreen" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6h-2.15c-.74-1.9-2.54-3.25-4.85-3.25s-4.11 1.35-4.85 3.25H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7 14c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm5-4H7V7h10v9z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Rendez-vous en ligne</h3>
                        <p class="mt-2 text-slate-600">Prenez rendez-vous facilement avec des professionnels validés, gérez votre agenda médical simplement.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandBlue" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.72-7 8.77V12H5V6.3l7-3.11v8.8z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Sécurité renforcée</h3>
                        <p class="mt-2 text-slate-600">Validation d'identité par IA, détection de fraude, conformité RGPD et chiffrement de bout en bout.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandGreen" fill="currentColor" viewBox="0 0 24 24"><path d="M17 2H7c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-5 18c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm5-3H7V4h10v13z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Application mobile</h3>
                        <p class="mt-2 text-slate-600">Interface optimisée pour smartphones, notifications en temps réel, accès à tous vos documents.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandBlue" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5-7l-4-4-4 4h2v6h4v-6h2z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Paiements flexibles</h3>
                        <p class="mt-2 text-slate-600">Paiements en ligne sécurisés avec options multiples: Mobile Money, carte bancaire et abonnements.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                        <svg class="mb-4 h-12 w-12 text-brandGreen" fill="currentColor" viewBox="0 0 24 24"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg>
                        <h3 class="text-xl font-bold text-slate-900">Tableau de bord analytique</h3>
                        <p class="mt-2 text-slate-600">Suivi de vos dépenses médicales, historique de rendez-vous et statistiques personnalisées.</p>
                        <a href="#" class="mt-4 inline-block font-semibold text-brandBlue transition hover:text-blue-700">En savoir plus →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="bg-white px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <h2 class="text-center text-3xl font-extrabold text-slate-900 sm:text-4xl">Ce que nos utilisateurs en disent</h2>
                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    <div class="rounded-2xl border-l-4 border-brandBlue bg-gradient-to-br from-blue-50 to-emerald-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-3 text-lg tracking-wider text-amber-400">★★★★★</div>
                        <p class="text-sm italic leading-7 text-slate-700">"MedConnect a complètement transformé ma façon de gérer mes rendez-vous médicaux. C'est tellement simple et efficace!"</p>
                        <div class="mt-4 font-bold text-slate-900">Marie Dupont</div>
                        <div class="text-xs text-slate-500">Patient, Yaoundé</div>
                    </div>

                    <div class="rounded-2xl border-l-4 border-brandGreen bg-gradient-to-br from-emerald-50 to-blue-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-3 text-lg tracking-wider text-amber-400">★★★★★</div>
                        <p class="text-sm italic leading-7 text-slate-700">"En tant que professionnel, j'apprécie la sécurité et la facilité d'accès aux dossiers de mes patients. Excellent!"</p>
                        <div class="mt-4 font-bold text-slate-900">Dr. Jean Nkomo</div>
                        <div class="text-xs text-slate-500">Médecin, Douala</div>
                    </div>

                    <div class="rounded-2xl border-l-4 border-brandBlue bg-gradient-to-br from-blue-50 to-emerald-50 p-8 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-3 text-lg tracking-wider text-amber-400">★★★★★</div>
                        <p class="text-sm italic leading-7 text-slate-700">"L'interface est ergonomique et les paiements sécurisés. Vraiment content d'utiliser MedConnect!"</p>
                        <div class="mt-4 font-bold text-slate-900">Sophie Martin</div>
                        <div class="text-xs text-slate-500">Infirmière, Libreville</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Adhésion Section -->
        <section class="relative overflow-hidden bg-gradient-to-r from-brandBlue to-brandGreen px-4 py-20 text-white sm:px-6 lg:px-8">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute -right-10 -top-12 h-80 w-80 rounded-full bg-white/30 blur-2xl"></div>
            </div>
            <div class="relative z-10 mx-auto max-w-4xl text-center">
                <h2 class="text-center text-3xl font-extrabold text-white sm:text-4xl">Prêt à rejoindre MedConnect?</h2>
                <p class="mt-4 text-blue-50">
                    Inscrivez-vous dès maintenant et accédez à une plateforme de santé moderne, sécurisée et performante. Gratuit et facile!
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-7 py-3 text-sm font-bold text-brandBlue shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">S'inscrire maintenant</a>
                    @endif
                    <a href="{{ route('login') }}" class="rounded-xl border-2 border-white/80 px-7 py-3 text-sm font-bold text-white transition hover:-translate-y-0.5 hover:bg-white/15">Déjà inscrit? Se connecter</a>
                </div>
            </div>
        </section>

        <!-- Publicité & Partenaires Section -->
        <section class="bg-slate-50 px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-16">
                    <h3 class="mb-8 text-center text-2xl font-extrabold text-slate-900">Espace Publicitaire</h3>
                    <div class="rounded-2xl border-2 border-blue-200 bg-gradient-to-r from-blue-100 to-emerald-100 p-12 text-center">
                        <p class="mx-auto max-w-2xl text-lg text-slate-700">
                            Vous êtes une entreprise intéressée par le partenariat ou la publicité?
                        </p>
                        <a href="mailto:contact@medconnect.cm" class="mt-5 inline-block rounded-xl bg-brandBlue px-6 py-2.5 text-sm font-semibold text-white shadow transition hover:-translate-y-0.5 hover:bg-blue-700">Nous contacter</a>
                    </div>
                </div>

                <div>
                    <h3 class="mb-10 text-center text-2xl font-extrabold text-slate-900">Nos partenaires de confiance</h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <p class="font-bold text-slate-700">Partenaire 1</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <p class="font-bold text-slate-700">Partenaire 2</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <p class="font-bold text-slate-700">Partenaire 3</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <p class="font-bold text-slate-700">Partenaire 4</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gradient-to-br from-slate-900 to-slate-800 px-4 py-14 text-white sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <h4 class="mb-4 text-lg font-bold text-white">MedConnect</h4>
                        <p class="text-slate-400">Votre plateforme de santé numérique de confiance.</p>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-bold text-white">Produit</h4>
                        <ul class="space-y-3 text-slate-300">
                            <li><a href="#" class="transition hover:text-blue-300">Fonctionnalités</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Tarification</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Sécurité</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-bold text-white">Entreprise</h4>
                        <ul class="space-y-3 text-slate-300">
                            <li><a href="#" class="transition hover:text-blue-300">À propos</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Blog</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Carrières</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="mb-4 text-lg font-bold text-white">Légal</h4>
                        <ul class="space-y-3 text-slate-300">
                            <li><a href="#" class="transition hover:text-blue-300">Confidentialité</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Conditions</a></li>
                            <li><a href="#" class="transition hover:text-blue-300">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-10 border-t border-white/10 pt-8">
                    <p class="text-center text-sm text-slate-400">&copy; 2026 MedConnect. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
