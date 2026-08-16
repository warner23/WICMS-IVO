# WICMS-IVO

**WICMS-IVO** is the installation-oriented public codebase for WICMS, a modular PHP/MySQL content-management and application platform developed as part of the wider WI ecosystem.

The project has evolved over a number of years from an earlier CMS into a broader systems platform with shared core services, administration, user management, package/plugin support, accessibility tooling and an installation system.

> **Project status:** Active development and ongoing public-repository audit.  
> This repository should not currently be treated as a drop-in production release without reviewing configuration, security and deployment requirements for your environment.

---

## What is WICMS?

WICMS is designed as a reusable foundation rather than a single-purpose website.

The core platform provides common services that other WI projects and packages can build on, including:

- Application bootstrap and shared core services
- PHP/MySQL data access
- User and member management
- Role and permission handling
- Administration interfaces
- Package/plugin registration
- Theme and UI infrastructure
- Installation and setup
- Accessibility tools
- Configuration and site settings
- Extensible modules and application packages

The aim is to avoid rebuilding the same underlying functionality for every application and instead provide a common platform on which more specialised systems can operate.

---

## Repository structure

```text
WICMS-IVO/
│
├── WIAdmin/       Administration, shared admin services and modules
├── WICore/        Main application bootstrap and core classes
├── WIInc/         Shared includes and application resources
├── WIInstall/     Installation and initial setup system
├── WIMembers/     Member/user-facing functionality
├── WITheme/       Themes, styles and frontend assets
│
├── index.php
├── login.php
├── alogin.php
└── README.md

