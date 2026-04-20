# Frontend Authentication Code Summary

This document contains a comprehensive audit of all frontend/UI code related to API authentication, token storage, and API calls in the MedConnect application.

## Overview

The application has **two distinct authentication patterns**:

1. **Web Interface (Blade Templates)**: Traditional Laravel form-based authentication with server-side sessions
2. **Mobile API (React Native/JS)**: Bearer token-based authentication using Laravel Sanctum

---

## Part 1: React Native / Mobile API Authentication Service

### File: `REACT_NATIVE_AUTH_SERVICE.js`

**Location**: Root project directory  
**Purpose**: Complete authentication service for React Native apps communicating with the Laravel API

#### Configuration

```javascript
// API CONFIGURATION
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api/v1';
const TOKEN_STORAGE_KEY = 'auth_token';
const TOKEN_EXPIRES_KEY = 'auth_token_expires_at';
const USER_STORAGE_KEY = 'auth_user';

// API CLIENT CREATION
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});
```

#### Token Storage
- **Storage Medium**: `AsyncStorage` (React Native)
- **Token Keys**:
  - `auth_token`: The JWT/Bearer token
  - `auth_token_expires_at`: Token expiration timestamp
  - `auth_user`: Cached user object

#### Request Interceptor - Adds Token to Every Request

```javascript
api.interceptors.request.use(
  async (config) => {
    try {
      const token = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error('Error getting token from storage:', error);
    }
    return config;
  },
  (error) => Promise.reject(error)
);
```

**Key Point**: Automatically adds `Authorization: Bearer {token}` header to all protected requests.

#### Response Interceptor - Handles Token Expiration

```javascript
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // If 401 and not already retrying
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        console.log('Token expiré, tentative de rafraîchissement...');
        
        // Get current token for refresh request
        const currentToken = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
        const refreshConfig = {
          ...api.defaults,
          headers: {
            ...api.defaults.headers,
            Authorization: `Bearer ${currentToken}`,
          },
        };

        // Call refresh endpoint
        const response = await axios.post(
          `${API_BASE_URL}/refresh`,
          {},
          refreshConfig
        );

        if (response.data.success && response.data.data.token) {
          const { token, expires_at } = response.data.data;

          // Save new token
          await AsyncStorage.multiSet([
            [TOKEN_STORAGE_KEY, token],
            [TOKEN_EXPIRES_KEY, expires_at],
          ]);

          console.log('Token rafraîchi avec succès');

          // Retry original request with new token
          originalRequest.headers.Authorization = `Bearer ${token}`;
          return api(originalRequest);
        }
      } catch (refreshError) {
        console.error('Impossible de rafraîchir le token:', refreshError.message);
        
        // Refresh failed - clear auth
        await clearAuth();
      }
    }

    return Promise.reject(error);
  }
);
```

**Key Features**:
- Automatically detects 401 (Unauthorized) responses
- Calls `/refresh` endpoint to get new token
- Saves new token to AsyncStorage
- Retries original request with new token
- Clears auth on refresh failure

#### Utility Functions

##### Store Authentication Data

```javascript
const storeAuthData = async (user, token, expiresAt) => {
  try {
    await AsyncStorage.multiSet([
      [TOKEN_STORAGE_KEY, token],
      [TOKEN_EXPIRES_KEY, expiresAt],
      [USER_STORAGE_KEY, JSON.stringify(user)],
    ]);
    console.log('Auth data stored successfully');
  } catch (error) {
    console.error('Error storing auth data:', error);
    throw error;
  }
};
```

##### Retrieve Token

```javascript
const getStoredToken = async () => {
  try {
    return await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
  } catch (error) {
    console.error('Error retrieving token:', error);
    return null;
  }
};
```

##### Retrieve User

```javascript
const getStoredUser = async () => {
  try {
    const userJson = await AsyncStorage.getItem(USER_STORAGE_KEY);
    return userJson ? JSON.parse(userJson) : null;
  } catch (error) {
    console.error('Error retrieving user:', error);
    return null;
  }
};
```

##### Check Token Expiration

```javascript
const isTokenExpiringSoon = async () => {
  try {
    const expiresAt = await AsyncStorage.getItem(TOKEN_EXPIRES_KEY);
    if (!expiresAt) return true;

    const expiresAtDate = new Date(expiresAt);
    const now = new Date();
    const diffMs = expiresAtDate - now;
    const diffHours = diffMs / (1000 * 60 * 60);

    // Return true if expiring in less than 2 hours
    return diffHours < 2;
  } catch (error) {
    console.error('Error checking token expiration:', error);
    return true;
  }
};
```

##### Proactive Token Refresh

```javascript
const refreshTokenProactively = async () => {
  try {
    const isExpiring = await isTokenExpiringSoon();

    if (isExpiring) {
      console.log('Token va expirer bientôt, rafraîchissement...');
      const response = await api.post('/refresh');

      if (response.data.success && response.data.data.token) {
        const { token, expires_at } = response.data.data;
        await AsyncStorage.multiSet([
          [TOKEN_STORAGE_KEY, token],
          [TOKEN_EXPIRES_KEY, expires_at],
        ]);
        console.log('Token rafraîchi proactivement');
      }
    }
  } catch (error) {
    console.error('Erreur lors du refresh proactif:', error);
  }
};
```

##### Clear Authentication

```javascript
const clearAuth = async () => {
  try {
    await AsyncStorage.multiRemove([
      TOKEN_STORAGE_KEY,
      TOKEN_EXPIRES_KEY,
      USER_STORAGE_KEY,
    ]);
    console.log('Auth data cleared');
  } catch (error) {
    console.error('Error clearing auth data:', error);
  }
};
```

##### Check Authentication Status

```javascript
const isAuthenticated = async () => {
  try {
    const token = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
    return !!token;
  } catch (error) {
    console.error('Error checking authentication:', error);
    return false;
  }
};
```

#### Authentication API Methods

##### 1. Login

```javascript
authService.login = async (email, password, deviceName = 'ReactNativeApp') => {
  try {
    const response = await api.post('/login', {
      email,
      password,
      device_name: deviceName,
    });

    if (response.data.success) {
      const { user, token, expires_at } = response.data.data;
      await storeAuthData(user, token, expires_at);
      return {
        success: true,
        user,
        token,
      };
    }

    return {
      success: false,
      message: response.data.message,
    };
  } catch (error) {
    console.error('Login error:', error);
    return {
      success: false,
      message: error.response?.data?.message || 'Erreur de connexion',
      errors: error.response?.data?.errors,
    };
  }
};
```

**Endpoint**: `POST /api/v1/login`  
**Headers**: None (public)  
**Body**: 
```json
{
  "email": "user@example.com",
  "password": "password123",
  "device_name": "ReactNativeApp"
}
```

**Store**: Token and user to AsyncStorage

##### 2. Logout

```javascript
authService.logout = async () => {
  try {
    await api.post('/logout');
    await clearAuth();
    return { success: true };
  } catch (error) {
    console.error('Logout error:', error);
    // Clear auth anyway
    await clearAuth();
    return { success: false };
  }
};
```

**Endpoint**: `POST /api/v1/logout`  
**Headers**: `Authorization: Bearer {token}`

##### 3. Logout from All Devices

```javascript
authService.logoutFromAllDevices = async () => {
  try {
    await api.post('/logout-all');
    await clearAuth();
    return { success: true };
  } catch (error) {
    console.error('Logout all error:', error);
    await clearAuth();
    return { success: false };
  }
};
```

**Endpoint**: `POST /api/v1/logout-all`  
**Headers**: `Authorization: Bearer {token}`

##### 4. Get Current User (Me)

```javascript
authService.getCurrentUser = async () => {
  try {
    const response = await api.get('/me');
    if (response.data.success) {
      const user = response.data.user;
      // Update local data
      await AsyncStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
      return user;
    }
  } catch (error) {
    console.error('Get current user error:', error);
  }
  return null;
};
```

**Endpoint**: `GET /api/v1/me`  
**Headers**: `Authorization: Bearer {token}`

##### 5. Verify Token

```javascript
authService.verifyToken = async () => {
  try {
    const response = await api.get('/verify');
    return response.data.valid === true;
  } catch (error) {
    console.error('Token verification error:', error);
    return false;
  }
};
```

**Endpoint**: `GET /api/v1/verify`  
**Headers**: `Authorization: Bearer {token}`

##### 6. Refresh Token

```javascript
authService.refreshToken = async () => {
  try {
    const response = await api.post('/refresh');
    if (response.data.success && response.data.data.token) {
      const { token, expires_at } = response.data.data;
      await storeAuthData(
        await getStoredUser(),
        token,
        expires_at
      );
      return { success: true, token };
    }
  } catch (error) {
    console.error('Token refresh error:', error);
    return { success: false };
  }
};
```

**Endpoint**: `POST /api/v1/refresh`  
**Headers**: `Authorization: Bearer {token}`

##### 7. Get Active Devices

```javascript
authService.getActiveDevices = async () => {
  try {
    const response = await api.get('/devices');
    return response.data.devices || [];
  } catch (error) {
    console.error('Get devices error:', error);
    return [];
  }
};
```

**Endpoint**: `GET /api/v1/devices`  
**Headers**: `Authorization: Bearer {token}`

##### 8. Revoke Device

```javascript
authService.revokeDevice = async (tokenId) => {
  try {
    await api.delete(`/devices/${tokenId}`);
    return { success: true };
  } catch (error) {
    console.error('Revoke device error:', error);
    return { success: false };
  }
};
```

**Endpoint**: `DELETE /api/v1/devices/{tokenId}`  
**Headers**: `Authorization: Bearer {token}`

---

## Part 2: React Native API Service (TypeScript Version)

### File: `REACT_NATIVE_API_SERVICE_FIXED.js`

**Location**: Root project directory  
**Purpose**: TypeScript version of the authentication service with typed responses

#### Client Configuration

```typescript
const STORAGE_KEYS = {
  TOKEN: "@medconnect_token",
  USER: "@medconnect_user",
};

const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});
```

#### Request Interceptor

```typescript
apiClient.interceptors.request.use(
  async (config) => {
    try {
      // Retrieve token from AsyncStorage with correct key
      const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
      if (token) {
        // Add token to Authorization header with "Bearer TOKEN" format
        config.headers.Authorization = `Bearer ${token}`;
        console.log("✅ Token ajouté au header Authorization");
      } else {
        console.log("⚠️ Pas de token trouvé");
      }
    } catch (error) {
      console.error("❌ Erreur lors de la lecture du token:", error);
    }
    return config;
  },
  (error) => Promise.reject(error),
);
```

#### Response Interceptor

```typescript
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      console.log("❌ 401 Unauthorized - Token invalide ou expiré");
      // Clear storage
      await AsyncStorage.removeItem(STORAGE_KEYS.TOKEN);
      await AsyncStorage.removeItem(STORAGE_KEYS.USER);
    }
    return Promise.reject(error);
  },
);
```

#### Authentication Functions

##### Login

```typescript
export const login = async (
  email: string,
  password: string,
  deviceName: string = "ReactNativeApp",
): Promise<User & { token: string }> => {
  try {
    console.log("🔄 Tentative de connexion...");

    const response = await apiClient.post<any>("/v1/login", {
      email,
      password,
      device_name: deviceName,
    });

    console.log("✅ Réponse login reçue:", response.data);

    // Backend returns: { success, message, data: { user, token, ... } }
    const loginData = response.data.data || response.data;
    const { user, token } = loginData;

    if (!token || !user) {
      throw new Error("Token ou utilisateur manquant dans la réponse");
    }

    // Store token and user with SAME keys
    await AsyncStorage.multiSet([
      [STORAGE_KEYS.TOKEN, token],
      [STORAGE_KEYS.USER, JSON.stringify(user)],
    ]);

    console.log("✅ Token sauvegardé:", token.substring(0, 20) + "...");
    console.log("✅ Utilisateur sauvegardé:", user.email);

    return {
      ...user,
      token,
    };
  } catch (error: any) {
    console.error("❌ Erreur login:", error.response?.data || error.message);
    throw error;
  }
};
```

##### Logout

```typescript
export const logout = async (): Promise<void> => {
  try {
    console.log("🔄 Déconnexion en cours...");

    // Call POST to /api/v1/logout
    await apiClient.post("/v1/logout");

    // Remove token and user from storage
    await AsyncStorage.multiRemove([STORAGE_KEYS.TOKEN, STORAGE_KEYS.USER]);

    console.log("✅ Déconnexion réussie");
  } catch (error) {
    console.error("❌ Erreur lors de la déconnexion:", error);
    // Clear auth anyway
    await AsyncStorage.multiRemove([STORAGE_KEYS.TOKEN, STORAGE_KEYS.USER]);
  }
};
```

##### Get Stored Token

```typescript
export const getToken = async (): Promise<string | null> => {
  try {
    // USE CORRECT KEY!
    const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
    console.log(token ? "✅ Token trouvé" : "⚠️ Pas de token");
    return token;
  } catch (error) {
    console.error("❌ Erreur lors de la récupération du token:", error);
    return null;
  }
};
```

##### Get Current User

```typescript
export const getCurrentUser = async (): Promise<User | null> => {
  try {
    // USE CORRECT KEY!
    const userJson = await AsyncStorage.getItem(STORAGE_KEYS.USER);
    const user = userJson ? JSON.parse(userJson) : null;
    console.log(user ? "✅ Utilisateur trouvé" : "⚠️ Pas d'utilisateur");
    return user;
  } catch (error) {
    console.error("❌ Erreur lors de la récupération de l'utilisateur:", error);
    return null;
  }
};
```

##### Is Authenticated

```typescript
export const isAuthenticated = async (): Promise<boolean> => {
  const token = await getToken();
  return !!token;
};
```

##### Get Profile

```typescript
export const getProfile = async (): Promise<User> => {
  try {
    console.log("🔄 Récupération du profil...");

    const response = await apiClient.get("/v1/profile");

    console.log("✅ Profil reçu:", response.data);

    return response.data.data || response.data;
  } catch (error: any) {
    console.error("❌ Erreur profil:", error.response?.data || error.message);
    throw error;
  }
};
```

**Endpoint**: `GET /api/v1/profile`  
**Headers**: `Authorization: Bearer {token}`

---

## Part 3: Web Interface Authentication

### File: `resources/views/auth/login.blade.php`

**Type**: Traditional Laravel form-based authentication  
**Purpose**: Login page for web interface

#### Login Form

```html
<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    <div class="space-y-2">
        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 px-1" for="email">
            Email / Identifiant
        </label>
        <div class="relative group">
            <input 
                class="w-full bg-white border @error('email') border-red-400 @else border-slate-200 @enderror focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-xl py-4 pl-12 pr-4 text-slate-900 placeholder:text-slate-400 transition-all"
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email') }}"
                placeholder="exemple@medconnect.com" 
                required 
                autofocus 
                autocomplete="username"
            />
        </div>
        @error('email')<p class="text-sm text-red-600 px-1 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-2">
        <div class="flex justify-between items-center px-1">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-500" for="password">
                Mot de Passe
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-teal-600 hover:text-teal-700 transition-colors" href="{{ route('password.request') }}">
                    Oublié ?
                </a>
            @endif
        </div>
        <div class="relative group">
            <input 
                class="w-full bg-white border @error('password') border-red-400 @else border-slate-200 @enderror focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 rounded-xl py-4 pl-12 pr-12 text-slate-900 placeholder:text-slate-400 transition-all"
                id="password" 
                name="password" 
                type="password"
                placeholder="••••••••••••" 
                required 
                autocomplete="current-password"
            />
        </div>
        @error('password')<p class="text-sm text-red-600 px-1 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center gap-3 px-1">
        <input 
            class="w-5 h-5 rounded-md border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer"
            id="remember_me" 
            name="remember" 
            type="checkbox"
        />
        <label class="text-sm font-medium text-slate-500 cursor-pointer" for="remember_me">
            Rester connecté sur cet appareil
        </label>
    </div>

    <button 
        class="w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-headline font-bold rounded-xl shadow-lg shadow-teal-600/20 hover:shadow-xl active:scale-[0.99] transition-all duration-200" 
        type="submit"
    >
        Accéder au Tableau de Bord
    </button>
</form>
```

**Key Points**:
- POST to `{{ route('login') }}`
- Includes CSRF token (`@csrf`)
- Server-side form validation
- Session-based authentication (not API token-based)
- No localStorage or axios usage

---

## Part 4: Frontend Bootstrap Configuration

### File: `resources/js/bootstrap.js`

```javascript
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

**Configuration Details**:
- Imports Axios globally
- Adds `X-Requested-With: XMLHttpRequest` header to all requests
- This is for traditional CSRF protection in Laravel

**Note**: No Bearer token configuration here - this is for web forms only.

---

## Part 5: Frontend API Calls in Views

### Location: Various Blade Templates

The application uses Fetch API for some AJAX calls within the web interface:

#### 1. Notifications Polling (`resources/views/layouts/app.blade.php`)

```javascript
const syncNotifications = async () => {
  try {
    const response = await window.axios.get(liveNotificationsUrl);
    const payload = response?.data ?? {};
    const notifications = Array.isArray(payload.notifications) ? payload.notifications : [];

    setUnreadIndicators(payload.unread_count ?? 0);

    notifications.forEach((notification) => {
      if (notification?.id) {
        if (!initialized) {
          knownNotificationIds.add(notification.id);
          return;
        }

        if (knownNotificationIds.has(notification.id)) {
          return;
        }

        knownNotificationIds.add(notification.id);
      }

      dispatchNotification({
        ...(notification?.data ?? {}),
        id: notification?.id,
        message: notification?.message,
        type: notification?.type,
        created_at: notification?.created_at,
        read_at: notification?.read_at,
      });
    });

    initialized = true;
  } catch (error) {
    console.debug('Fallback polling notifications unavailable.', error);
  }
};

syncNotifications();
window.setInterval(syncNotifications, 6000);
```

**Endpoint**: `GET {{ route('patient.notifications.live') }}`  
**Polling Interval**: Every 6 seconds  
**Authentication**: Session-based (via cookies)

#### 2. Search API Calls

Example from `resources/views/carte-medicale/index.blade.php`:

```javascript
fetch(`{{ route('carte-medicale.search') }}?q=${encodeURIComponent(query)}`)
  .then(response => response.json())
  .then(data => {
    // Handle results
  })
  .catch(error => console.error('Error:', error));
```

**Key Points**:
- Uses Fetch API, not Axios
- Session-based authentication (no token)
- CSRF token added by Laravel automatically

#### 3. Form Submissions with Fetch

Example from `resources/views/subscriptions/create.blade.php`:

```javascript
fetch(`{{ route('subscriptions.calculer') }}`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  },
  body: JSON.stringify(data)
})
.then(response => response.json())
.then(data => {
  // Handle results
})
.catch(error => console.error('Error:', error));
```

**Key Points**:
- Manually includes CSRF token from meta tag
- Session-based authentication
- No Bearer token usage

---

## Part 6: API Documentation

### File: `API_MOBILE_DOCUMENTATION.md`

#### Base URL
```
http://localhost:8000/api/v1
```

#### Authentication Method
- **Type**: Bearer Token (Laravel Sanctum)
- **Format**: `Authorization: Bearer {token}`
- **Storage**: AsyncStorage

#### Complete Authentication Endpoints

```
POST /api/v1/login
- Public endpoint
- Returns: { user, token, token_type, expires_at }

POST /api/v1/logout
- Protected: Bearer token required
- Revokes current token

POST /api/v1/logout-all
- Protected: Bearer token required
- Revokes all tokens for user

GET /api/v1/me
- Protected: Bearer token required
- Returns current authenticated user

GET /api/v1/verify
- Protected: Bearer token required
- Verifies token validity

POST /api/v1/refresh
- Protected: Bearer token required
- Returns new token with extended expiration

GET /api/v1/devices
- Protected: Bearer token required
- Lists all active authenticated devices/tokens

DELETE /api/v1/devices/{tokenId}
- Protected: Bearer token required
- Revokes specific device/token
```

---

## Part 7: Architecture Summary

### Token Storage & Retrieval

#### Mobile (React Native)
```
Storage: AsyncStorage (local device storage)
Keys:
  - @medconnect_token: The Bearer token
  - @medconnect_user: User object (JSON)

Retrieval:
  1. Retrieved automatically by request interceptor
  2. Added to Authorization header
  3. Tokens expire every ~2 hours (configurable)
```

#### Web (Blade/Session)
```
Storage: HTTP-only session cookies
Method: Laravel Sessions
Retrieval: Automatic via Laravel middleware
```

### Authentication Flow - Mobile

```
1. User submits login form
   POST /api/v1/login { email, password, device_name }
   ↓
2. Backend validates and returns token
   Response: { success, data: { user, token, expires_at } }
   ↓
3. Frontend stores token in AsyncStorage
   Keys: @medconnect_token, @medconnect_user
   ↓
4. Token added to all subsequent requests
   Request Interceptor: Authorization: Bearer {token}
   ↓
5. If 401 received:
   POST /api/v1/refresh { with current token }
   ↓
6. Backend returns new token
   Response: { success, data: { token, expires_at } }
   ↓
7. Update AsyncStorage with new token
```

### Authorization Header Format

```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

Components:
- "Bearer" literal string
- Space separator
- Authentication token (JWT or custom)
```

### Interceptor Chain

#### Request Flow
```
Outgoing Request
    ↓
Request Interceptor
  - Retrieve token from AsyncStorage
  - Add Authorization header
  - Return modified config
    ↓
Axios sends request with Authorization header
```

#### Response Flow
```
Backend Response
    ↓
Response Interceptor
  - Check status code
  - If 401:
    * Mark request for retry
    * Call /refresh endpoint
    * Get new token
    * Update AsyncStorage
    * Retry original request
  - If success: return response
  - If other error: reject
```

### Token Expiration Handling

#### Automatic Refresh on 401
- Triggered when backend returns 401 Unauthorized
- Calls `/refresh` endpoint with current token
- Updates AsyncStorage with new token
- Retries original request

#### Proactive Refresh
```javascript
isTokenExpiringSoon() // Check if < 2 hours remaining
  ↓
Call POST /api/v1/refresh proactively
  ↓
Update AsyncStorage with new token
```

---

## Part 8: API Response Format

### Success Login Response

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Nom Patient",
      "email": "patient@example.com",
      "phone": "+212612345678",
      "role": "user",
      "created_at": "2024-01-15T10:30:00Z"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer",
    "expires_at": "2024-01-16T10:30:00Z"
  }
}
```

### Token Refresh Response

```json
{
  "success": true,
  "message": "Token rafraîchi",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "Bearer"
  }
}
```

### Profile Response

```json
{
  "data": {
    "id": 1,
    "name": "Nom Patient",
    "email": "patient@example.com",
    "phone": "+212612345678",
    "role": "user",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

---

## Part 9: Key Files Reference

### React Native / Mobile API
- `REACT_NATIVE_AUTH_SERVICE.js` - Complete auth service
- `REACT_NATIVE_API_SERVICE_FIXED.js` - TypeScript version
- `API_MOBILE_DOCUMENTATION.md` - API docs
- `API_REACT_NATIVE_COMPLETE.md` - Complete React Native guide
- `REACT_NATIVE_SETUP.md` - Setup instructions

### Web Interface
- `resources/views/auth/login.blade.php` - Login form
- `resources/views/layouts/app.blade.php` - Layout with notifications
- `resources/js/bootstrap.js` - Axios bootstrap
- `resources/js/app.js` - Alpine.js startup
- `routes/api.php` - API route definitions

### Configuration
- `.env` - Environment variables (API_BASE_URL for mobile)
- `routes/api.php` - API route groups and middleware

---

## Part 10: Security Observations

### Token Storage
✅ **Good**: AsyncStorage for mobile (encrypted at OS level)  
⚠️ **Consider**: Token not stored in plain text  
✅ **Good**: Tokens stored with expiration time  

### Request Interceptor
✅ **Good**: Automatically adds Authorization header  
✅ **Good**: Uses Bearer token format  
✅ **Good**: Error handling during token retrieval  

### Response Interceptor
✅ **Good**: Handles 401 automatically  
✅ **Good**: Refreshes token before retry  
✅ **Good**: Clears auth on refresh failure  
✅ **Good**: Prevents infinite retry loops (retry flag)  

### Session Management
✅ **Good**: Supports multiple devices/tokens  
✅ **Good**: Can revoke individual devices  
✅ **Good**: Can revoke all devices at once  

### Web Interface
✅ **Good**: Uses HTTP-only session cookies  
✅ **Good**: No token visible in localStorage  
✅ **Good**: CSRF token included in forms  

---

## Part 11: Configuration Required

### For Mobile/React Native Apps

**.env file**:
```
# For local development
REACT_APP_API_URL=http://localhost:8000/api/v1

# For physical device on same network
REACT_APP_API_URL=http://192.168.1.100:8000/api/v1

# For Android emulator
REACT_APP_API_URL=http://10.0.2.2:8000/api/v1
```

### For Web Interface
No special configuration needed - uses Laravel sessions automatically.

---

## Summary Table

| Aspect | Mobile API | Web Interface |
|--------|-----------|---------------|
| **Auth Type** | Bearer Token | Session Cookies |
| **Storage** | AsyncStorage | HTTP-only Cookies |
| **Client** | Axios with interceptors | Fetch + Session |
| **Login Endpoint** | POST /api/v1/login | POST /login (web route) |
| **Token Passing** | Authorization header | Cookie (automatic) |
| **Token Refresh** | POST /api/v1/refresh | Automatic (Laravel) |
| **CSRF Protection** | Not applicable | Laravel middleware |
| **Device Management** | Via /devices endpoint | Not applicable |
| **Multiple Devices** | Supported | Not applicable |

---

## End of Frontend Authentication Audit

This document represents a complete audit of all frontend authentication code in the MedConnect application, including token storage, API integration, and request/response handling.
