# ⚠️ Guide des Erreurs d'Authentification

## 401 Unauthorized

### Erreur 1: Token invalide/expiré
```json
{
  "message": "Unauthenticated",
  "exception": "AuthenticationException"
}
```

**Causes possibles:**
- Token expiré (24h)
- Token corrompu
- Token d'un autre utilisateur
- Mauvais format Bearer

**Comment corriger:**
```javascript
// MAUVAIS ❌
'Authorization', '1|abc123'

// BON ✅
'Authorization', 'Bearer 1|abc123'
```

**Solution automatique (déjà implémentée):**
- React Native interceptor détecte 401
- Appelle `/api/v1/refresh` automatiquement
- Retry la requête originale
- Sinon redirection login

---

## 401 - Identifiants invalides

```json
{
  "message": "Identifiants invalides",
  "errors": {
    "authentication": ["Email ou mot de passe incorrect"]
  }
}
```

**Causes possibles:**
- Email n'existe pas
- Password incorrect
- User supprimé/désactivé

**Comment corriger:**
```php
// Vérifier dans Laravel Tinker
php artisan tinker
>>> User::where('email', 'test@example.com')->first()
```

Si null → l'utilisateur n'existe pas. Créer:
```php
>>> User::create([
    'name' => 'Test',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
    'phone' => '+212612345678'
]);
```

---

## 429 Too Many Requests

```json
{
  "message": "Trop de tentatives. Réessayez dans 1 minute."
}
```

**Causes possibles:**
- 5+ tentatives login en 1 minute
- Rate limiting actif

**Comment corriger:**
```javascript
// React Native: Attendre avant de retry
const handleLoginFailure = (error) => {
  if (error.response.status === 429) {
    Alert.alert(
      'Trop de tentatives',
      'Veuillez patienter 1 minute avant de réessayer'
    );
    // Bloquer le bouton login pendant 60 secondes
    setIsBlocked(true);
    setTimeout(() => setIsBlocked(false), 60000);
  }
};
```

**Backend:** Configuration dans `app/Http/Controllers/Api/V1/AuthController.php`
```php
// Limité à 5 tentatives/minute par IP (ligne 47)
$this->throttle('login:' . request()->ip(), 5, 1);
```

---

## 422 Unprocessable Entity

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required"],
    "password": ["The password field is required"],
    "device_name": ["The device name field is required"]
  }
}
```

**Causes possibles:**
- Champs manquants dans la requête
- Format email invalide
- Password trop court

**Comment corriger:**

```javascript
// Avant d'appeler login(), vérifier:
const validateLoginForm = (email, password) => {
  const errors = {};
  
  if (!email) errors.email = 'Email requis';
  if (!password) errors.password = 'Password requis';
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    errors.email = 'Email invalide';
  }
  if (password.length < 6) {
    errors.password = 'Min 6 caractères';
  }
  
  return Object.keys(errors).length === 0 ? null : errors;
};

// Utiliser:
const errors = validateLoginForm(email, password);
if (errors) {
  setValidationErrors(errors);
  return;
}
```

---

## 500 Internal Server Error

```json
{
  "message": "Server Error",
  "exception": "ErrorException"
}
```

**Causes possibles:**
- Bug dans le code Laravel
- Database hors ligne
- Fichier corrompu

**Comment corriger:**

1. **Vérifier les logs Laravel:**
```bash
tail -f storage/logs/laravel.log
```

2. **Tester chaque endpoint:**
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer token"
```

3. **Vérifier la database:**
```bash
php artisan tinker
>>> User::count()  # Devrait retourner > 0
```

---

## CORS Error (Browser/React Native)

```error
Access to XMLHttpRequest at 'http://localhost:8000/api/v1/login' 
from origin 'http://localhost:3000' has been blocked by CORS policy
```

**Causes possibles:**
- CORS mal configurée
- Frontend port pas autorisé
- Request header invalide

**Comment corriger:**

**Fichier: config/cors.php**
```php
'allowed_origins' => [
    'http://localhost:3000',      // React web
    'http://localhost:8081',      // React Native
    'http://127.0.0.1:3000',
    'http://127.0.0.1:8081',
],
```

**React Native:**
```javascript
// Le fetch de React Native n'a pas CORS restrictions
// Mais vérifier que le backend accepte localhost:8081

// Dans bootstrap/app.php:
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

---

## Token Interceptor Issues

### Problème: Interceptor ne rajoute pas le token

```javascript
// ❌ MAUVAIS - Token pas rajouté
const api = axios.create({
  baseURL: 'http://localhost:8000'
});

// ✅ BON - Utiliser authService
import authService from './authService';
const api = authService.getApiInstance();
```

### Problème: Boucle infinie de refresh

```javascript
// ❌ MAUVAIS - Boucle infinie
response interceptor {
  if (error.response.status === 401) {
    const refreshed = await refresh();
    return api.request(error.config);  // Refait la même requête
  }
}
```

**Solution:** Le code fourni gère déjà ça
```javascript
// ✅ BON - Utiliser le authService.js fourni
// Il a une flag pour éviter les boucles
let isRefreshing = false;
let failedQueue = [];
```

---

## AsyncStorage Issues

### Problème: Token pas sauvegardé

```javascript
// ❌ MAUVAIS
const storeToken = (token) => {
  // Attendre pas implémenté
  AsyncStorage.setItem('auth_token', token);
  return token;  // Retourne avant que le token soit vraiment sauvé!
};

// ✅ BON
const storeToken = async (token) => {
  await AsyncStorage.setItem('auth_token', token);
  return token;
};
```

### Test si AsyncStorage fonctionne:
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

const testAsyncStorage = async () => {
  try {
    await AsyncStorage.setItem('test', 'hello');
    const retrieved = await AsyncStorage.getItem('test');
    console.log('✅ AsyncStorage fonctionne:', retrieved);
  } catch (error) {
    console.error('❌ AsyncStorage erreur:', error);
  }
};

testAsyncStorage();
```

---

## Network Errors

### Problème: "Network Error" lors du login

```javascript
// Solution: Vérifier que Laravel tourne
curl http://localhost:8000/api/v1/login
// Devrait retourner 422 (validation), pas "Cannot get"
```

### Problème: Timeout sur /refresh

```javascript
// Les timeouts sont normaux si le réseau est lent
// Solution: Augmenter le timeout de Axios
const api = axios.create({
  baseURL: 'http://localhost:8000',
  timeout: 10000  // 10 secondes au lieu de 5
});
```

---

## Device Management Errors

### Problème: Impossible de lister devices

```json
{
  "message": "Unauthorized",
  "exception": "UnauthorizedHttpException"
}
```

**Cause:** Token invalide

**Solution:**
```javascript
const listDevices = async () => {
  const token = await AsyncStorage.getItem('auth_token');
  if (!token) {
    // Rediriger login
    return;
  }
  
  try {
    const response = await api.get('/devices', {
      headers: { Authorization: `Bearer ${token}` }
    });
    setDevices(response.data.devices);
  } catch (error) {
    if (error.response?.status === 401) {
      // Token expiré - interceptor fera le refresh auto
      this.listDevices(); // Retry
    }
  }
};
```

---

## Production Errors

### Problème: Token fonctionne mais endpoint dit "unauthorized"

```json
{
  "message": "This action is unauthorized."
}
```

**Cause:** Pas un problème d'auth token, c'est une authorization policy

**Solution:** Vérifier les policies Laravel
```bash
cd app/Policies
# Voir les fichiers de policies
```

---

## Integration Checklist

- [ ] User créé dans database avec password hashhé
- [ ] API tourne sur http://localhost:8000
- [ ] CORS configurée pour React Native port
- [ ] AsyncStorage installé et fonctionne
- [ ] Token sauvegardé après login
- [ ] Interceptor rajoute Bearer token
- [ ] 401 déclenche refresh automatiquement
- [ ] Refresh retourne nouveau token valide
- [ ] Logout invalide le token
- [ ] Rate limiting accepte 5 tentatives/minute

---

## Log Debug

### Voir les tokens stockés (React Native)
```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

const debugAuthStorage = async () => {
  const keys = await AsyncStorage.getAllKeys();
  const authKeys = keys.filter(k => k.includes('auth'));
  
  for (const key of authKeys) {
    const value = await AsyncStorage.getItem(key);
    console.log(`🔐 ${key}:`, value);
  }
};

debugAuthStorage();
```

### Voir tous les tokens en database (Laravel)
```bash
php artisan tinker
>>> DB::table('personal_access_tokens')->get();
```

### Voir les tentatives login échouées
```bash
php artisan tinker
>>> Cache::get('login:' . request()->ip())
```

---

## Dashboard Debugging

| Erro | Status | Solution |
|------|--------|----------|
| "Unauthenticated" | 401 | Vérifier token présent et valide |
| "Identifiants invalides" | 401 | Vérifier email/password corrects |
| "Trop de tentatives" | 429 | Attendre 1 minute, rate limiting |
| "Validation failed" | 422 | Vérifier tous les champs remplis |
| "Server Error" | 500 | Voir `storage/logs/laravel.log` |
| CORS Error | CORS | Vérifier `config/cors.php` |
| "Cannot find module" | JS | Vérifier authService.js est importé |
| Token pas sauvé | - | AsyncStorage async/await |

---

**Status:** ✅ Guide complet  
**Mis à jour:** 2024-04-08
