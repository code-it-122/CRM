# CRM System Requirements

## Project Overview

A centralized PHP-based CRM system developed to manage Sales, Billing, and HR operations from a single platform. The system reduces manual work by organizing customer, product, employee, and sales information while providing secure role-based access for different users.

---

## User Roles

### 1. Admin
- Full access to all modules.
- Manage users and system data.
- Access Admin Dashboard.
- Manage Products, Customers, Sales, Billing, Employees, Attendance, and Leave records.

### 2. Sales User
- Access Sales Dashboard.
- Manage Customers.
- View Products.
- Manage Sales.
- Generate and manage Invoices.

### 3. HR User
- Access HR Dashboard.
- Manage Employees.
- Manage Attendance.
- Manage Leave records.

---

# Modules

## 1. Login Module

The Login module provides secure authentication for all users using PHP sessions. Users are redirected to their respective dashboards based on their assigned role.

Features:
- User Login
- Session Management
- Role-Based Authentication
- Logout

---

## 2. Admin Module

The Admin module provides complete control over the CRM system.

Features:
- User Management (CRUD)
- Dashboard Overview
- Access to all modules
- Role Management

---

## 3. Product Module

This module manages company products.

Features:
- Add Product
- View Products
- Edit Product
- Delete Product
- Store Product Name, Category, Price, Stock, and Description

---

## 4. Customer Module

This module manages customer information.

Features:
- Add Customer
- View Customers
- Edit Customer
- Delete Customer
- Store customer contact details

---

## 5. Sales Module

This module records product sales.

Features:
- Add Sales
- View Sales
- Edit Sales
- Delete Sales
- Select Customer
- Select Products
- Calculate Total Amount
- Automatically Update Product Stock

---

## 6. Billing Module

This module manages invoices generated from sales.

Features:
- Create Invoice
- View Invoice
- Edit Invoice
- Delete Invoice
- Store Invoice Details
- Maintain Payment Status

---

## 7. HR Module

The HR module manages employee records and attendance.

### Employee Management
- Add Employee
- View Employees
- Edit Employee
- Delete Employee

### Attendance Management
- Mark Attendance
- View Attendance
- Edit Attendance
- Delete Attendance

### Leave Management
- Apply Leave
- View Leave Requests
- Edit Leave Details
- Delete Leave Records

---

## 8. Dashboard

Separate dashboards are available for each user role.

### Admin Dashboard
Displays:
- Total Users
- Total Products
- Total Customers
- Total Employees

### Sales Dashboard
Displays:
- Total Products
- Total Customers
- Total Sales

### HR Dashboard
Displays:
- Total Employees
- Active Employees
- Inactive Employees
- Pending Leave Requests
- Recently Added Employees

---

# Role-Based Access

### Admin
- Full access to all modules.

### Sales User
- Sales Dashboard
- Customers
- Products (View)
- Sales
- Billing

### HR User
- HR Dashboard
- Employees
- Attendance
- Leave

---

# System Workflow

### Authentication Workflow
- User Login
- Session Verification
- Redirect to Role Dashboard
- Logout

### Sales Workflow
- Add Customer
- Select Products
- Record Sale
- Calculate Total Amount
- Update Product Stock
- Generate Invoice

### HR Workflow
- Add Employee
- Manage Employee Records
- Mark Attendance
- Manage Leave Records

---

# Technologies Used

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- XAMPP