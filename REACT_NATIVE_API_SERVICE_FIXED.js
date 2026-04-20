// === SERVICE API POUR AUTHENTIFICATION ===
// Ce fichier gère toutes les requêtes API pour l'authentification avec Laravel

import { API_BASE_URL } from "@/config";
import AsyncStorage from "@react-native-async-storage/async-storage";
import axios, { AxiosInstance } from "axios";

// URL de base de l'API Laravel - configured in config.ts

// === CLÉS CONSISTANTES POUR AsyncStorage ===
const STORAGE_KEYS = {
  TOKEN: "@medconnect_token",
  USER: "@medconnect_user",
};

// Création d'une instance axios avec configuration
const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Intercepteur: ajoute le token à chaque requête
apiClient.interceptors.request.use(
  async (config) => {
    try {
      // Récupère le token depuis AsyncStorage avec la bonne clé
      const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
      if (token) {
        // Ajoute le token au header Authorization avec le format "Bearer TOKEN"
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

// Intercepteur réponse pour gérer les erreurs
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      console.log("❌ 401 Unauthorized - Token invalide ou expiré");
      // Nettoie le stockage
      await AsyncStorage.removeItem(STORAGE_KEYS.TOKEN);
      await AsyncStorage.removeItem(STORAGE_KEYS.USER);
    }
    return Promise.reject(error);
  },
);

// Exporte apiClient pour l'utiliser dans les autres services
export { apiClient };

// === INTERFACE POUR TYPER LES RÉPONSES ===
interface User {
  id: number;
  name: string;
  email: string;
  phone?: string | null;
  role: string;
}

interface LoginResponse {
  success: boolean;
  message: string;
  data: {
    user: User;
    token: string;
    token_type: string;
    expires_in: number;
    expires_at: string;
  };
}

// === FONCTIONS D'AUTHENTIFICATION ===

/**
 * Connexion - Envoie email + password à Laravel
 * @param email - Email de l'utilisateur
 * @param password - Mot de passe de l'utilisateur
 * @param deviceName - Nom du device (optionnel)
 * @returns Token et informations utilisateur
 */
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

    // Le backend retourne: { success, message, data: { user, token, ... } }
    const loginData = response.data.data || response.data;
    const { user, token } = loginData;

    if (!token || !user) {
      throw new Error("Token ou utilisateur manquant dans la réponse");
    }

    // Stocke le token et l'utilisateur avec les MÊMES clés
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

/**
 * Déconnexion - Logout l'utilisateur
 */
export const logout = async (): Promise<void> => {
  try {
    console.log("🔄 Déconnexion en cours...");

    // Appel POST à /api/v1/logout
    await apiClient.post("/v1/logout");

    // Supprime le token et l'utilisateur du stockage
    await AsyncStorage.multiRemove([STORAGE_KEYS.TOKEN, STORAGE_KEYS.USER]);

    console.log("✅ Déconnexion réussie");
  } catch (error) {
    console.error("❌ Erreur lors de la déconnexion:", error);
    // Même en cas d'erreur, supprime les données locales
    await AsyncStorage.multiRemove([STORAGE_KEYS.TOKEN, STORAGE_KEYS.USER]);
  }
};

/**
 * Récupère le token stocké
 * @returns Le token ou null
 */
export const getToken = async (): Promise<string | null> => {
  try {
    // UTILISE LA BONNE CLÉ!
    const token = await AsyncStorage.getItem(STORAGE_KEYS.TOKEN);
    console.log(token ? "✅ Token trouvé" : "⚠️ Pas de token");
    return token;
  } catch (error) {
    console.error("❌ Erreur lors de la récupération du token:", error);
    return null;
  }
};

/**
 * Récupère l'utilisateur actuel
 * @returns L'objet utilisateur ou null
 */
export const getCurrentUser = async (): Promise<User | null> => {
  try {
    // UTILISE LA BONNE CLÉ!
    const userJson = await AsyncStorage.getItem(STORAGE_KEYS.USER);
    const user = userJson ? JSON.parse(userJson) : null;
    console.log(user ? "✅ Utilisateur trouvé" : "⚠️ Pas d'utilisateur");
    return user;
  } catch (error) {
    console.error("❌ Erreur lors de la récupération de l'utilisateur:", error);
    return null;
  }
};

/**
 * Vérifie si l'utilisateur est authentifié
 * @returns true si un token existe, false sinon
 */
export const isAuthenticated = async (): Promise<boolean> => {
  const token = await getToken();
  return !!token;
};

/**
 * Récupère le profil actuel depuis l'API
 * @returns Les infos du profil utilisateur
 */
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

export default apiClient;
