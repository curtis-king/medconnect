import {
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  View,
  TextInput,
  ActivityIndicator,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useState, useEffect } from "react";
import { useRouter } from "expo-router";

import { ThemedText } from "@/components/themed-text";
import { ThemedView } from "@/components/themed-view";

// ✅ IMPORTANT: Utiliser apiClient (axios) au lieu de fetch
import apiClient, { logout } from "@/services/authService";
import { getToken, getUser, clearAuth } from "@/services/storageService";

export default function ProfileScreen() {
  const [user, setUser] = useState(null);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [loading, setLoading] = useState(false);
  const [initialLoading, setInitialLoading] = useState(true);
  const [error, setError] = useState("");
  const router = useRouter();

  // ✅ Charge les données utilisateur depuis AsyncStorage au démarrage
  useEffect(() => {
    const loadUserData = async () => {
      try {
        setInitialLoading(true);
        
        // Vérifier si l'utilisateur est authentifié
        const token = await getToken();
        if (!token) {
          console.warn("Pas de token trouvé - redirection vers login");
          router.push("/login");
          return;
        }

        // Charger les données utilisateur stockées
        const storedUser = await getUser();
        if (storedUser) {
          setUser(storedUser);
          setName(storedUser.name || "");
          setEmail(storedUser.email || "");
          setPhone(storedUser.phone || "");
        }
      } catch (err) {
        console.error("Erreur au chargement du profil:", err);
        setError("Impossible de charger le profil");
      } finally {
        setInitialLoading(false);
      }
    };

    loadUserData();
  }, []);

  // ✅ Déconnexion
  const handleLogout = async () => {
    try {
      await logout();
      router.push("/login");
    } catch (err) {
      console.error("Erreur lors de la déconnexion:", err);
      // Même si erreur, nettoyer et rediriger
      await clearAuth();
      router.push("/login");
    }
  };

  // ✅ Mise à jour du profil via apiClient (avec Bearer token automatique)
  const handleUpdateProfile = async () => {
    if (!name || !email) {
      setError("Veuillez remplir tous les champs requis");
      return;
    }

    setLoading(true);
    setError("");

    try {
      // ✅ Utiliser apiClient au lieu de fetch
      // Les intercepteurs ajoutent automatiquement le Bearer token
      const response = await apiClient.patch("/profile", {
        name,
        email,
        phone: phone || null,
      });

      const updatedUser = response.data.data || response.data;
      setUser(updatedUser);
      
      // Mettre à jour le stockage
      if (updatedUser) {
        await AsyncStorage.setItem("@medconnect_user", JSON.stringify(updatedUser));
      }

      setError(""); // Succès
      alert("Profil mis à jour avec succès!");
    } catch (error: any) {
      console.error("Erreur lors de la mise à jour:", error);

      // Gérer les erreurs spécifiques
      if (error.response?.status === 401) {
        setError("Votre session a expiré. Veuillez vous reconnecter.");
        router.push("/login");
      } else if (error.response?.data?.message) {
        setError(error.response.data.message);
      } else {
        setError("Une erreur est survenue lors de la mise à jour");
      }
    } finally {
      setLoading(false);
    }
  };

  if (initialLoading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.centerContent}>
          <ActivityIndicator size="large" color="#007AFF" />
          <ThemedText style={styles.loadingText}>Chargement...</ThemedText>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        <ThemedText style={styles.title}>Mon Profil</ThemedText>

        {user && (
          <ThemedText style={styles.subtitle}>
            ID utilisateur: {user.id}
          </ThemedText>
        )}

        <TextInput
          style={styles.input}
          placeholder="Nom complet"
          value={name}
          onChangeText={setName}
          editable={!loading}
        />

        <TextInput
          style={styles.input}
          placeholder="Email"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          editable={!loading}
        />

        <TextInput
          style={styles.input}
          placeholder="Téléphone"
          value={phone}
          onChangeText={setPhone}
          keyboardType="phone-pad"
          editable={!loading}
        />

        {error && <ThemedText style={styles.error}>{error}</ThemedText>}

        <TouchableOpacity
          style={[styles.button, styles.successButton, loading && styles.disabled]}
          onPress={handleUpdateProfile}
          disabled={loading}
        >
          <ThemedText style={styles.buttonText}>
            {loading ? "Mise à jour en cours..." : "Mettre à jour le profil"}
          </ThemedText>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.button, styles.dangerButton]}
          onPress={handleLogout}
          disabled={loading}
        >
          <ThemedText style={styles.buttonText}>Se déconnecter</ThemedText>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#fff",
  },
  content: {
    flexGrow: 1,
    paddingHorizontal: 20,
    paddingVertical: 30,
  },
  centerContent: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  title: {
    fontSize: 28,
    fontWeight: "bold",
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 14,
    color: "#666",
    marginBottom: 20,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
  },
  input: {
    borderWidth: 1,
    borderColor: "#ddd",
    padding: 12,
    marginBottom: 15,
    borderRadius: 8,
    fontSize: 16,
    color: "#000",
  },
  button: {
    padding: 14,
    borderRadius: 8,
    alignItems: "center",
    marginBottom: 12,
  },
  successButton: {
    backgroundColor: "#007AFF",
  },
  dangerButton: {
    backgroundColor: "#FF3B30",
  },
  buttonText: {
    color: "#fff",
    fontWeight: "600",
    fontSize: 16,
  },
  disabled: {
    opacity: 0.6,
  },
  error: {
    color: "#FF3B30",
    marginBottom: 15,
    fontSize: 14,
    fontWeight: "500",
  },
});
