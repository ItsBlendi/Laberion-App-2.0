# 🏢 Laberion Workforce Management System (WMS)

<div align="center">

![Laberion Logo](https://img.shields.io/badge/Laberion-WMS-1e3a8a?style=for-the-badge&logo=building&logoColor=white)

**A Professional Employee Management System with Face ID Recognition**

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Python](https://img.shields.io/badge/Python-3.7+-3776AB?style=flat&logo=python&logoColor=white)](https://www.python.org/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=flat)](LICENSE)

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Documentation](#-documentation) • [Support](#-support)

</div>

---

## 📖 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Screenshots](#-screenshots)
- [Technology Stack](#-technology-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Folder Structure](#-folder-structure)
- [API Documentation](#-api-documentation)
- [Database Schema](#-database-schema)
- [Security](#-security)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)
- [Support](#-support)

---

## 🎯 Overview

**Laberion WMS** is a comprehensive workforce management system designed for modern businesses. It combines traditional employee management with cutting-edge face recognition technology for seamless attendance tracking.

### Key Highlights

- 🔐 **Face ID Check-in/out** - Contactless attendance using face recognition
- 📊 **Real-time Dashboard** - Live statistics and analytics
- 👥 **Worker Management** - Complete employee lifecycle management
- 🌴 **Leave Management** - Vacation and sick leave tracking
- 📈 **Comprehensive Reports** - Detailed analytics and exports
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile
- 🛡️ **Secure** - Industry-standard security practices

---

## ✨ Features

### 🏠 Admin Dashboard
- ✅ Real-time statistics overview
- ✅ Total workers count
- ✅ Present/absent tracking
- ✅ Late arrivals monitoring
- ✅ Recent check-ins display
- ✅ Pending leave requests
- ✅ Quick action buttons

### 👷 Worker Management
- ✅ Add new workers with photos
- ✅ Edit worker information
- ✅ View detailed profiles
- ✅ Search and filter workers
- ✅ Department-wise organization
- ✅ Status tracking (active, vacation, sick)
- ✅ Face ID registration
- ✅ Bulk import/export

### 🕒 Attendance Tracking
- ✅ Face recognition check-in/out
- ✅ Manual attendance marking
- ✅ Real-time attendance status
- ✅ Late arrival detection
- ✅ Hours calculation
- ✅ Date range filters
- ✅ Department filtering
- ✅ Export to Excel/CSV

### 🌴 Leave Management
- ✅ Vacation leave requests
- ✅ Sick leave tracking
- ✅ Personal leave management
- ✅ Approval workflow
- ✅ Leave history
- ✅ Status notifications
- ✅ Calendar view

### 📊 Reports & Analytics
- ✅ Monthly attendance reports
- ✅ Department-wise analysis
- ✅ Hours worked tracking
- ✅ Late days analysis
- ✅ Excel/CSV exports
- ✅ Custom date ranges
- ✅ Visual charts

### 📱 Kiosk Application
- ✅ Tablet-optimized interface
- ✅ Real-time camera feed
- ✅ Automatic face detection
- ✅ Instant check-in/out
- ✅ Visual confirmations
- ✅ Multi-language support

### ⚙️ System Features
- ✅ Admin user management
- ✅ Activity logging
- ✅ Session management
- ✅ Password security
- ✅ Email notifications
- ✅ System settings
- ✅ Data backup

---

## 🖼️ Screenshots

### Login Page
```
[Login Screen]
- Modern gradient background
- Clean form design
- Password visibility toggle
- Remember me functionality
```

### Dashboard
```
[Dashboard View]
- Statistics cards
- Recent activity
- Pending leaves
- Quick actions
```

### Workers List
```
[Workers Page]
- Searchable table
- Filter by department
- Action buttons
- Pagination
```

### Kiosk Interface
```
[Kiosk Screen]
- Live camera feed
- Face detection box
- Real-time status
- Confirmation message
```

---

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Markup language
- **CSS3** - Styling and animations
- **JavaScript (ES6+)** - Interactive functionality
- **Bootstrap 5.3** - UI framework
- **Font Awesome 6** - Icons
- **Chart.js** - Data visualization
- **Axios** - HTTP requests

### Backend
- **PHP 7.4+** - Server-side language
- **MySQL 5.7+** - Database
- **Apache/Nginx** - Web server

### Face Recognition
- **Python 3.7+** - Programming language
- **OpenCV 4.8+** - Computer vision
- **face_recognition** - Face recognition library
- **dlib** - Machine learning toolkit
- **NumPy** - Numerical computing

### Tools & Libraries
- **DataTables** - Advanced tables
- **jQuery** - DOM manipulation
- **PHPMailer** - Email handling
- **PHPExcel** - Excel exports

---

## 💻 System Requirements

### Minimum Requirements

| Component | Specification |
|-----------|---------------|
| **OS** | Windows 10/11, macOS, Linux |
| **PHP** | 7.4 or higher |
| **MySQL** | 5.7 or higher |
| **Python** | 3.7 or higher |
| **RAM** | 2 GB |
| **Storage** | 1 GB free space |
| **Browser** | Chrome 90+, Firefox 88+, Safari 14+, Edge 90+ |

### Recommended Requirements

| Component | Specification |
|-----------|---------------|
| **OS** | Linux (Ubuntu 20.04+) |
| **PHP** | 8.0 or higher |
| **MySQL** | 8.0 |
| **Python** | 3.9+ |
| **RAM** | 4 GB |
| **Storage** | 5 GB free space |
| **CPU** | 2+ cores |

### Hardware (For Kiosk)
- Tablet or touchscreen device
- HD webcam (720p minimum)
- Stable internet connection

---

## 📥 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-username/laberion-wms.git
cd laberion-wms
```

### Step 2: Install PHP Dependencies (if using Composer)

```bash
composer install
```

### Step 3: Setup Database

```bash
# Login to MySQL
mysql -u root -p

# Import database
source database.sql

# Or use command line
mysql -u root -p < database.sql
```

### Step 4: Configure Database Connection

Edit `includes/db.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'laberion_wms');
```

### Step 5: Install Python Dependencies

```bash
cd python
pip install -r requirements.txt
python3 setup.py
```

### Step 6: Set Permissions

```bash
chmod -R 755 assets/uploads/
chmod -R 755 logs/
chmod -R 755 python/temp/
```

### Step 7: Configure Web Server

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteBase /laberion/

# Redirect to login if not authenticated
RewriteCond %{REQUEST_URI} !^/laberion/login\.php$
RewriteCond %{REQUEST_URI} !^/laberion/kiosk/
RewriteCond %{REQUEST_URI} !^/laberion/api/
RewriteCond %{HTTP_COOKIE} !PHPSESSID
RewriteRule ^(.*)$ login.php [L]
```

#### Nginx
```nginx
server {
    listen 80;
    server_name laberion.local;
    root /var/www/laberion;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### Step 8: Access the Application

Open your browser and navigate to:
```
http://localhost/laberion/
```

**Default Credentials:**
- **Username:** `admin`
- **Password:** `password`

⚠️ **Important:** Change the default password immediately after first login!

---

## ⚙️ Configuration

### Database Configuration

Edit `includes/db.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'laberion_wms');
define('DB_PORT', 3306);
```

### Application Settings

Edit `includes/config.php`:

```php
// Application
define('APP_NAME', 'Laberion WMS');
define('APP_URL', 'http://localhost/laberion');

// Working Hours
define('WORK_START_TIME', '09:00:00');
define('WORK_END_TIME', '17:00:00');
define('LATE_THRESHOLD', 15);

// Timezone
date_default_timezone_set('Europe/Zurich');
```

### Environment Variables (.env)

Create `.env` file in root:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=laberion_wms

PYTHON_PATH=/usr/bin/python3
FACE_RECOGNITION_THRESHOLD=0.6

TIMEZONE=Europe/Zurich
APP_ENV=production
APP_DEBUG=false

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=your_email@gmail.com
MAIL_PASS=your_app_password
```

---

## 📚 Usage

### Admin Dashboard

#### 1. Login
- Navigate to `http://localhost/laberion/`
- Enter credentials
- Click "Sign In"

#### 2. Add a Worker
1. Go to **Workers** → **Add New Worker**
2. Fill in the worker information:
   - Name, Last Name
   - Position
   - Department
   - Phone, Email
   - Salary (optional)
   - Start Date
3. Upload profile photo
4. Check "Capture Face ID" if needed
5. Click **Save Worker**

#### 3. Capture Face ID
1. Open worker profile
2. Click **Capture Face**
3. Position face in camera
4. Wait for capture
5. Confirm registration

#### 4. View Attendance
1. Go to **Attendance**
2. Select date range
3. Filter by worker/department
4. View detailed records
5. Export to Excel

#### 5. Manage Leaves
1. Go to **Leaves**
2. View pending requests
3. Click **Approve** or **Reject**
4. Add rejection reason if needed

### Kiosk Application

#### Setup
1. Open `http://localhost/laberion/kiosk/`
2. Allow camera permissions
3. Position kiosk at entrance

#### Usage
1. Worker approaches kiosk
2. Face is detected automatically
3. System recognizes worker
4. Check-in/out is recorded
5. Confirmation is displayed

---

## 📁 Folder Structure

```
laberion/
│
├── 📁 admin/                    # Admin pages
│   ├── dashboard.php
│   ├── workers.php
│   ├── worker-profile.php
│   ├── add-worker.php
│   ├── edit-worker.php
│   ├── delete-worker.php
│   ├── attendance.php
│   ├── attendance-details.php
│   ├── leaves.php
│   ├── leave-approve.php
│   ├── leave-reject.php
│   ├── reports.php
│   ├── export-excel.php
│   ├── settings.php
│   └── logout.php
│
├── 📁 kiosk/                    # Kiosk interface
│   ├── index.php
│   ├── camera.js
│   ├── face-capture.js
│   ├── check-in.php
│   ├── check-out.php
│   ├── face-capture.php
│   └── process-attendance.php
│
├── 📁 includes/                 # Shared components
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   ├── sidebar.php
│   ├── footer.php
│   ├── functions.php
│   └── config.php
│
├── 📁 api/                      # API endpoints
│   ├── check-face.php
│   ├── get-worker.php
│   ├── save-attendance.php
│   ├── get-stats.php
│   ├── upload-face.php
│   ├── delete-worker.php
│   ├── get-attendance.php
│   ├── get-leaves.php
│   ├── approve-leave.php
│   └── reject-leave.php
│
├── 📁 assets/                   # Static assets
│   ├── 📁 css/
│   │   ├── style.css
│   │   ├── dashboard.css
│   │   ├── kiosk.css
│   │   └── responsive.css
│   ├── 📁 js/
│   │   ├── main.js
│   │   ├── dashboard.js
│   │   ├── workers.js
│   │   └── attendance.js
│   ├── 📁 images/
│   └── 📁 uploads/
│       ├── 📁 workers/
│       └── 📁 faces/
│
├── 📁 python/                   # Face recognition
│   ├── face_recognition.py
│   ├── train_faces.py
│   ├── capture_face.py
│   ├── compare_faces.py
│   ├── batch_train.py
│   ├── setup.py
│   └── requirements.txt
│
├── 📁 logs/                     # System logs
├── 📁 exports/                  # Generated exports
│
├── 📄 index.php
├── 📄 login.php
├── 📄 database.sql
├── 📄 .htaccess
├── 📄 .env
├── 📄 .gitignore
└── 📄 README.md
```

---

## 🔌 API Documentation

### Authentication

All API endpoints (except face recognition) require authentication via session.

### Endpoints

#### 1. Check Face
```http
POST /api/check-face.php
Content-Type: multipart/form-data

face_image: [file]
```

**Response:**
```json
{
    "success": true,
    "worker_id": 123,
    "worker_name": "John Doe",
    "confidence": 0.95,
    "action": "check_in"
}
```

#### 2. Save Attendance
```http
POST /api/save-attendance.php
Content-Type: application/x-www-form-urlencoded

worker_id=123&date=2024-01-15&time=09:00:00&action=check_in
```

#### 3. Get Worker
```http
GET /api/get-worker.php?id=123
```

#### 4. Get Statistics
```http
GET /api/get-stats.php
```

#### 5. Get Attendance
```http
GET /api/get-attendance.php?from=2024-01-01&to=2024-01-31
```

For complete API documentation, see [API_DOCS.md](API_DOCS.md)

---

## 🗄️ Database Schema

### Main Tables

| Table | Records | Description |
|-------|---------|-------------|
| `admin_users` | Admin accounts | Authentication |
| `workers` | Employees | Worker information |
| `attendance` | Daily records | Check-in/out tracking |
| `leaves` | Leave requests | Vacation/sick leaves |
| `departments` | Departments | Company structure |
| `positions` | Job positions | Roles and salaries |
| `activity_logs` | Audit logs | System activities |
| `system_settings` | Configuration | App settings |
| `face_encodings` | Face data | Recognition data |
| `notifications` | Notifications | System alerts |

For complete schema, see `database.sql`

---

## 🔒 Security

### Implemented Security Features

✅ **Authentication**
- Password hashing with bcrypt
- Session management
- CSRF protection
- Login attempt limiting

✅ **Database**
- Prepared statements
- SQL injection prevention
- Input sanitization
- Foreign key constraints

✅ **Files**
- File type validation
- Size limits
- Secure upload paths
- Access restrictions

✅ **API**
- Authentication required
- Input validation
- Rate limiting
- Error handling

### Security Best Practices

1. **Change default credentials** immediately
2. **Use HTTPS** in production
3. **Keep PHP and MySQL updated**
4. **Regular backups** of database
5. **Monitor activity logs**
6. **Use strong passwords**
7. **Limit admin access**
8. **Update Python packages**

---

## 🔧 Troubleshooting

### Common Issues

#### Issue: Camera Not Working
**Solution:**
- Check browser permissions
- Verify camera hardware
- Try different browser
- Clear browser cache

#### Issue: Face Not Recognized
**Solution:**
- Ensure good lighting
- Position face clearly
- Re-register face
- Check Python service

#### Issue: Database Connection Error
**Solution:**
- Verify credentials in `db.php`
- Check MySQL is running
- Confirm database exists
- Test connection

#### Issue: Permission Denied
**Solution:**
```bash
chmod -R 755 assets/uploads/
chmod -R 755 logs/
chown -R www-data:www-data /path/to/laberion/
```

#### Issue: Python Not Found
**Solution:**
```bash
which python3
# Update PYTHON_PATH in .env
```

### Debug Mode

Enable debug mode in `.env`:
```env
APP_ENV=development
APP_DEBUG=true
```

### Log Files

Check these log files:
- `logs/error.log` - PHP errors
- `logs/activity.log` - User activities
- `logs/face_recognition.log` - Face recognition

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- **PHP**: Follow PSR-12
- **JavaScript**: Use ES6+ features
- **CSS**: BEM methodology
- **Comments**: Document complex logic
- **Security**: Always validate input

---

## 📝 Changelog

### Version 1.0.0 (2024-01-15)
- ✨ Initial release
- 🎉 Face recognition integration
- 📊 Dashboard with analytics
- 👥 Worker management
- 🕒 Attendance tracking
- 🌴 Leave management
- 📈 Reports and exports

---

## 📜 License

This project is proprietary software. All rights reserved.

```
Copyright (c) 2024 Laberion
All rights reserved.
```

For licensing inquiries, contact: **license@laberion.com**

---

## 🆘 Support

### Documentation
- 📖 [User Guide](USER_GUIDE.md)
- 🔧 [Admin Guide](ADMIN_GUIDE.md)
- 🛠️ [Developer Guide](DEVELOPER_GUIDE.md)
- 🐛 [Troubleshooting](TROUBLESHOOTING.md)

### Get Help
- 📧 **Email:** support@laberion.com
- 💬 **Live Chat:** [chat.laberion.com](https://chat.laberion.com)
- 📞 **Phone:** +1 (555) 123-4567
- 🌐 **Website:** [www.laberion.com](https://www.laberion.com)

### Community
- 💻 **GitHub Issues:** Report bugs
- 💡 **Feature Requests:** Suggest improvements
- 📚 **Wiki:** Knowledge base
- 🎓 **Tutorials:** Video guides

---

## 🌟 Acknowledgments

Special thanks to:

- **OpenCV Community** - Computer vision library
- **face_recognition** - Face recognition library
- **Bootstrap Team** - UI framework
- **PHP Community** - Server-side scripting
- **MySQL Team** - Database management
- **Contributors** - Everyone who helped

---

## 📊 Project Stats

![GitHub stars](https://img.shields.io/github/stars/laberion/wms?style=social)
![GitHub forks](https://img.shields.io/github/forks/laberion/wms?style=social)
![GitHub issues](https://img.shields.io/github/issues/laberion/wms)
![GitHub PRs](https://img.shields.io/github/issues-pr/laberion/wms)

---

## 🚀 Roadmap

### Version 1.1 (Q2 2024)
- [ ] Mobile app (iOS/Android)
- [ ] Multi-language support
- [ ] Advanced analytics
- [ ] Payroll integration

### Version 1.2 (Q3 2024)
- [ ] Biometric integration
- [ ] Cloud deployment
- [ ] API v2
- [ ] Performance management

### Version 2.0 (Q4 2024)
- [ ] AI-powered insights
- [ ] Predictive analytics
- [ ] Integration with HR systems
- [ ] Advanced reporting

---

<div align="center">

### Made with ❤️ by Laberion Team

**[⬆ Back to Top](#-laberion-workforce-management-system-wms)**

---

⭐ **Star this project if you find it helpful!** ⭐

</div>