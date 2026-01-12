# ✈️ DDS Web Application – Defect & Deferment System

## 📌 Project Overview
The **DDS Web Application (Defect & Deferment System)** is a web-based platform developed during my internship to manage **aircraft defects and operational limitations** in a structured, secure, and efficient way.

The application centralizes defect tracking, limitation management, audit logging, and data export, replacing manual or spreadsheet-based processes with a modern web solution.

---

## 🎯 Objectives
- Centralize aircraft defect and limitation data  
- Ensure **role-based access control** (Admin, Technician, Viewer)  
- Improve traceability through **audit logs**  
- Provide **Excel export** for reporting and analysis  
- Enhance data integrity, security, and usability  

---

## 🛠️ Technologies Used
- **Frontend**: HTML5, CSS3, Bootstrap, JavaScript  
- **Backend**: PHP (PDO)  
- **Database**: MySQL  
- **Security**: Prepared statements, sessions  
- **Export**: PhpSpreadsheet (Excel)  
- **Environment**: Apache (XAMPP / WAMP)  

---

## 👥 User Roles & Permissions
| Role | Permissions |
|------|------------|
| **Admin** | Full access (CRUD, users, logs, exports) |
| **Technician** | Manage own defects & limitations |
| **Viewer** | Read-only access |

---

## 🔑 Main Features

### 🔐 Authentication & Authorization
- Secure login system  
- Role-based access control  
- Session handling  
- Remember me functionality  

### 🛠️ Defect Management
- Add, edit, delete defects  
- Open / Closed status tracking  
- Technician ownership enforcement  

### ⚠️ Limitation Management
- Linked to defects  
- Role-restricted modifications  
- Automatic calculations  

### 📊 Reporting & Export
- Recap dashboard  
- Excel export  
- Filtered and structured data  

### 🧾 Audit Logging
- Track:
  - Defect operations
  - Limitation operations
  - User administration actions  

### 🌙 UI Enhancements
- Dark mode  
- Responsive design  
- Clean interface  

---

## 🗄️ Database Structure
The database is structured around:
- Users & roles  
- Aircraft & stations  
- Defects  
- Limitations  
- Audit logs  

> UML diagrams are included in the internship report.

---

## 🔒 Security Measures
- PDO prepared statements (SQL injection protection)  
- Server-side validation  
- Role-based access enforcement  
- Secure sessions  


---

## 🚀 Installation & Setup
1. Clone the repository:
```bash
git clone https://github.com/your-username/tunisair-dds-web.git
 ```

2.Move the project to your web server directory

3.Create the database and import the SQL file

4.Configure database access in config.php

5.Start Apache & MySQL

👤 Author
---
Samer Rebai

Intern
