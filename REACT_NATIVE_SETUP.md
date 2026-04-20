# 🚀 Implémentation React Native - Setup Complet

## 📦 Step 1: Créer le projet React Native

### Option A: Expo (Plus facile pour débuter)
```bash
npx create-expo-app ZerMobileApp
cd ZerMobileApp
npm install axios @react-native-async-storage/async-storage
npm install @react-navigation/native @react-navigation/bottom-tabs
npm install react-native-screens react-native-safe-area-context
```

### Option B: React Native CLI (Plus contrôle)
```bash
npx react-native init ZerMobileApp --version 0.73.0
cd ZerMobileApp
npm install axios @react-native-async-storage/async-storage
npm install @react-navigation/native @react-navigation/native-stack
npm install react-native-screens react-native-safe-area-context
```

---

## 📁 Step 2: Structure Dossiers

```
ZerMobileApp/
├── src/
│   ├── services/
│   │   ├── authService.js           # ⬅️ Copier de REACT_NATIVE_AUTH_SERVICE.js
│   │   └── apiService.js             # Axios instance
│   ├── screens/
│   │   ├── LoginScreen.js
│   │   ├── HomeScreen.js
│   │   ├── ProfileScreen.js
│   │   └── DevicesScreen.js
│   ├── contexts/
│   │   └── AuthContext.js            # State management
│   ├── navigation/
│   │   └── Navigation.js             # Routing
│   └── components/
│       ├── LoadingScreen.js
│       └── ErrorAlert.js
├── App.js                             # Entry point
├── .env                               # API_URL
└── package.json
```

---

## 🔧 Step 3: Fichier d'Environnement

### Créer `.env`
```env
API_BASE_URL=http://192.168.1.100:8000
# ou pour émulateur Android:
# API_BASE_URL=http://10.0.2.2:8000
# ou pour MacOS:
# API_BASE_URL=http://localhost:8000
```

### Installer dotenv
```bash
npm install dotenv
```

---

## 🛠️ Step 4: Copier authService.js

```bash
# 1. Copier le fichier depuis le projet Laravel
cp ../zer/REACT_NATIVE_AUTH_SERVICE.js src/services/authService.js

# 2. Adapter l'URL (inclure /api/v1)
# Dans src/services/authService.js, changer:
# const API_BASE_URL = 'http://localhost:8000/api/v1';
# À:
# const API_BASE_URL = process.env.API_BASE_URL || 'http://localhost:8000/api/v1';
```

---

## 📱 Step 5: AuthContext pour State Management

### Fichier: `src/contexts/AuthContext.js`
```javascript
import React, { createContext, useState, useEffect } from 'react';
import authService from '../services/authService';
import AsyncStorage from '@react-native-async-storage/async-storage';

export const AuthContext = createContext({});

export const AuthProvider = ({ children }) => {
  const [state, dispatch] = React.useReducer(
    (prevState, action) => {
      switch (action.type) {
        case 'RESTORE_TOKEN':
          return {
            ...prevState,
            userToken: action.token,
            user: action.user,
            isLoading: false,
          };
        case 'SIGN_IN':
          return {
            ...prevState,
            isSignout: false,
            userToken: action.token,
            user: action.user,
          };
        case 'SIGN_OUT':
          return {
            ...prevState,
            isSignout: true,
            userToken: null,
            user: null,
          };
        case 'SIGN_UP':
          return {
            ...prevState,
            isSignout: false,
            userToken: action.token,
            user: action.user,
          };
      }
    },
    {
      isLoading: true,
      isSignout: false,
      userToken: null,
      user: null,
    }
  );

  useEffect(() => {
    const bootstrapAsync = async () => {
      try {
        const token = await AsyncStorage.getItem('auth_token');
        const user = await AsyncStorage.getItem('auth_user');

        dispatch({
          type: 'RESTORE_TOKEN',
          token,
          user: user ? JSON.parse(user) : null,
        });
      } catch (e) {
        console.error('Failed to restore token:', e);
      }
    };

    bootstrapAsync();
  }, []);

  const authContext = {
    sign_in: async (email, password) => {
      try {
        const result = await authService.login(
          email,
          password,
          'ReactNativeApp'
        );

        if (result.success) {
          dispatch({
            type: 'SIGN_IN',
            token: result.token,
            user: result.user,
          });
          return result;
        }
        throw new Error(result.message);
      } catch (error) {
        throw error;
      }
    },

    sign_up: async (name, email, password) => {
      // À implémenter selon votre système d'inscription
      try {
        // Appel API sign-up
        return { success: true };
      } catch (error) {
        throw error;
      }
    },

    sign_out: async () => {
      try {
        await authService.logout();
        dispatch({ type: 'SIGN_OUT' });
      } catch (error) {
        console.error('Logout error:', error);
      }
    },

    sign_up: async (email, password) => {
      // À implémenter
    },

    refresh: async () => {
      try {
        const result = await authService.refreshToken();
        if (result.success) {
          dispatch({
            type: 'SIGN_IN',
            token: result.token,
            user: state.user, // Le user ne change généralement pas
          });
        }
      } catch (error) {
        console.error('Refresh error:', error);
      }
    },
  };

  return (
    <AuthContext.Provider value={{ state, ...authContext }}>
      {children}
    </AuthContext.Provider>
  );
};
```

---

## 🔐 Step 6: LoginScreen

### Fichier: `src/screens/LoginScreen.js`
```javascript
import React, { useState, useContext } from 'react';
import {
  View,
  TextInput,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  Text,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { AuthContext } from '../contexts/AuthContext';

export const LoginScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  const { sign_in } = useContext(AuthContext);

  const validateForm = () => {
    const newErrors = {};

    if (!email) newErrors.email = 'Email requis';
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
      newErrors.email = 'Email invalide';
    }

    if (!password) newErrors.password = 'Password requis';
    if (password.length < 6) {
      newErrors.password = 'Min 6 caractères';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleLogin = async () => {
    if (!validateForm()) return;

    setLoading(true);
    try {
      const result = await sign_in(email, password);

      if (result.success) {
        // Navigation automatique via AuthContext
        Alert.alert('Succès', `Bienvenue ${result.user.name}`);
      }
    } catch (error) {
      Alert.alert(
        'Erreur de connexion',
        error.message || 'Erreur inconnue'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
      style={styles.container}
    >
      <View style={styles.innerContainer}>
        <Text style={styles.title}>Zer</Text>
        <Text style={styles.subtitle}>Application Médicale</Text>

        {/* Email Input */}
        <TextInput
          placeholder="Email"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          autoCapitalize="none"
          editable={!loading}
          style={[styles.input, errors.email && styles.inputError]}
          placeholderTextColor="#999"
        />
        {errors.email && (
          <Text style={styles.errorText}>{errors.email}</Text>
        )}

        {/* Password Input */}
        <TextInput
          placeholder="Mot de passe"
          value={password}
          onChangeText={setPassword}
          secureTextEntry
          editable={!loading}
          style={[styles.input, errors.password && styles.inputError]}
          placeholderTextColor="#999"
        />
        {errors.password && (
          <Text style={styles.errorText}>{errors.password}</Text>
        )}

        {/* Login Button */}
        <TouchableOpacity
          onPress={handleLogin}
          disabled={loading}
          style={[
            styles.button,
            loading && styles.buttonDisabled,
          ]}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.buttonText}>Se connecter</Text>
          )}
        </TouchableOpacity>

        {/* Forgot Password */}
        <TouchableOpacity onPress={() => Alert.alert('À implémenter')}>
          <Text style={styles.forgotPassword}>Mot de passe oublié?</Text>
        </TouchableOpacity>

        {/* Sign Up */}
        <View style={styles.signupContainer}>
          <Text style={styles.signupText}>Pas encore inscrit? </Text>
          <TouchableOpacity onPress={() => Alert.alert('À implémenter')}>
            <Text style={styles.signupLink}>S'inscrire</Text>
          </TouchableOpacity>
        </View>
      </View>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  innerContainer: {
    flex: 1,
    padding: 20,
    justifyContent: 'center',
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#007AFF',
    marginBottom: 10,
    textAlign: 'center',
  },
  subtitle: {
    fontSize: 14,
    color: '#666',
    marginBottom: 40,
    textAlign: 'center',
  },
  input: {
    borderWidth: 1,
    borderColor: '#ddd',
    borderRadius: 8,
    padding: 12,
    marginBottom: 15,
    backgroundColor: '#fff',
    fontSize: 16,
  },
  inputError: {
    borderColor: '#ff3b30',
  },
  errorText: {
    color: '#ff3b30',
    fontSize: 12,
    marginBottom: 10,
    marginTop: -10,
  },
  button: {
    backgroundColor: '#007AFF',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  forgotPassword: {
    color: '#007AFF',
    textAlign: 'center',
    marginTop: 15,
  },
  signupContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 30,
  },
  signupText: {
    color: '#666',
  },
  signupLink: {
    color: '#007AFF',
    fontWeight: 'bold',
  },
});
```

---

## 🏠 Step 7: HomeScreen

### Fichier: `src/screens/HomeScreen.js`
```javascript
import React, { useContext, useState, useEffect } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  StyleSheet,
  Alert,
} from 'react-native';
import { AuthContext } from '../contexts/AuthContext';
import authService from '../services/authService';

export const HomeScreen = ({ navigation }) => {
  const { state, sign_out } = useContext(AuthContext);
  const [loading, setLoading] = useState(false);

  const handleLogout = async () => {
    Alert.alert(
      'Déconnexion',
      'Êtes-vous sûr?',
      [
        { text: 'Annuler', onPress: () => {} },
        {
          text: 'Déconnecter',
          onPress: async () => {
            await sign_out();
          },
        },
      ]
    );
  };

  const handleLogoutAll = async () => {
    Alert.alert(
      'Déconnecter tous les appareils',
      'Cela déconnectera cet appareil ET tous les autres',
      [
        { text: 'Annuler', onPress: () => {} },
        {
          text: 'Déconnecter tous',
          onPress: async () => {
            setLoading(true);
            try {
              const result = await authService.logoutAll();
              if (result.success) {
                Alert.alert('Succès', 'Déconnecté de tous les appareils');
                await sign_out();
              }
            } catch (error) {
              Alert.alert('Erreur', error.message);
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Accueil</Text>
      </View>

      {/* User Info */}
      {state.user && (
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Profil</Text>
          <View style={styles.infoRow}>
            <Text style={styles.label}>Nom:</Text>
            <Text style={styles.value}>{state.user.name}</Text>
          </View>
          <View style={styles.infoRow}>
            <Text style={styles.label}>Email:</Text>
            <Text style={styles.value}>{state.user.email}</Text>
          </View>
          <View style={styles.infoRow}>
            <Text style={styles.label}>Téléphone:</Text>
            <Text style={styles.value}>{state.user.phone || 'N/A'}</Text>
          </View>
        </View>
      )}

      {/* Quick Actions */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Actions</Text>

        <TouchableOpacity
          style={styles.actionButton}
          onPress={() => navigation.navigate('Devices')}
        >
          <Text style={styles.actionButtonText}>📱 Gérer les appareils</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.actionButton}
          onPress={() => navigation.navigate('Profile')}
        >
          <Text style={styles.actionButtonText}>👤 Modifier profil</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.actionButton, styles.warningButton]}
          onPress={handleLogout}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.actionButtonText}>🚪 Déconnecter</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.actionButton, styles.dangerButton]}
          onPress={handleLogoutAll}
          disabled={loading}
        >
          <Text style={styles.actionButtonText}>
            ⚠️ Déconnecter tous les appareils
          </Text>
        </TouchableOpacity>
      </View>

      {/* API Status */}
      <View style={styles.card}>
        <Text style={styles.cardTitle}>Status API</Text>
        <View style={styles.infoRow}>
          <Text style={styles.label}>Base URL:</Text>
          <Text style={styles.value}>
            {process.env.API_BASE_URL || 'http://localhost:8000'}
          </Text>
        </View>
        <View style={styles.infoRow}>
          <Text style={styles.label}>Token:</Text>
          <Text style={[styles.value, { fontSize: 10 }]}>
            {state.userToken?.substring(0, 20)}...
          </Text>
        </View>
      </View>
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    backgroundColor: '#007AFF',
    padding: 20,
    paddingTop: 40,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#fff',
  },
  card: {
    backgroundColor: '#fff',
    margin: 15,
    padding: 15,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
    color: '#333',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 10,
    paddingBottom: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  label: {
    color: '#666',
    fontWeight: '600',
  },
  value: {
    color: '#333',
    fontWeight: '500',
  },
  actionButton: {
    backgroundColor: '#007AFF',
    padding: 12,
    borderRadius: 8,
    marginVertical: 8,
    alignItems: 'center',
  },
  actionButtonText: {
    color: '#fff',
    fontWeight: '600',
    fontSize: 14,
  },
  warningButton: {
    backgroundColor: '#FF9500',
  },
  dangerButton: {
    backgroundColor: '#ff3b30',
  },
});
```

---

## 📱 Step 8: DevicesScreen

### Fichier: `src/screens/DevicesScreen.js`
```javascript
import React, { useContext, useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  StyleSheet,
  Alert,
} from 'react-native';
import { AuthContext } from '../contexts/AuthContext';
import authService from '../services/authService';

export const DevicesScreen = () => {
  const { state } = useContext(AuthContext);
  const [devices, setDevices] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadDevices();
  }, []);

  const loadDevices = async () => {
    setLoading(true);
    try {
      const response = await authService.api.get('/devices');
      setDevices(response.data.devices || []);
    } catch (error) {
      Alert.alert('Erreur', 'Impossible de charger les appareils');
    } finally {
      setLoading(false);
    }
  };

  const handleRevokeDevice = (deviceId, deviceName) => {
    Alert.alert(
      'Révoquer l\'appareil',
      `Êtes-vous sûr de vouloir déconnecter "${deviceName}"?`,
      [
        { text: 'Annuler', onPress: () => {} },
        {
          text: 'Révoquer',
          onPress: async () => {
            try {
              await authService.api.delete(`/devices/${deviceId}`);
              setDevices(devices.filter(d => d.id !== deviceId));
              Alert.alert('Succès', 'Appareil révoqué');
            } catch (error) {
              Alert.alert('Erreur', 'Impossible de révoquer l\'appareil');
            }
          },
        },
      ]
    );
  };

  if (loading) {
    return (
      <View style={[styles.container, { justifyContent: 'center' }]}>
        <ActivityIndicator size="large" color="#007AFF" />
      </View>
    );
  }

  return (
    <ScrollView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Appareils Connectés</Text>
        <Text style={styles.subtitle}>
          {devices.length} appareil{devices.length > 1 ? 's' : ''}
        </Text>
      </View>

      {devices.map((device) => (
        <View key={device.id} style={styles.deviceCard}>
          <View style={styles.deviceHeader}>
            <Text style={styles.deviceName}>{device.device_name}</Text>
            {device.is_current && (
              <View style={styles.currentBadge}>
                <Text style={styles.currentText}>Actuel</Text>
              </View>
            )}
          </View>

          <Text style={styles.deviceDate}>
            Créé: {new Date(device.created_at).toLocaleDateString('fr-FR')}
          </Text>
          <Text style={styles.deviceDate}>
            Utilisé:
            {new Date(device.last_used_at).toLocaleDateString('fr-FR')}
          </Text>

          {!device.is_current && (
            <TouchableOpacity
              style={styles.revokeButton}
              onPress={() => handleRevokeDevice(device.id, device.device_name)}
            >
              <Text style={styles.revokeButtonText}>Révoquer</Text>
            </TouchableOpacity>
          )}
        </View>
      ))}

      {devices.length === 0 && (
        <View style={styles.emptyState}>
          <Text style={styles.emptyText}>Aucun appareil trouvé</Text>
        </View>
      )}
    </ScrollView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    backgroundColor: '#007AFF',
    padding: 20,
    paddingTop: 40,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#fff',
  },
  subtitle: {
    color: 'rgba(255,255,255,0.8)',
    marginTop: 5,
  },
  deviceCard: {
    backgroundColor: '#fff',
    margin: 15,
    padding: 15,
    borderRadius: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  deviceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  deviceName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#333',
  },
  currentBadge: {
    backgroundColor: '#34C759',
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 20,
  },
  currentText: {
    color: '#fff',
    fontSize: 12,
    fontWeight: '600',
  },
  deviceDate: {
    color: '#999',
    fontSize: 12,
    marginBottom: 5,
  },
  revokeButton: {
    backgroundColor: '#ff3b30',
    padding: 10,
    borderRadius: 8,
    marginTop: 10,
    alignItems: 'center',
  },
  revokeButtonText: {
    color: '#fff',
    fontWeight: '600',
  },
  emptyState: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    minHeight: 200,
  },
  emptyText: {
    color: '#999',
    fontSize: 16,
  },
});
```

---

## 🧭 Step 9: Navigation

### Fichier: `src/navigation/Navigation.js`
```javascript
import React, { useContext } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';

import { AuthContext } from '../contexts/AuthContext';
import { LoginScreen } from '../screens/LoginScreen';
import { HomeScreen } from '../screens/HomeScreen';
import { ProfileScreen } from '../screens/ProfileScreen';
import { DevicesScreen } from '../screens/DevicesScreen';
import { LoadingScreen } from '../components/LoadingScreen';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

function HomeStack() {
  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
      }}
    >
      <Stack.Screen name="HomeTab" component={HomeScreen} />
      <Stack.Screen name="Profile" component={ProfileScreen} />
      <Stack.Screen name="Devices" component={DevicesScreen} />
    </Stack.Navigator>
  );
}

function AuthStack() {
  return (
    <Stack.Navigator screenOptions={{ headerShown: false }}>
      <Stack.Screen name="Login" component={LoginScreen} />
    </Stack.Navigator>
  );
}

function RootNavigator() {
  const { state } = useContext(AuthContext);

  if (state.isLoading) {
    return <LoadingScreen />;
  }

  return (
    <NavigationContainer>
      {state.userToken == null ? <AuthStack /> : <HomeStack />}
    </NavigationContainer>
  );
}

export default RootNavigator;
```

---

## 🎯 Step 10: App.js Principal

### Fichier: `App.js`
```javascript
import React from 'react';
import { AuthProvider } from './src/contexts/AuthContext';
import RootNavigator from './src/navigation/Navigation';

export default function App() {
  return (
    <AuthProvider>
      <RootNavigator />
    </AuthProvider>
  );
}
```

---

## 🚀 Step 11: Lancer l'Application

### Expo
```bash
npm start
# Scanne QR code avec appareil
```

### React Native CLI - Android
```bash
# Terminal 1: Metro bundler
npm start

# Terminal 2: Lancer app
npm run android
```

### React Native CLI - iOS
```bash
npm run ios
```

---

## 📋 Checklist Implémentation

- [ ] Structure dossiers créée
- [ ] authService.js copié et adapté
- [ ] AuthContext créé
- [ ] LoginScreen implémenté
- [ ] HomeScreen implémenté
- [ ] Navigation configurée
- [ ] App.js mis à jour
- [ ] Dépendances installées
- [ ] .env crée avec API_URL
- [ ] Application lance sans erreur
- [ ] Login fonctionne
- [ ] Token sauvegardé dans AsyncStorage
- [ ] Refresh token automatique functions
- [ ] Logout fonctionne
- [ ] Devices listing affiche appareils
- [ ] Revoke device fonctionne

---

## 🧪 Test Rapide

1. **Lancer app:**
   ```bash
   npm start
   ```

2. **Connexion:**
   - Email: `test@example.com`
   - Password: `password123`

3. **Vérifier:**
   - Token sauvegardé dans AsyncStorage
   - Profil s'affiche
   - Appareils listés
   - Logout fonctionne

---

**Status:** ✅ Setup Complet  
**Prochaine étape:** Intégrer les endpoints API pour dossiers patients, RDVs, etc.
