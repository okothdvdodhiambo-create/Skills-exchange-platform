# Community Skill Exchange and Service Matching Platform

A PHP/MySQL service-oriented web application for community skill sharing and service matching.

## Features

- User registration and login
- Member dashboards and profile management
- Offered and wanted skills management
- Rule-based matching engine
- Exchange request workflow with accept/reject/complete states
- Notification tracking
- Admin dashboard with member and category management
- RESTful JSON APIs per SOA service

## Project Structure

```text
community-skill-exchange/
├── index.php
├── login.php
├── register.php
├── logout.php
├── config/
│   └── database.php
├── gateway/
│   └── index.php
├── includes/
│   └── bootstrap.php
├── services/
│   ├── user/UserService.php
│   ├── skill/SkillService.php
│   ├── matching/MatchingService.php
│   ├── exchange/ExchangeService.php
│   ├── notification/NotificationService.php
│   └── reporting/ReportingService.php
├── frontend/
│   ├── css/style.css
│   ├── js/app.js
│   └── images/
├── member/
│   ├── dashboard.php
│   ├── profile.php
│   ├── my_skills.php
│   ├── find_skills.php
│   ├── matches.php
│   ├── requests.php
│   ├── exchanges.php
│   └── notifications.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── categories.php
│   └── reports.php
├── database/
│   └── community_skill_exchange.sql
├── README.md
└── .
```

## Installation (XAMPP / Windows)

1. Install XAMPP and ensure Apache and MySQL are running.
2. Copy the project folder into `htdocs`.
3. Create the MySQL database and import `database/community_skill_exchange.sql`.
4. Update `config/database.php` if your MySQL credentials differ from the defaults.
5. Open the app in a browser using:
   `http://localhost/community-skill-exchange/index.php`

## Default Demo Accounts

- Member: `alice@example.com` / `password123`
- Member: `david@example.com` / `password123`
- Member: `mary@example.com` / `password123`
- Admin: `admin@community.test` / `admin123`

## SOA Services Documentation

### 1. User Management Service
Handles registration, login, logout, profile updates, and member listing.

### 2. Skill Management Service
Handles skill categories, offered skills, wanted skills, and CRUD operations on member skill data.

### 3. Skill Matching Service
Compares offered and wanted skills between users to identify mutual exchange opportunities and display match reasons.

### 4. Exchange Management Service
Creates exchange requests, tracks status, and updates them to accepted, rejected, or completed states.

### 5. Notification Service
Creates and reads in-app notifications whenever exchange actions occur.

### 6. Reporting Service
Collects dashboard statistics, status breakdowns, and skill popularity data for admin reporting.

## API Overview

- `POST /community-skill-exchange/gateway/index.php/api/users/register`
- `POST /community-skill-exchange/gateway/index.php/api/users/login`
- `GET /community-skill-exchange/gateway/index.php/api/users/profile`
- `PUT /community-skill-exchange/gateway/index.php/api/users/profile`
- `POST /community-skill-exchange/gateway/index.php/api/skills/offer`
- `POST /community-skill-exchange/gateway/index.php/api/skills/wanted`
- `GET /community-skill-exchange/gateway/index.php/api/skills`
- `GET /community-skill-exchange/gateway/index.php/api/matches`
- `POST /community-skill-exchange/gateway/index.php/api/exchanges/request`
- `GET /community-skill-exchange/gateway/index.php/api/exchanges`
- `PUT /community-skill-exchange/gateway/index.php/api/exchanges/{id}/accept`
- `PUT /community-skill-exchange/gateway/index.php/api/exchanges/{id}/reject`
- `PUT /community-skill-exchange/gateway/index.php/api/exchanges/{id}/complete`
- `GET /community-skill-exchange/gateway/index.php/api/notifications`
- `GET /community-skill-exchange/gateway/index.php/api/reports/dashboard`

## Notes

This project is intentionally kept beginner-friendly and service-oriented so it can be demonstrated locally in a classroom or academic setting.
