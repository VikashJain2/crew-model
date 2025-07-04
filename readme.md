# CrewSync - Professional Crew Management System

CrewSync is a web-based platform designed to help organizations and employees efficiently manage crew profiles, professional information, and work experience. Built with PHP and MySQL, CrewSync offers a secure, intuitive interface for storing and updating employee data.

---

## Features

- **User Registration & Login:** Secure sign-up and authentication for employees.
- **Personal Information Management:** Add and update personal details (name, contact, DOB, etc.).
- **Professional Information Management:** Manage employment details (employee ID, department, designation, joining date).
- **Work Experience Tracking:** Add, edit, and remove previous job experiences, including roles, durations, and technologies used.
- **Technology Tagging:** Assign technologies to each work experience for skill tracking.
- **Responsive Dashboard:** Modern UI with sidebar navigation for easy access to all features.

---

## Prerequisites

- [XAMPP](https://www.apachefriends.org/index.html) (includes PHP 8+, Apache, MySQL)
- Web browser (Chrome, Firefox, Edge, etc.)

---

## Installation & Setup

1. **Install XAMPP**

   Download and install XAMPP from [here](https://www.apachefriends.org/index.html).

2. **Clone or Download the Project**

   Place the `crew-model` folder inside your XAMPP `htdocs` directory.

3. **Import the Database**

   - Open [phpMyAdmin](http://localhost/phpmyadmin).
   - Create a new database named `crew_model`.
   - Import the SQL file: `db/crew_model.sql`.

4. **Configure Database Connection**

   Edit `config/db.php` if your MySQL username or password is different from the default (`root` / no password).

5. **Start XAMPP**

   Open the XAMPP Control Panel and start both Apache and MySQL.

6. **Access the Application**

   Open your browser and go to:  
   [http://localhost/crew-model/index.php](http://localhost/crew-model/index.php)

---

## Usage Example

### 1. Register a New Employee

- Click **Register Now** on the login page.
- Fill in your desired username and password.
- Submit the form to create your account.

### 2. Login

- Enter your username and password on the login page.
- Click **Sign In** to access your dashboard.

### 3. Add Personal Information

- Navigate to **Personal Info** from the sidebar.
- Enter your full name, date of birth, contact number, and address.
- Click **Save**.

**Example:**
```
Full Name:   John Doe
DOB:         1990-05-15
Contact:     +1-555-1234
Address:     123 Main St, Springfield
```

### 4. Add Professional Information

- Go to **Professional Info**.
- Enter your employee ID, department, designation, and joining date.
- Click **Save**.

**Example:**
```
Employee ID:   EMP102
Department:    IT
Designation:   Software Engineer
Joining Date:  2022-01-10
```

### 5. Add Work Experience

- Click **Work Experience** in the sidebar.
- Add previous job details, including company, role, duration, and technologies used.
- Click **Add Experience**.

**Example:**
```
Company:        Tech Solutions Inc.
Role:           Backend Developer
Duration:       2018-2021
Technologies:   PHP, MySQL, Laravel
```

### 6. Logout

- Click **Logout** in the sidebar to securely end your session.

---

## Project Structure

```
crew-model/
│
├── assets/
│   └── css/
├── config/
│   └── db.php
├── db/
│   └── crew_model.sql
├── includes/
│   └── auth.php
├── dashboard.php
├── experience.php
├── index.php
├── logout.php
├── personal-info.php
├── professional-info.php
├── registration.php
└── sidebar.php
```

---

## Security Notes

- Passwords are hashed using PHP's `password_hash`.
- All database queries use prepared statements to prevent SQL injection.
- Session management ensures secure authentication.

---

## Need Help?

For support, contact:

**Name:** Vikash Jain   
**Email:** [vikashjain2205@gmail.com](mailto:vikashjain2205@gmail.com)   
**Phone:** 9079393821  

---

**Thank you for using CrewSync!**