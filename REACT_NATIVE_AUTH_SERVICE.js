/**
 * authService.js - Authentication Service pour React Native
 * 
 * À placer dans: src/services/authService.js
 * 
 * Cette service gère COMPLÈTEMENT l'authentification avec le backend Laravel
 * - Token storage/retrieval
 * - Auto-refresh avant expiration
 * - Gestion d'erreurs 401
 * - Device tracking
 */

import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

// ========== CONFIGURATION ==========
// IMPORTANT: L'URL doit inclure /api/v1 - c'est critique pour que les endpoints soient corrects
// Ne pas utiliser: 'http://localhost:8000' ou 'http://localhost:8000/api'
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api/v1';
const TOKEN_STORAGE_KEY = 'auth_token';
const TOKEN_EXPIRES_KEY = 'auth_token_expires_at';
const USER_STORAGE_KEY = 'auth_user';

// ========== CRÉATION API CLIENT ==========
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// ========== INTERCEPTEUR REQUÊTE ==========
// Ajoute le token à chaque requête
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

// ========== INTERCEPTEUR RÉPONSE ==========
// Gère les erreurs 401 avec refresh automatique
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    // Si 401 et pas déjà en train de réessayer
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        console.log('Token expiré, tentative de rafraîchissement...');
        
        // Récupérer le token actuel pour la requête de refresh
        const currentToken = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
        const refreshConfig = {
          ...api.defaults,
          headers: {
            ...api.defaults.headers,
            Authorization: `Bearer ${currentToken}`,
          },
        };

        // Appeler le endpoint de refresh
        const response = await axios.post(
          `${API_BASE_URL}/refresh`,
          {},
          refreshConfig
        );

        if (response.data.success && response.data.data.token) {
          const { token, expires_at } = response.data.data;

          // Sauvegarder le nouveau token
          await AsyncStorage.multiSet([
            [TOKEN_STORAGE_KEY, token],
            [TOKEN_EXPIRES_KEY, expires_at],
          ]);

          console.log('Token rafraîchi avec succès');

          // Réessayer la requête initiale avec le nouveau token
          originalRequest.headers.Authorization = `Bearer ${token}`;
          return api(originalRequest);
        }
      } catch (refreshError) {
        console.error('Impossible de rafraîchir le token:', refreshError.message);
        
        // Le refresh a échoué - nettoyer l'auth
        await clearAuth();
        
        // Rediriger vers login si possible
        // À adapter selon votre navigation
        // authStore.resetAuth(); // ou navigation.reset(...)
      }
    }

    return Promise.reject(error);
  }
);

// ========== FONCTIONS UTILITAIRES ==========

/**
 * Sauvegarder le token et les données utilisateur
 */
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

/**
 * Récupérer le token
 */
const getStoredToken = async () => {
  try {
    return await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
  } catch (error) {
    console.error('Error retrieving token:', error);
    return null;
  }
};

/**
 * Récupérer l'utilisateur stocké
 */
const getStoredUser = async () => {
  try {
    const userJson = await AsyncStorage.getItem(USER_STORAGE_KEY);
    return userJson ? JSON.parse(userJson) : null;
  } catch (error) {
    console.error('Error retrieving user:', error);
    return null;
  }
};

/**
 * Vérifier si le token va bientôt expirer
 */
const isTokenExpiringSoon = async () => {
  try {
    const expiresAt = await AsyncStorage.getItem(TOKEN_EXPIRES_KEY);
    if (!expiresAt) return true;

    const expiresAtDate = new Date(expiresAt);
    const now = new Date();
    const diffMs = expiresAtDate - now;
    const diffHours = diffMs / (1000 * 60 * 60);

    // Retourner true si expire dans moins de 2 heures
    return diffHours < 2;
  } catch (error) {
    console.error('Error checking token expiration:', error);
    return true;
  }
};

/**
 * Rafraîchir le token proactivement
 */
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

/**
 * Nettoyer les données d'authentification
 */
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

/**
 * Vérifier si l'utilisateur est authentifié
 */
const isAuthenticated = async () => {
  try {
    const token = await AsyncStorage.getItem(TOKEN_STORAGE_KEY);
    return !!token;
  } catch (error) {
    console.error('Error checking authentication:', error);
    return false;
  }
};

// ========== API D'AUTHENTIFICATION ==========

export const authService = {
  /**
   * LOGIN
   * POST /login
   */
  login: async (email, password, deviceName = 'ReactNativeApp') => {
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
  },

  /**
   * LOGOUT
   * POST /logout
   */
  logout: async () => {
    try {
      await api.post('/logout');
      await clearAuth();
      return { success: true };
    } catch (error) {
      console.error('Logout error:', error);
      // Nettoyer quand même
      await clearAuth();
      return { success: false };
    }
  },

  /**
   * LOGOUT DE TOUS LES APPAREILS
   * POST /logout-all
   */
  logoutFromAllDevices: async () => {
    try {
      await api.post('/logout-all');
      await clearAuth();
      return { success: true };
    } catch (error) {
      console.error('Logout all error:', error);
      await clearAuth();
      return { success: false };
    }
  },

  /**
   * VÉRIFIER L'UTILISATEUR ACTUEL
   * GET /me
   */
  getCurrentUser: async () => {
    try {
      const response = await api.get('/me');
      if (response.data.success) {
        const user = response.data.user;
        // Mettre à jour les données locales
        await AsyncStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user));
        return user;
      }
    } catch (error) {
      console.error('Get current user error:', error);
    }
    return null;
  },

  /**
   * VÉRIFIER SI LE TOKEN EST VALIDE
   * GET /verify
   */
  verifyToken: async () => {
    try {
      const response = await api.get('/verify');
      return response.data.valid === true;
    } catch (error) {
      console.error('Token verification error:', error);
      return false;
    }
  },

  /**
   * RAFRAÎCHIR LE TOKEN
   * POST /refresh
   */
  refreshToken: async () => {
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
  },

  /**
   * LISTER LES APPAREILS ACTIFS
   * GET /devices
   */
  getActiveDevices: async () => {
    try {
      const response = await api.get('/devices');
      return response.data.devices || [];
    } catch (error) {
      console.error('Get devices error:', error);
      return [];
    }
  },

  /**
   * RÉVOQUER UN APPAREIL
   * DELETE /devices/{tokenId}
   */
  revokeDevice: async (tokenId) => {
    try {
      await api.delete(`/devices/${tokenId}`);
      return { success: true };
    } catch (error) {
      console.error('Revoke device error:', error);
      return { success: false };
    }
  },
};

// ========== EXPORTS UTILITAIRES ==========

export {
  api,
  getStoredToken,
  getStoredUser,
  storeAuthData,
  clearAuth,
  isAuthenticated,
  isTokenExpiringSoon,
  refreshTokenProactively,
};

// ========== EXEMPLE D'UTILISATION ==========

/*
// Dans votre composant de login
import { authService, storeAuthData } from '../services/authService';

const handleLogin = async () => {
  try {
    const result = await authService.login(email, password, 'My iPhone');
    
    if (result.success) {
      console.log('Utilisateur connecté:', result.user);
      navigation.reset({
        routes: [{ name: 'Home' }]
      });
    } else {
      Alert.alert('Erreur', result.message);
    }
  } catch (error) {
    Alert.alert('Erreur', 'Une erreur est survenue');
  }
};

// Dans votre App.js pour vérifier l'authentification au démarrage
import { isAuthenticated, getStoredUser } from '../services/authService';

useEffect(() => {
  const checkAuth = async () => {
    const authenticated = await isAuthenticated();
    const user = await getStoredUser();
    
    if (authenticated && user) {
      navigation.reset({
        routes: [{ name: 'Home' }]
      });
    } else {
      navigation.reset({
        routes: [{ name: 'Login' }]
      });
    }
  };
  
  checkAuth();
}, []);

// Pour rafraîchir le token toutes les 30 minutes
import { refreshTokenProactively } from '../services/authService';

useEffect(() => {
  const interval = setInterval(refreshTokenProactively, 30 * 60 * 1000);
  return () => clearInterval(interval);
}, []);
*/

export default authService;
