<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedConnect') }} — Connexion</title>
    <link rel="icon" type="image/png" href="{{ asset('medconnect_3.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-medical-hero {
            background-image: linear-gradient(rgba(13,148,136,0.82), rgba(15,118,110,0.88)),
                url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=1200&q=80');
            background-size: cover; background-position: center;
        }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
        input:focus { outline: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">
<main class="flex-grow flex items-stretch min-h-screen">

    <div class="hidden lg:flex lg:w-1/2 bg-medical-hero relative items-center justify-center p-20 overflow-hidden">
        <div class="relative z-10 max-w-xl text-white">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30">
                    <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings:'FILL' 1;">medical_services</span>
                </div>
                <span class="font-headline font-extrabold text-3xl tracking-tight">MedConnect</span>
            </div>
            <h1 class="font-headline text-6xl font-extrabold tracking-tight leading-[1.1] mb-8">
                La Santé,<br><span class="text-teal-200">Reconnectée.</span>
            </h1>
            <p class="text-xl text-teal-50/90 leading-relaxed mb-12">
                Accédez à votre sanctuaire médical numérique. Une plateforme sécurisée connectant patients, médecins et assureurs.
            </p>
            <div class="grid grid-cols-2 gap-6">
                <div class="p-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/10">
                    <span class="material-symbols-outlined text-teal-200 mb-3 text-3xl block" style="font-variation-settings:'FILL' 1;">lock_person</span>
                    <p class="font-headline font-bold text-lg">Sécurité Bancaire</p>
                    <p class="text-sm text-teal-100/70 mt-2">Chiffrement AES-256 sur toutes vos données de santé.</p>
                </div>
                <div class="p-6 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/10">
                    <span class="material-symbols-outlined text-teal-200 mb-3 text-3xl block" style="font-variation-settings:'FILL' 1;">sync_saved_locally</span>
                    <p class="font-headline font-bold text-lg">Sync en Temps Réel</p>
                    <p class="text-sm text-teal-100/70 mt-2">Résultats et diagnostics mis à jour instantanément.</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-400/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="flex-grow lg:w-1/2 flex flex-col items-center justify-center p-8 bg-slate-50">
        <div class="w-full max-w-md">

            <div class="lg:hidden flex items-center justify-center gap-3 mb-12">
                <div class="w-10 h-10 bg-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-teal-600/20">
                    <span class="material-symbols-outlined text-white text-2xl" style="font-variation-settings:'FILL' 1;">medical_services</span>
                </div>
                <span class="font-headline font-extrabold text-2xl tracking-tight text-teal-600">MedConnect</span>
            </div>

            <div class="text-center mb-10">
                <h2 class="font-headline text-3xl font-bold text-slate-900">Bon Retour</h2>
                <p class="text-slate-500 mt-2">Entrez vos identifiants pour accéder à votre portail</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-teal-50 border border-teal-200 rounded-xl text-sm text-teal-700 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 px-1" for="email">Email / Identifiant</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-teal-600 transition-colors select-none">alternate_email</span>
                        <input class="w-full bg-white border @error('email') border-red-400 @else border-slate-200 @enderror focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-xl py-4 pl-12 pr-4 text-slate-900 placeholder:text-slate-400 transition-all"
                            id="email" name="email" type="email" value="{{ old('email') }}"
                            placeholder="exemple@medconnect.com" required autofocus autocomplete="username"/>
                    </div>
                    @error('email')<p class="text-sm text-red-600 px-1 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="password">Mot de Passe</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-teal-600 hover:text-teal-700 transition-colors" href="{{ route('password.request') }}">Oublié ?</a>
                        @endif
                    </div>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-teal-600 transition-colors select-none">lock_open</span>
                        <input class="w-full bg-white border @error('password') border-red-400 @else border-slate-200 @enderror focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-xl py-4 pl-12 pr-12 text-slate-900 placeholder:text-slate-400 transition-all"
                            id="password" name="password" type="password"
                            placeholder="••••••••••••" required autocomplete="current-password"/>
                        <button class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600 transition-colors" type="button"
                            onclick="const i=document.getElementById('password');i.type=i.type==='password'?'text':'password';this.querySelector('span').textContent=i.type==='password'?'visibility':'visibility_off'">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                    @error('password')<p class="text-sm text-red-600 px-1 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 px-1">
                    <input class="w-5 h-5 rounded-md border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer"
                        id="remember_me" name="remember" type="checkbox"/>
                    <label class="text-sm font-medium text-slate-500 cursor-pointer" for="remember_me">Rester connecté sur cet appareil</label>
                </div>

                <button class="w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-headline font-bold rounded-xl shadow-lg shadow-teal-600/20 hover:shadow-xl active:scale-[0.99] transition-all duration-200" type="submit">
                    Accéder au Tableau de Bord
                </button>
            </form>

            <div class="mt-10 pt-8 border-t border-slate-200 text-center">
                <p class="text-sm text-slate-500">
                    Nouveau sur MedConnect ?
                    <a class="font-bold text-teal-600 hover:underline underline-offset-4 ml-1" href="{{ route('register') }}">Créer un compte</a>
                </p>
            </div>

            <div class="mt-10 flex justify-center items-center gap-8 text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">verified</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">HIPAA Conforme</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">security</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">SSL Chiffré</span>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="py-6 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[11px] font-bold uppercase tracking-widest text-slate-400">
        <span>© {{ date('Y') }} MedConnect Digital Healthcare.</span>
        <div class="flex gap-6">
            <a class="hover:text-teal-600 transition-colors" href="#">Confidentialité</a>
            <a class="hover:text-teal-600 transition-colors" href="#">Conditions</a>
            <a class="hover:text-teal-600 transition-colors" href="#">Aide</a>
        </div>
    </div>
</footer>
</body>
</html>
