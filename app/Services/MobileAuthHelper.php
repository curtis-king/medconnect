<?php

namespace App\Services;

/**
 * Mobile Authentication Helper
 *
 * Guide pour React Native - Gestion des tokens d'authentification
 *
 * Cette classe documente le flux d'authentification complet
 * à implémenter côté React Native.
 */
class MobileAuthHelper
{
    /**
     * Flow Complet d'Authentification
     *
     * 1. LOGIN
     * --------
     * POST /api/v1/login
     * Body: {
     *   "email": "patient@example.com",
     *   "password": "password123",
     *   "device_name": "iPhone 13 Pro"  // optionnel mais recommandé
     * }
     *
     * Response 200:
     * {
     *   "message": "Connexion réussie",
     *   "success": true,
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Ahmed Hassan",
     *       "email": "patient@example.com",
     *       "phone": "+212612345678",
     *       "role": "user",
     *       "avatar_url": null,
     *     },
     *     "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
     *     "token_type": "Bearer",
     *     "expires_in": 86400,  // 24 heures en secondes
     *     "expires_at": "2024-04-09T10:30:00Z"
     *   }
     * }
     *
     * ACTION REACT NATIVE:
     * - Sauvegarder token dans AsyncStorage
     * - Sauvegarder user data
     * - Sauvegarder expires_at pour tracking
     * - Initialiser token refresh timer
     *
     *
     * 2. STOCKAGE DU TOKEN
     * --------------------
     * import AsyncStorage from '@react-native-async-storage/async-storage';
     *
     * const storeAuthToken = async (token, expiresAt) => {
     *   try {
     *     await AsyncStorage.multiSet([
     *       ['auth_token', token],
     *       ['auth_token_expires_at', expiresAt],
     *       ['auth_user', JSON.stringify(user)],
     *     ]);
     *   } catch (error) {
     *     console.error('Error storing token:', error);
     *   }
     * };
     *
     *
     * 3. RÉCUPÉRER LE TOKEN A CHAQUE REQUÊTE
     * ----------------------------------------
     * const getAuthToken = async () => {
     *   try {
     *     return await AsyncStorage.getItem('auth_token');
     *   } catch (error) {
     *     console.error('Error retrieving token:', error);
     *     return null;
     *   }
     * };
     *
     *
     * 4. INTERCEPTEUR AXIOS POUR AJOUTER LE TOKEN
     * -----------------------------------------------
     * import axios from 'axios';
     *
     * const api = axios.create({
     *   baseURL: 'http://localhost:8000/api/v1',
     *   timeout: 30000,
     * });
     *
     * api.interceptors.request.use(async (config) => {
     *   const token = await getAuthToken();
     *   if (token) {
     *     config.headers.Authorization = `Bearer ${token}`;
     *   }
     *   return config;
     * });
     *
     *
     * 5. GESTION DES ERREURS 401 (Token Expiré)
     * -------------------------------------------
     * api.interceptors.response.use(
     *   (response) => response,
     *   async (error) => {
     *     const originalRequest = error.config;
     *
     *     if (error.response?.status === 401 && !originalRequest._retry) {
     *       originalRequest._retry = true;
     *
     *       try {
     *         // Tenter de rafraîchir le token
     *         const response = await api.post('/refresh');
     *         const newToken = response.data.data.token;
     *
     *         // Sauvegarder le nouveau token
     *         await AsyncStorage.setItem('auth_token', newToken);
     *
     *         // Réessayer la requête initiale
     *         originalRequest.headers.Authorization = `Bearer ${newToken}`;
     *         return api(originalRequest);
     *       } catch (refreshError) {
     *         // Le refresh a échoué - rediriger vers login
     *         await AsyncStorage.multiRemove([
     *           'auth_token',
     *           'auth_token_expires_at',
     *           'auth_user',
     *         ]);
     *         // Navigation.push('Login');
     *         return Promise.reject(refreshError);
     *       }
     *     }
     *
     *     return Promise.reject(error);
     *   }
     * );
     *
     *
     * 6. VÉRIFIER SI TOKEN EST VALIDE
     * ----------------------------------
     * const isTokenValid = async () => {
     *   try {
     *     const response = await api.get('/verify');
     *     return response.data.valid;
     *   } catch {
     *     return false;
     *   }
     * };
     *
     *
     * 7. RAFRAÎCHIR LE TOKEN PROACTIVEMENT
     * -------------------------------------
     * const refreshTokenProactively = async () => {
     *   try {
     *     const expiresAt = await AsyncStorage.getItem('auth_token_expires_at');
     *     const expiresAtDate = new Date(expiresAt);
     *     const now = new Date();
     *     const diffMs = expiresAtDate - now;
     *     const diffHours = diffMs / (1000 * 60 * 60);
     *
     *     // Si token expire dans moins de 2 heures, le rafraîchir
     *     if (diffHours < 2) {
     *       const response = await api.post('/refresh');
     *       const newToken = response.data.data.token;
     *       await AsyncStorage.setItem('auth_token', newToken);
     *       await AsyncStorage.setItem(
     *         'auth_token_expires_at',
     *         response.data.data.expires_at
     *       );
     *     }
     *   } catch (error) {
     *     console.error('Failed to refresh token:', error);
     *   }
     * };
     *
     * // Appeler toutes les 30 minutes
     * setInterval(refreshTokenProactively, 30 * 60 * 1000);
     *
     *
     * 8. LOGOUT
     * ---------
     * const logout = async () => {
     *   try {
     *     await api.post('/logout');
     *   } catch (error) {
     *     console.error('Logout error:', error);
     *   } finally {
     *     // Toujours nettoyer le stockage
     *     await AsyncStorage.multiRemove([
     *       'auth_token',
     *       'auth_token_expires_at',
     *       'auth_user',
     *     ]);
     *     // Navigation.reset({ routes: [{ name: 'Login' }] });
     *   }
     * };
     *
     *
     * 9. LOGOUT DE TOUS LES APPAREILS
     * --------------------------------
     * const logoutFromAllDevices = async () => {
     *   try {
     *     await api.post('/logout-all');
     *   } catch (error) {
     *     console.error('Logout all error:', error);
     *   } finally {
     *     await AsyncStorage.multiRemove([
     *       'auth_token',
     *       'auth_token_expires_at',
     *       'auth_user',
     *     ]);
     *   }
     * };
     *
     *
     * 10. LISTER LES APPAREILS ACTIFS
     * --------------------------------
     * const getActiveDevices = async () => {
     *   try {
     *     const response = await api.get('/devices');
     *     return response.data.devices;
     *   } catch (error) {
     *     console.error('Error fetching devices:', error);
     *     return [];
     *   }
     * };
     *
     *
     * 11. RÉVOQUER UN APPAREIL
     * ------------------------
     * const revokeDevice = async (tokenId) => {
     *   try {
     *     await api.delete(`/devices/${tokenId}`);
     *   } catch (error) {
     *     console.error('Error revoking device:', error);
     *   }
     * };
     *
     *
     * STRUCTURE COMPLÈTE REACT NATIVE
     * ================================
     *
     * // authService.js
     * import axios from 'axios';
     * import AsyncStorage from '@react-native-async-storage/async-storage';
     *
     * const API_BASE = 'http://localhost:8000/api/v1';
     *
     * const api = axios.create({
     *   baseURL: API_BASE,
     *   timeout: 30000,
     * });
     *
     * // Intercepteur Requête
     * api.interceptors.request.use(async (config) => {
     *   const token = await AsyncStorage.getItem('auth_token');
     *   if (token) {
     *     config.headers.Authorization = `Bearer ${token}`;
     *   }
     *   return config;
     * });
     *
     * // Intercepteur Réponse
     * api.interceptors.response.use(
     *   (response) => response,
     *   async (error) => {
     *     const originalRequest = error.config;
     *
     *     if (error.response?.status === 401 && !originalRequest._retry) {
     *       originalRequest._retry = true;
     *
     *       try {
     *         const response = await api.post('/refresh');
     *         const newToken = response.data.data.token;
     *         await AsyncStorage.setItem('auth_token', newToken);
     *         originalRequest.headers.Authorization = `Bearer ${newToken}`;
     *         return api(originalRequest);
     *       } catch {
     *         await AsyncStorage.multiRemove([
     *           'auth_token',
     *           'auth_token_expires_at',
     *           'auth_user',
     *         ]);
     *         // Navigation.goBack();
     *       }
     *     }
     *
     *     return Promise.reject(error);
     *   }
     * );
     *
     * export const authService = {
     *   login: (email, password, deviceName = 'mobile-app') =>
     *     api.post('/login', { email, password, device_name: deviceName }),
     *
     *   logout: () => api.post('/logout'),
     *
     *   me: () => api.get('/me'),
     *
     *   refresh: () => api.post('/refresh'),
     *
     *   verify: () => api.get('/verify'),
     *
     *   devices: () => api.get('/devices'),
     *
     *   revokeDevice: (tokenId) => api.delete(`/devices/${tokenId}`),
     *
     *   logoutAll: () => api.post('/logout-all'),
     * };
     *
     * export const storeToken = async (user, token, expiresAt) => {
     *   await AsyncStorage.multiSet([
     *     ['auth_token', token],
     *     ['auth_token_expires_at', expiresAt],
     *     ['auth_user', JSON.stringify(user)],
     *   ]);
     * };
     *
     * export const getStoredToken = async () => {
     *   return await AsyncStorage.getItem('auth_token');
     * };
     *
     * export const clearAuth = async () => {
     *   await AsyncStorage.multiRemove([
     *     'auth_token',
     *     'auth_token_expires_at',
     *     'auth_user',
     *   ]);
     * };
     *
     * export default api;
     */
    public static function getDocumentation(): string
    {
        return 'Mobile Authentication Flow Documentation';
    }
}
