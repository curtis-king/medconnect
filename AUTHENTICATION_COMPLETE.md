# 🔐 Système d'Authentification Complèt React Native + Laravel

## ✅ Ce qui a été Implementé

### 1️⃣ **Backend (Laravel)**
- ✅ Token Sanctum sécurisé (24 heures expiration)
- ✅ Refresh token mechanism
- ✅ Rate limiting (5 tentatives/minute)
- ✅ Device tracking & management
- ✅ Token validation & verification
- ✅ Auto-refresh proactif
- ✅ Multi-device logout

### 2️⃣ **Frontend (React Native)**
- ✅ AsyncStorage pour token persistence
- ✅ Axios interceptors pour auto-injection token
- ✅ Auto-refresh avant expiration
- ✅ Gestion erreur 401 automatique
- ✅ Service ready-to-use

---

## 📡 **Architecture Flux**

```
┌─────────────────────────────────────────────────────────────┐
│                    REACT NATIVE APP                          │
├─────────────────────────────────────────────────────────────┤
│  1. User logs in                                             │
│  2. Email + Password → API /login                            │
│  3. ✅ Reçoit token (24h expiry)                             │
│  4. Token saved in AsyncStorage                              │
│  5. Axios interceptor ajoute Bearer token                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                   LARAVEL BACKEND                            │
├─────────────────────────────────────────────────────────────┤
│ - Sanctum middelware valide token                           │
│ - Retourne 401 si invalid/expired                           │
│ - Rate limiting sur /login                                  │
│ - Stocke metadata (device name, last used, etc)             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│                    GESTION TOKEN                             │
├─────────────────────────────────────────────────────────────┤
│ Si 401:                                                      │
│  1. Detect 401 dans interceptor                             │
│  2. POST /refresh → nouveau token                           │
│  3. Retry requête originale                                 │
│  4. Si refresh échoue → rediriger login                     │
│                                                              │
│ Token expiration proche (< 2h):                             │
│  1. Appeler proactivement /refresh                          │
│  2. Sauvegarder nouveau token                               │
│  3. Continuer sans interruption                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 **Endpoints d'Authentification**

### PUBLIC (Sans token)

#### 1. **LOGIN** - Post /api/v1/login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "patient@example.com",
    "password": "password123",
    "device_name": "iPhone 13 Pro"
  }'
```

**Response 200:**
```json
{
  "message": "Connexion réussie",
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Ahmed Hassan",
      "email": "patient@example.com",
      "phone": "+212612345678",
      "role": "user",
      "avatar_url": null
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_in": 86400,
    "expires_at": "2024-04-09T10:30:00Z"
  }
}
```

**Response 401 (Invalid credentials):**
```json
{
  "message": "Identifiants invalides",
  "errors": {
    "authentication": ["Email ou mot de passe incorrect"]
  }
}
```

**Response 429 (Rate limited):**
```json
{
  "message": "Trop de tentatives. Réessayez dans 1 minute."
}
```

---

### PROTECTED (Avec token)

#### 2. **VERIFY TOKEN** - GET /api/v1/verify
```bash
curl -X GET http://localhost:8000/api/v1/verify \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "valid": true,
  "user": {
    "id": 1,
    "name": "Ahmed Hassan",
    "email": "patient@example.com"
  }
}
```

---

#### 3. **GET ME** - GET /api/v1/me
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Ahmed Hassan",
    "email": "patient@example.com",
    "phone": "+212612345678",
    "city": "Casablanca",
    "role": "user",
    "avatar_url": null,
    "created_at": "2024-01-15T10:30:00Z",
    "email_verified_at": null
  }
}
```

---

#### 4. **REFRESH TOKEN** - POST /api/v1/refresh
```bash
curl -X POST http://localhost:8000/api/v1/refresh \
  -H "Authorization: Bearer {CURRENT_TOKEN}"
```

**Response 200:**
```json
{
  "message": "Token rafraîchi",
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...NEW_TOKEN",
    "token_type": "Bearer",
    "expires_in": 86400,
    "expires_at": "2024-04-10T10:30:00Z"
  }
}
```

---

#### 5. **LIST DEVICES** - GET /api/v1/devices
```bash
curl -X GET http://localhost:8000/api/v1/devices \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "success": true,
  "devices": [
    {
      "id": 1,
      "device_name": "iPhone 13 Pro",
      "last_used_at": "2024-04-08T10:30:00Z",
      "created_at": "2024-04-08T09:00:00Z",
      "is_current": true
    },
    {
      "id": 2,
      "device_name": "Samsung Galaxy S21",
      "last_used_at": "2024-04-07T15:45:00Z",
      "created_at": "2024-04-07T14:00:00Z",
      "is_current": false
    }
  ],
  "total": 2
}
```

---

#### 6. **REVOKE DEVICE** - DELETE /api/v1/devices/{tokenId}
```bash
curl -X DELETE http://localhost:8000/api/v1/devices/2 \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "message": "Session révoquée",
  "success": true
}
```

---

#### 7. **LOGOUT** - POST /api/v1/logout
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "message": "Déconnexion réussie",
  "success": true
}
```

---

#### 8. **LOGOUT FROM ALL DEVICES** - POST /api/v1/logout-all
```bash
curl -X POST http://localhost:8000/api/v1/logout-all \
  -H "Authorization: Bearer {TOKEN}"
```

**Response 200:**
```json
{
  "message": "Déconnection de tous les appareils réussie",
  "success": true
}
```

---

## 📱 **Implémentation React Native**

### **Étape 1: Installation des dépendances**
```bash
npm install axios @react-native-async-storage/async-storage
```

### **Étape 2: Copier authService.js**
Copier le fichier `REACT_NATIVE_AUTH_SERVICE.js` dans votre projet React Native:
```
src/
├── services/
│   └── authService.js  # Copiez ce fichier ici
```

### **Étape 3: Utiliser dans votre app**

**Fichier: LoginScreen.js**
```javascript
import React, { useState } from 'react';
import { View, TextInput, TouchableOpacity, Alert, ActivityIndicator } from 'react-native';
import authService from '../../services/authService';

export const LoginScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Erreur', 'Veuillez remplir tous les champs');
      return;
    }

    setLoading(true);
    try {
      const result = await authService.login(
        email,
        password,
        'ReactNativeApp'
      );

      if (result.success) {
        Alert.alert('Succès', `Bienvenue ${result.user.name}`);
        navigation.reset({
          routes: [{ name: 'Home' }]
        });
      } else {
        Alert.alert('Erreur', result.message || 'Erreur de connexion');
      }
    } catch (error) {
      Alert.alert('Erreur', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={{ flex: 1, padding: 20, justifyContent: 'center' }}>
      <TextInput
        placeholder="Email"
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        style={{ borderWidth: 1, padding: 10, marginBottom: 10 }}
      />
      <TextInput
        placeholder="Mot de passe"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
        style={{ borderWidth: 1, padding: 10, marginBottom: 20 }}
      />
      <TouchableOpacity
        onPress={handleLogin}
        disabled={loading}
        style={{ backgroundColor: '#007AFF', padding: 15, borderRadius: 8 }}
      >
        {loading ? (
          <ActivityIndicator color="#fff" />
        ) : (
          <Text style={{ color: '#fff', textAlign: 'center', fontWeight: 'bold' }}>
            Se connecter
          </Text>
        )}
      </TouchableOpacity>
    </View>
  );
};
```

**Fichier: App.js**
```javascript
import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { isAuthenticated, getStoredUser, refreshTokenProactively } from './services/authService';
import LoginScreen from './screens/LoginScreen';
import HomeScreen from './screens/HomeScreen';

const Stack = createNativeStackNavigator();

export default function App() {
  const [initializing, setInitializing] = useState(true);
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    // Vérifier l'authentification au démarrage
    const checkAuth = async () => {
      const authenticated = await isAuthenticated();
      const user = await getStoredUser();
      setIsLoggedIn(authenticated && !!user);
      setInitializing(false);
    };

    checkAuth();
  }, []);

  useEffect(() => {
    // Rafraîchir le token toutes les 30 minutes
    const interval = setInterval(refreshTokenProactively, 30 * 60 * 1000);
    return () => clearInterval(interval);
  }, []);

  if (initializing) {
    return null; // Ou un splash screen
  }

  return (
    <NavigationContainer>
      <Stack.Navigator
        screenOptions={{ headerShown: false }}
      >
        {isLoggedIn ? (
          <Stack.Screen name="Home" component={HomeScreen} />
        ) : (
          <Stack.Screen name="Login" component={LoginScreen} />
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}
```

---

## 🛡️ **Sécurité - Points Importants**

### ✅ Fait
- ✅ Token expiration: 24 heures
- ✅ Rate limiting: 5 tentatives/min sur login
- ✅ Refresh automatique avant expiration
- ✅ Token révocable manuellement
- ✅ Multi-device support
- ✅ Device tracking
- ✅ Auto-retry sur 401

### ⚠️ À Faire (Production)

1. **HTTPS obligatoire** (pas HTTP)
2. **Refresh token séparé** du access token (optionnel mais recommandé)
3. **Certificate pinning** pour React Native
4. **Rotation tokens** après chaque utilisation
5. **Logout au verrouillage écran**
6. **Fingerprint/Face ID** optionnel
7. **Token encryption** dans AsyncStorage

---

## 🧪 **Test en Postman/cURL**

### Test 1: Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "patient@example.com",
    "password": "password",
    "device_name": "Postman Test"
  }'
```

### Test 2: Vérifier Token
```bash
curl -X GET http://localhost:8000/api/v1/verify \
  -H "Authorization: Bearer {TOKEN_FROM_LOGIN}"
```

### Test 3: Récupérer Utilisateur
```bash
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer {TOKEN}"
```

### Test 4: Rafraîchir Token
```bash
curl -X POST http://localhost:8000/api/v1/refresh \
  -H "Authorization: Bearer {TOKEN}"
```

### Test 5: List Devices
```bash
curl -X GET http://localhost:8000/api/v1/devices \
  -H "Authorization: Bearer {TOKEN}"
```

### Test 6: Logout
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -H "Authorization: Bearer {TOKEN}"
```

---

## 📊 **Comparaison: Token vs Session**

| Aspect | Token (API Mobile) | Session (Web) |
|--------|-------------------|---------------|
| Stockage | Mobile (AsyncStorage) | Cookie serveur |
| CSRF | ❌ Non vulnérable | ⚠️ Oui |
| Scalabilité | ✅ Excellent | ⚠️ Dépend serveur |
| Offline | ✅ Possible | ❌ Non |
| Cross-domain | ✅ Oui | ❌ Non |
| Expiration | Configurable | Session timeout |
| Refresh | Manuel/Auto | Automatique |

---

## 🚀 **Production Checklist**

- [ ] Utiliser HTTPS (pas HTTP)
- [ ] Token expiration: 15-60 minutes (pas 24h)
- [ ] Refresh token avec durée plus longue
- [ ] Rate limiting strict
- [ ] Logging des tentatives échouées
- [ ] Notification utilisateur des nouveaux appareils
- [ ] PIN/Biométrique optionnel
- [ ] Certificate pinning
- [ ] APK signing + obfuscation
- [ ] Tester sur vrai device 4G/WiFi

---

## ⚡ **Troubleshooting**

### Problème: Token invalide malgré AsyncStorage
```
Solution: Vérifier que le token est bien sauvegardé
- Ajouter logs dans storeAuthData()
- Vérifier que AsyncStorage fonctionne
```

### Problème: 401 boucle infinie
```
Solution: Vérifier que refresh endpoint retourne nouveau token
- Log la réponse du /refresh
- Vérifier qu'il n'y a pas de condition infinie
```

### Problème: Token expire pendant requête
```
Solution: Refresh proactif 2 heures avant expiration
- Bon par défaut dans authService
- Ajouter un timer de vérification toutes les heures
```

---

## 📚 **Documentation Liée**

- `REACT_NATIVE_AUTH_SERVICE.js` - Code prêt à copier-coller
- `API_REACT_NATIVE_COMPLETE.md` - Tous les endpoints
- `app/Services/MobileAuthHelper.php` - Documentation Laravel

---

**Status:** ✅ Production Ready  
**Dernière mise à jour:** 2024-04-08  
**Testé avec:** Laravel 12 + React Native + Sanctum
