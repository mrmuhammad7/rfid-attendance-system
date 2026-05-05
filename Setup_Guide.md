# RFID Attendance System — Setup Guide

## 📁 Project Structure

```
C:\xampp\htdocs\attendance\
├── index.php               ← Student/Guest dashboard
├── login.php               ← Admin login
├── logout.php
├── database.sql            ← Run this first in phpMyAdmin
├── esp8266_firmware.ino    ← Upload to ESP8266
│
├── includes/
│   ├── db.php              ← DB connection + helpers
│   └── auth.php            ← Session / login logic
│
├── api/
│   ├── scan.php            ← POST from ESP | GET for dashboard polling
│   ├── stats.php           ← Attendance statistics
│   ├── students.php        ← CRUD for students
│   └── pending_scan.php    ← Real-time UID relay to admin pages
│
├── admin/
│   ├── index.php           ← Admin dashboard
│   ├── add.php             ← Add new student via RFID scan
│   ├── edit.php            ← Edit student via RFID scan
│   ├── delete.php          ← Delete students with checkboxes
│   ├── _header.php         ← Shared layout header
│   └── _footer.php         ← Shared layout footer
│
└── assets/
    ├── css/style.css
    └── js/app.js
```

---

## 🚀 Setup Steps

### 1. Database
1. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Click **Import** → select `database.sql`
3. Click **Go**

Default admin login:
- Email: `admin@attendance.com`
- Password: `admin123`

---

### 2. XAMPP
Make sure **Apache** and **MySQL** are both **Running** (green).

Place the entire project folder in:
```
C:\xampp\htdocs\attendance\
```

Access:
- Student View: `http://localhost/attendance/`
- Admin Login:  `http://localhost/attendance/login.php`

---

### 3. ESP8266 Firmware

Edit these 3 lines in `esp8266_firmware.ino`:

```cpp
const char* WIFI_SSID     = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";
const char* SERVER_URL    = "http://10.147.37.30/attendance/api/scan.php";
//                                   ↑ your PC's IP on same network
```

**Find your PC's IP:**
```
cmd → ipconfig → look for your WiFi IPv4 Address
```

**Arduino Libraries needed:**
- MFRC522
- TFT_eSPI  (configure User_Setup.h as provided)
- ESP8266WiFi (built-in with ESP8266 board package)
- ESP8266HTTPClient (built-in)

---

### 4. Firewall (Important!)
Allow port 80 inbound on Windows:
```cmd
netsh advfirewall firewall add rule name="XAMPP HTTP" protocol=TCP dir=in localport=80 action=allow
```
Run CMD as **Administrator**.

---

## 🔌 API Reference

### POST /attendance/api/scan.php
ESP8266 sends card UID.

**Request:**
```json
{ "uid": "BB1B3402" }
```

**Response (known):**
```json
{
  "status": "success",
  "uid": "BB1B3402",
  "name": "Mazen Ahmed",
  "student_id": "23/121534",
  "known": true,
  "already": false,
  "timestamp": "2025-01-15 10:30:00"
}
```

**Response (unknown):**
```json
{
  "status": "unknown",
  "uid": "AABBCCDD",
  "message": "Card not registered",
  "timestamp": "2025-01-15 10:30:00"
}
```

### GET /attendance/api/stats.php
Returns today's attendance stats + student list.

### GET|POST|PUT|DELETE /attendance/api/students.php
Full CRUD for students.

---

## 🔄 How Real-time Works

```
ESP scans card
    ↓
POST /api/scan.php  →  saves to DB + writes last_scan.json (temp)
    ↓
Dashboard polls GET /api/scan.php every 2 seconds
    ↓
If new UID → update banner + stats + table
```

Admin Add/Edit pages poll `/api/pending_scan.php` every 1 second
to detect when ESP scans a card → auto-fill UID field.
