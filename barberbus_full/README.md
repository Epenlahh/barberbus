# BarberBus – Complete System Documentation

## 📁 Full Project Structure

```
barberbus_full/
├── index.html              ← Homepage
├── services.html           ← Services listing
├── barbers.html            ← Barbers listing
├── booking.html            ← Customer booking form
├── login.html              ← Login / Register
├── dashboard.html          ← Customer self-service dashboard
├── tryon.html              ← AR Hairstyle Try-On (TensorFlow.js)
│
├── css/style.css           ← Shared design tokens
├── js/
│   ├── auth.js             ← Real PHP-connected auth
│   ├── booking.js
│   ├── main.js
│   ├── services.js
│   └── fashion.js
│
├── api/                    ← Customer-facing PHP backend
│   ├── config.php          ← DB config + JWT helpers
│   ├── database.sql        ← ★ RUN THIS FIRST ★
│   ├── auth.php            ← Login, register, profile
│   ├── bookings.php        ← Booking CRUD
│   ├── services.php        ← Services + barbers
│   ├── users.php           ← User list (admin)
│   └── stats.php           ← Admin analytics
│
├── admin/
│   └── index.html          ← Admin management panel
│
└── officer/                ← ★ NEW: Officer Dashboard ★
    ├── index.html          ← Full officer real-time UI
    ├── style.css           ← Officer-specific CSS
    ├── app.js              ← All real-time JS logic
    └── api.php             ← Officer-specific API
```

---

## 🚀 Quick Setup (XAMPP / Laragon)

1. Copy `barberbus_full/` → `htdocs/barberbus/` (XAMPP) or `www/barberbus/` (Laragon)
2. Start Apache + MySQL
3. Open `http://localhost/phpmyadmin` → **SQL** tab → paste `api/database.sql` → Go
4. Edit `api/config.php` with your MySQL credentials
5. Done! Access the site at `http://localhost/barberbus/`

---

## 🔑 Login Accounts

| Role      | Email                     | Password   | Access                     |
|-----------|---------------------------|------------|----------------------------|
| Admin     | admin@barberbus.com       | password   | Full admin panel           |
| Officer   | officer@barberbus.com     | password   | Officer dashboard only     |
| Customer  | Register via login page   | (yours)    | Customer dashboard + booking |

---

## 🖥️ System Overview

### Customer Journey
```
index.html → login.html → dashboard.html
    └── booking.html → api/bookings.php → MySQL
```

### Officer Dashboard (NEW)
```
officer/index.html
  ├── Real-time queue (polls every 12s)
  ├── Walk-in customer entry
  ├── Today's full schedule (timeline + table)
  ├── Customer search & history
  ├── Live notifications (new bookings)
  └── Barber utilisation stats
```
Access: `http://localhost/barberbus/officer/`
Login with admin or officer credentials.

### Admin Panel
```
admin/index.html
  ├── KPI dashboard with revenue chart
  ├── All bookings management
  ├── User management
  ├── Barber add/edit/deactivate
  └── Service add/edit/deactivate
```
Access: `http://localhost/barberbus/admin/`

---

## 📡 API Reference

### Customer API (`/api/`)
| Endpoint          | Method | Auth     | Description              |
|-------------------|--------|----------|--------------------------|
| auth.php?action=login     | POST | None  | Login → JWT token        |
| auth.php?action=register  | POST | None  | Create account           |
| auth.php?action=me        | GET  | User  | Get own profile          |
| auth.php?action=update    | POST | User  | Update profile           |
| bookings.php              | GET  | User  | My bookings              |
| bookings.php              | POST | User  | Create booking           |
| bookings.php?id=X         | PUT  | User  | Cancel own booking       |
| bookings.php?admin=1      | GET  | Admin | All bookings             |
| services.php              | GET  | None  | All services             |
| services.php?type=barbers | GET  | None  | All barbers              |
| stats.php                 | GET  | Admin | Dashboard analytics      |

### Officer API (`/officer/api.php`)
| action              | Method | Description                       |
|---------------------|--------|-----------------------------------|
| login               | POST   | Officer login                     |
| stats               | GET    | Today's live KPIs                 |
| queue               | GET    | Today's pending + confirmed       |
| today               | GET    | Full today schedule               |
| update-status       | POST   | Change booking status             |
| walkin              | POST   | Add walk-in (no user account)     |
| search?q=X          | GET    | Search customers by name/phone    |
| customer-history    | GET    | Customer's full booking history   |
| notifications       | GET    | Bookings created in last 30s      |

---

## ⚡ Officer Dashboard Features

| Feature              | Detail                                       |
|----------------------|----------------------------------------------|
| Live Queue           | Auto-refreshes every 12 seconds              |
| Status Updates       | Confirm → In Progress → Done in one click    |
| Walk-In Entry        | Add customers without a user account         |
| Today's Timeline     | Visual timeline + full table view            |
| Customer Lookup      | Search by name or phone, see full history    |
| Live Notifications   | New bookings appear as toast alerts          |
| Barber Utilisation   | See which barbers are busiest today          |
| Collapsible Sidebar  | Maximize screen space on small displays      |
| Real-time Clock      | Always-visible time display                  |

---

## 🛡️ Security
- JWT tokens, 7-day expiry, HS256 signed
- bcrypt password hashing
- Role-based API guards: user / officer / admin
- CORS headers on all API endpoints
- SQL injection protected via PDO prepared statements

---

## 🎨 AR Try-On (`/tryon.html`)
- TensorFlow.js + BlazeFace real face detection
- 12 trending 2025 hairstyles rendered on canvas
- Hair colour picker, opacity blend, scale controls
- Photo capture + download with BarberBus watermark
- No data uploaded — all runs locally in browser
