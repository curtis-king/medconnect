# API REST Complète - React Native Documentation

## 📱 Vue d'ensemble

Une **API REST complète** construite avec Laravel Sanctum pour consommer depuis React Native.

**Base URL:** `http://localhost:8000/api/v1`

**Total Endpoints:** 46

---

## 🔐 Authentication

### Login
```
POST /api/v1/login
Content-Type: application/json

{
  "email": "patient@example.com",
  "password": "password123"
}

Response 200:
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Ahmed Hassan",
    "email": "patient@example.com",
    "role": "user"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Logout
```
POST /api/v1/logout
Authorization: Bearer {TOKEN}
```

### Get Current User
```
GET /api/v1/me
Authorization: Bearer {TOKEN}
```

---

## 👤 User Profile

### Get Profile
```
GET /api/v1/profile
Authorization: Bearer {TOKEN}
```

### Update Profile
```
PATCH /api/v1/profile
Authorization: Bearer {TOKEN}
Content-Type: application/json

{
  "name": "New Name",
  "phone": "+212612345678"
}
```

---

## 🏥 Medical Dossiers

### List All Medical Dossiers
```
GET /api/v1/medical-dossiers?page=1
Authorization: Bearer {TOKEN}
```

### Create Medical Dossier
```
POST /api/v1/medical-dossiers
Authorization: Bearer {TOKEN}
Content-Type: application/json

{
  "est_personne_a_charge": false,
  "nom": "Hassan",
  "prenom": "Ahmed",
  "date_naissance": "1990-05-15",
  "sexe": "M",
  "telephone": "+212612345678",
  "groupe_sanguin": "O+",
  "allergies": "Pénicilline",
  "maladies_chroniques": "Diabète",
  "contact_urgence_nom": "Fatima Hassan",
  "contact_urgence_telephone": "+212612345679",
  "contact_urgence_relation": "Mère"
}
```

### Get Single Dossier
```
GET /api/v1/medical-dossiers/{id}
Authorization: Bearer {TOKEN}
```

### Update Dossier
```
PATCH /api/v1/medical-dossiers/{id}
Authorization: Bearer {TOKEN}
Content-Type: application/json

{
  "allergies": "Pénicilline, Aspiriine",
  "telephone": "+212612345678"
}
```

### Delete Dossier
```
DELETE /api/v1/medical-dossiers/{id}
Authorization: Bearer {TOKEN}
```

### Get Dossier Summary (Quick Info)
```
GET /api/v1/medical-dossiers/{id}/summary
Authorization: Bearer {TOKEN}
```

---

## 📅 Appointments (Rendez-vous)

### List Appointments
```
GET /api/v1/appointments?page=1
Authorization: Bearer {TOKEN}
```

### Create Appointment
```
POST /api/v1/appointments
Authorization: Bearer {TOKEN}
Content-Type: application/json

{
  "professional_id": 2,
  "date_consultation": "2024-02-20",
  "heure_consultation": "14:30",
  "type_consultation": "presentiel"
}
```

### Get Single Appointment
```
GET /api/v1/appointments/{id}
Authorization: Bearer {TOKEN}
```

### Cancel Appointment
```
POST /api/v1/appointments/{id}/cancel
Authorization: Bearer {TOKEN}
```

---

## 📄 Documents (Ordonnances, Examens, Consultations)

### Get All Documents (Overview)
```
GET /api/v1/documents
Authorization: Bearer {TOKEN}

Response: {
  "ordonnances": [...],
  "examens": [...],
  "consultations": [...],
  "total": {...}
}
```

### ⚕️ Prescriptions (Ordonnances)

#### List Prescriptions
```
GET /api/v1/documents/prescriptions?type=all&page=1
Authorization: Bearer {TOKEN}

Query params:
- type: all, en_cours, termines
```

#### Get Single Prescription
```
GET /api/v1/documents/prescriptions/{id}
Authorization: Bearer {TOKEN}
```

### 🔬 Exams (Examens)

#### List Exams
```
GET /api/v1/documents/exams?type=blood&page=1
Authorization: Bearer {TOKEN}

Query params:
- type: (optional) type_examen filter
```

#### Get Single Exam
```
GET /api/v1/documents/exams/{id}
Authorization: Bearer {TOKEN}
```

### 🏥 Consultations

#### List Consultations
```
GET /api/v1/documents/consultations?status=completed&page=1
Authorization: Bearer {TOKEN}

Query params:
- status: completed, pending
```

#### Get Single Consultation
```
GET /api/v1/documents/consultations/{id}
Authorization: Bearer {TOKEN}
```

### Download Document File
```
GET /api/v1/documents/download/{type}/{id}
Authorization: Bearer {TOKEN}

Path params:
- type: ordonnance, examen
- id: document ID

Response: {
  "url": "http://localhost:8000/storage/...",
  "type": "ordonnance"
}
```

---

## 💰 Invoices (Factures)

### List Invoices
```
GET /api/v1/invoices?status=pending&page=1
Authorization: Bearer {TOKEN}

Query params:
- status: paid, pending, submitted
```

### Get Single Invoice
```
GET /api/v1/invoices/{id}
Authorization: Bearer {TOKEN}
```

### Get Invoice Statistics
```
GET /api/v1/invoices/statistics?from_date=2024-01-01&to_date=2024-12-31
Authorization: Bearer {TOKEN}

Response: {
  "total_amount": 5000.00,
  "paid_amount": 3000.00,
  "pending_amount": 2000.00,
  "mutual_covered": 2500.00,
  "invoices_count": 15,
  "paid_count": 10,
  "pending_count": 5
}
```

### Get Invoice Summary
```
GET /api/v1/invoices/summary
Authorization: Bearer {TOKEN}
```

### Submit Invoice to Backoffice (Insurance)
```
PATCH /api/v1/invoices/{id}/submit-backoffice
Authorization: Bearer {TOKEN}
```

### Cancel Backoffice Submission
```
PATCH /api/v1/invoices/{id}/cancel-backoffice
Authorization: Bearer {TOKEN}
```

### Mark Invoice as Paid
```
PATCH /api/v1/invoices/{id}/mark-paid
Authorization: Bearer {TOKEN}
Content-Type: application/json

{
  "mode_paiement": "mobile_money",
  "reference_paiement": "TRX123456"
}
```

---

## 🔄 Subscriptions (Abonnements)

### List All Subscriptions
```
GET /api/v1/subscriptions
Authorization: Bearer {TOKEN}

Response: {
  "medical": [...],
  "professional": [...],
  "total_active": 2
}
```

### Get Single Subscription
```
GET /api/v1/subscriptions/{id}
Authorization: Bearer {TOKEN}
```

### Get Medical Dossier Subscription Status
```
GET /api/v1/subscriptions/dossier/{dossier_id}/status
Authorization: Bearer {TOKEN}

Response: {
  "has_active": true,
  "subscription": {...},
  "renewal_fee": 500.00,
  "days_until_expiry": 45,
  "can_renew": false
}
```

### Renew Single Medical Subscription
```
POST /api/v1/subscriptions/dossier/{dossier_id}/renew
Authorization: Bearer {TOKEN}

Response 201: {
  "message": "Reabonnement initié",
  "subscription": {...},
  "next_payment_required": 500.00
}
```

### Renew All Subscriptions (Medical + Professional)
```
POST /api/v1/subscriptions/renew-all
Authorization: Bearer {TOKEN}

Response 201: {
  "message": "Tous les reabonnements ont été initiés",
  "total_amount_due": 1000.00,
  "details": {...}
}
```

### Get Subscription History
```
GET /api/v1/subscriptions/history?page=1
Authorization: Bearer {TOKEN}
```

---

## 🔔 Notifications

### List Notifications
```
GET /api/v1/notifications?unread=false&page=1
Authorization: Bearer {TOKEN}

Query params:
- unread: true/false
```

### Get Single Notification
```
GET /api/v1/notifications/{id}
Authorization: Bearer {TOKEN}
```

### Get Notification Summary
```
GET /api/v1/notifications/summary
Authorization: Bearer {TOKEN}

Response: {
  "unread_count": 5,
  "total_count": 127,
  "recent": [...],
  "types": {
    "appointments": 2,
    "invoices": 1,
    "subscriptions": 1,
    "documents": 1
  }
}
```

### Get Alerts
```
GET /api/v1/notifications/alerts?type=health
Authorization: Bearer {TOKEN}

Query params:
- type: (optional) alert type
```

### Get Live Notifications (WebSocket Ready)
```
GET /api/v1/notifications/live
Authorization: Bearer {TOKEN}
```

### Mark Notification as Read
```
PATCH /api/v1/notifications/{id}/read
Authorization: Bearer {TOKEN}
```

### Mark All Notifications as Read
```
PATCH /api/v1/notifications/read-all
Authorization: Bearer {TOKEN}
```

### Delete Notification
```
DELETE /api/v1/notifications/{id}
Authorization: Bearer {TOKEN}
```

---

## 👨‍⚕️ Professionals

### List All Professionals
```
GET /api/v1/professionals?specialty=cardiologie&city=Casablanca&page=1

Query params:
- specialty: (optional) specialité médicale
- city: (optional) ville
```

### Get Professional Details
```
GET /api/v1/professionals/{id}
```

---

## 📌 React Native Axios Setup

```javascript
// api.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://localhost:8000/api/v1';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

let authToken = null;

// Request interceptor
api.interceptors.request.use(async (config) => {
  if (!authToken) {
    authToken = await AsyncStorage.getItem('auth_token');
  }
  if (authToken) {
    config.headers.Authorization = `Bearer ${authToken}`;
  }
  return config;
});

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Token expired, redirect to login
      await AsyncStorage.removeItem('auth_token');
      authToken = null;
      // Navigation.push('Login')
    }
    return Promise.reject(error);
  }
);

// Auth methods
export const authAPI = {
  login: (email, password) =>
    api.post('/login', { email, password }),
  logout: () => api.post('/logout'),
  me: () => api.get('/me'),
};

// Medical dossiers
export const dossierAPI = {
  list: (page = 1) => api.get('/medical-dossiers', { params: { page } }),
  get: (id) => api.get(`/medical-dossiers/{id}`),
  create: (data) => api.post('/medical-dossiers', data),
  update: (id, data) => api.patch(`/medical-dossiers/{id}`, data),
  delete: (id) => api.delete(`/medical-dossiers/{id}`),
};

// Appointments
export const appointmentAPI = {
  list: (page = 1) => api.get('/appointments', { params: { page } }),
  get: (id) => api.get(`/appointments/{id}`),
  create: (data) => api.post('/appointments', data),
  cancel: (id) => api.post(`/appointments/{id}/cancel`),
};

// Documents
export const documentAPI = {
  getAll: () => api.get('/documents'),
  prescriptions: (page = 1) => api.get('/documents/prescriptions', { params: { page } }),
  exams: (page = 1) => api.get('/documents/exams', { params: { page } }),
  consultations: (page = 1) => api.get('/documents/consultations', { params: { page } }),
  download: (type, id) => api.get(`/documents/download/{type}/{id}`),
};

// Invoices
export const invoiceAPI = {
  list: (page = 1) => api.get('/invoices', { params: { page } }),
  get: (id) => api.get(`/invoices/{id}`),
  statistics: () => api.get('/invoices/statistics'),
  summary: () => api.get('/invoices/summary'),
  markPaid: (id, data) => api.patch(`/invoices/{id}/mark-paid`, data),
  submitBackoffice: (id) => api.patch(`/invoices/{id}/submit-backoffice`),
};

// Subscriptions
export const subscriptionAPI = {
  list: () => api.get('/subscriptions'),
  getStatus: (dossierId) => api.get(`/subscriptions/dossier/{dossierId}/status`),
  renew: (dossierId) => api.post(`/subscriptions/dossier/{dossierId}/renew`),
  renewAll: () => api.post('/subscriptions/renew-all'),
};

// Notifications
export const notificationAPI = {
  list: (page = 1) => api.get('/notifications', { params: { page } }),
  summary: () => api.get('/notifications/summary'),
  markAsRead: (id) => api.patch(`/notifications/{id}/read`),
  markAllAsRead: () => api.patch('/notifications/read-all'),
};

export default api;
```

---

## 🛡️ Error Handling

```javascript
try {
  const response = await api.get('/medical-dossiers');
  console.log(response.data);
} catch (error) {
  if (error.response?.status === 401) {
    console.log('Unauthorized - Login required');
  } else if (error.response?.status === 403) {
    console.log('Forbidden - Access denied');
  } else if (error.response?.status === 422) {
    console.log('Validation error:', error.response.data);
  } else {
    console.log('Error:', error.response?.data?.message);
  }
}
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Success |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Authentication required |
| 403 | Forbidden - Access denied |
| 404 | Not Found - Resource doesn't exist |
| 422 | Unprocessable Entity - Validation error |
| 500 | Server Error |

---

## 📦 Rate Limiting

Currently no rate limiting applied. Production should implement:
- 100 requests/minute for authenticated users
- 10 requests/minute for unauthenticated endpoints

---

## 🔄 Pagination

```
GET /api/v1/medical-dossiers?page=2&per_page=15

Response structure:
{
  "data": [...],
  "pagination": {
    "total": 150,
    "per_page": 15,
    "current_page": 2,
    "last_page": 10
  }
}
```

---

**API Version:** 1.0  
**Last Updated:** 2024-04-08  
**Framework:** Laravel 12 + Sanctum
