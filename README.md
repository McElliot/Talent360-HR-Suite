# 🚀 Talent360 HR Suite

![Talent360 Banner](public/images/banner.png) **(Add a screenshot later)*

A **modern HR management system** built with Laravel, Livewire, and FluxUI. Features include:
- Psychometric testing engine
- 360° employee feedback
- Payroll & leave management *(coming soon)*
- AI-powered analytics

## 🛠 Tech Stack
- **Backend**: Laravel 12
- **Frontend**: Livewire 3, FluxUI, Alpine.js
- **Database**: MySQL/PostgreSQL
- **Export/Import**: Laravel Excel

## 📦 Installation
```bash
# Clone the repo
git clone https://github.com/McElliot/Talent360-HR-Suite.git

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations (after setting up your DB)
php artisan migrate --seed
