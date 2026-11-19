# SiUKS – Sistem Informasi Kesehatan Siswa

> A Laravel‑based web application that helps schools manage student health records, including disease diagnoses and medication tracking. This README is intended for the **S4‑04 Repository README Lengkap** deliverable.

---

## Table of Contents
- [Demo](#demo)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [Repository Structure](#repository-structure)
- [Generating PDF (S4‑04)](#generating-pdf-s4-04)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

---

## Demo
**(Insert screenshot of the health dashboard here)**
---

## Features
- **Import siswa per kelas** – pilih kelas, semua siswa otomatis terdaftar tanpa input manual satu per satu.
- **Diagnosa penyakit** – dropdown dengan pilihan penyakit umum; pengguna dapat menambah entri custom bila diperlukan.
- **Pencatatan obat** – dropdown obat, dengan kolom tambahan untuk dosis dan catatan.
- **Manajemen peran** – admin dan wali kelas dapat mengelola data sesuai hak akses masing‑masing.
- **Laporan PDF** – menghasilkan laporan lengkap (S4‑04) yang mencakup README dan placeholder screenshot.

---

## Tech Stack
| Layer | Technology |
|-------|------------|
| Backend | Laravel 10 (PHP ^8.2) |
| Frontend | Blade + Tailwind CSS |
| Database | MySQL / MariaDB |
| PDF Generation | Microsoft Edge (headless) + Node.js script |
| CI/CD | GitHub Actions (optional) |

---

## Prerequisites
- **PHP** `^8.2`
- **Composer** `^2.5`
- **Node.js** `>=18` and **npm**
- **MySQL** (or compatible) database
- **Microsoft Edge** (installed, used for headless PDF conversion)

---

## Installation
```bash
# Clone the repository
git clone https://github.com/ineuherlina44-jpg/SiUKS.git
cd SiUKS

# Install PHP dependencies
composer install

# Install front‑end assets
npm install && npm run dev

# Copy environment file and set database credentials
cp .env.example .env
# edit .env → set DB_DATABASE, DB_USERNAME, DB_PASSWORD, etc.

# Generate application key
php artisan key:generate

# Run migrations and (optionally) seed demo data
php artisan migrate --seed
```

---

## Running the Application
```bash
php artisan serve
# Application will be available at http://127.0.0.1:8000
```

---

## Repository Structure
```
/app                – Laravel application code (controllers, models, …)
/database
   ├─ migrations   – Database schema
   └─ seeders      – Seed data (example students, diseases, medicines)
/resources
   ├─ views        – Blade templates (dashboard, forms)
   ├─ js / css     – Front‑end assets
/public             – Public assets and entry point
/scratch            – Helper scripts (PDF generation, docs)
```

---

## Generating PDF (S4‑04)
The **S4‑04** deliverable is a PDF that contains the full repository README.
A helper script (`scratch/build_s4_04_pdf.js`) already exists.
```bash
cd scratch
node build_s4_04_pdf.js
```
The generated file will be saved as:
`C:/Users/Hype GLK/.gemini/antigravity/brain/4301e258-6d82-4b06-9bd9-6046bee7df1f/S4-04_REPOSITORY_README_LENGKAP_SIUKS.pdf`

Replace the placeholder tags (`[ LETAKKAN DI SINI SCREENSHOT ... ]`) in the PDF using any PDF editor if you need to insert actual screenshots.

---

## Testing
```bash
php artisan test          # Backend tests (PHPUnit / Pest)
npm run test:unit        # Front‑end unit tests (if any)
```

---

## Contributing
1. Fork the repository.
2. Create a feature branch (`git checkout -b feat/your-feature`).
3. Commit your changes with clear messages.
4. Push the branch and open a Pull Request.

Please follow the Laravel coding standards and run `php artisan format` before submitting.

---

## License
Distributed under the **MIT License**. See `LICENSE` for details.

---

## Contact
- **Author**: Your Name – [GitHub Profile](https://github.com/ineuherlina44-jpg)
- **Issue Tracker**: https://github.com/ineuherlina44-jpg/SiUKS/issues
