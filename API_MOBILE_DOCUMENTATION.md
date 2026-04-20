# API Documentation - React Native

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
L'API utilise **Laravel Sanctum** pour l'authentification. Après login, tu reçois un token à inclure dans chaque requête:

```
Authorization: Bearer {token}
```

---

## 1. Authentication

### Login
```
POST /api/v1/login
```

**Body:**
```json
{
  "email": "patient@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Nom Patient",
    "email": "patient@example.com",
    "role": "user"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Logout
```
POST /api/v1/logout
```
**Headers:** `Authorization: Bearer {token}`

### Get Current User
```
GET /api/v1/me
```
**Headers:** `Authorization: Bearer {token}`

---

## 2. User Profile

### Get Profile
```
GET /api/v1/profile
```
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "id": 1,
  "name": "Nom Patient",
  "email": "patient@example.com",
  "phone": "+212612345678",
  "role": "user",
  "created_at": "2024-01-15T10:30:00Z"
}
```

### Update Profile
```
PATCH /api/v1/profile
```
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "name": "Nouveau Nom",
  "phone": "+212612345678"
}
```

---

## 3. Professionals

### List All Professionals
```
GET /api/v1/professionals?specialty=cardiologie&city=Casablanca
```

**Query Parameters:**
- `specialty` (optional): specialité médicale
- `city` (optional): ville

**Response:**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Dr. Ahmed",
      "email": "dr.ahmed@example.com",
      "phone": "+212612345679",
      "role": "professional",
      "created_at": "2024-01-10T08:00:00Z"
    }
  ],
  "pagination": {
    "total": 150,
    "count": 20,
    "per_page": 20,
    "current_page": 1,
    "last_page": 8
  }
}
```

### Get Professional Details
```
GET /api/v1/professionals/{id}
```

---

## 4. Appointments

### List My Appointments
```
GET /api/v1/appointments
```
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
[
  {
    "id": 1,
    "date": "2024-02-15",
    "heure": "14:30",
    "professional": {
      "id": 2,
      "name": "Dr. Ahmed",
      "email": "dr.ahmed@example.com",
      "role": "professional"
    },
    "status": "soumis",
    "type": "presentiel",
    "created_at": "2024-01-20T10:30:00Z"
  }
]
```

### Get Single Appointment
```
GET /api/v1/appointments/{id}
```
**Headers:** `Authorization: Bearer {token}`

### Create Appointment
```
POST /api/v1/appointments
```
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "professional_id": 2,
  "date_consultation": "2024-02-15",
  "heure_consultation": "14:30",
  "type_consultation": "presentiel"
}
```

### Cancel Appointment
```
POST /api/v1/appointments/{id}/cancel
```
**Headers:** `Authorization: Bearer {token}`

---

## React Native Example

```javascript
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api/v1';

// Store token in AsyncStorage or state
let authToken = null;

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add token
api.interceptors.request.use((config) => {
  if (authToken) {
    config.headers.Authorization = `Bearer ${authToken}`;
  }
  return config;
});

// Login
export const login = async (email, password) => {
  try {
    const response = await api.post('/login', { email, password });
    authToken = response.data.token;
    return response.data;
  } catch (error) {
    console.error('Login failed:', error.response.data);
    throw error;
  }
};

// Get user profile
export const getUserProfile = async () => {
  try {
    const response = await api.get('/profile');
    return response.data;
  } catch (error) {
    console.error('Failed to fetch profile:', error);
    throw error;
  }
};

// Get professionals
export const getProfessionals = async (specialty = null, city = null) => {
  try {
    const params = {};
    if (specialty) params.specialty = specialty;
    if (city) params.city = city;
    
    const response = await api.get('/professionals', { params });
    return response.data;
  } catch (error) {
    console.error('Failed to fetch professionals:', error);
    throw error;
  }
};

// Get appointments
export const getAppointments = async () => {
  try {
    const response = await api.get('/appointments');
    return response.data;
  } catch (error) {
    console.error('Failed to fetch appointments:', error);
    throw error;
  }
};

// Create appointment
export const createAppointment = async (professionalId, date, time, type) => {
  try {
    const response = await api.post('/appointments', {
      professional_id: professionalId,
      date_consultation: date,
      heure_consultation: time,
      type_consultation: type,
    });
    return response.data;
  } catch (error) {
    console.error('Failed to create appointment:', error);
    throw error;
  }
};

// Logout
export const logout = async () => {
  try {
    await api.post('/logout');
    authToken = null;
  } catch (error) {
    console.error('Logout failed:', error);
    throw error;
  }
};
```

---

## Error Handling

Toutes les erreurs retournent un JSON avec un message:

```json
{
  "message": "Invalid credentials"
}
```

**Status Codes:**
- `200` - Succès
- `201` - Créé
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Non trouvé
- `422` - Validation error
- `500` - Erreur serveur
