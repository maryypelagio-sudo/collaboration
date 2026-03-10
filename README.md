# iReply Inventory Management System

This is the inventory management application for iReply Back Office Services, built with **Laravel 11, Inertia.js, and Svelte 5**.

## Application Requirements
- PHP 8.2+
- Composer
- Node.js & npm
- MySQL (XAMPP recommended)

## Collaborator Setup Instructions

Since `.env` files and `vendor/` / `node_modules/` folders are ignored by Git for security and size, collaborators will need to follow these steps to get the project running locally after cloning or branching:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/maryypelagio-sudo/collaboration.git
   cd collaboration
   ```

2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies:**
   ```bash
   npm install
   ```

4. **Set Up the Environment File:**
   Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
   
   Once copied, open your new `.env` file and **update your database credentials** to point to your local MySQL (for example, XAMPP):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventory_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate the App Key:**
   ```bash
   php artisan key:generate
   ```

6. **Create the Local Database:**
   Ensure you create a MySQL database named `inventory_db` in your local SQL manager (like SQL-Front or phpMyAdmin).

7. **Run Database Migrations:**
   ```bash
   php artisan migrate
   ```

8. **Build Frontend Assets and Start Development Servers:**
   In one terminal, run the frontend Vite dev server:
   ```bash
   npm run dev
   ```
   In a second terminal, start the Laravel backend:
   ```bash
   php artisan serve
   ```

You can now view the app at `http://localhost:8000`!
