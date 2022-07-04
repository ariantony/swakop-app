# Aplikasi Swadaya Koperasi

## Step By Step Instalasi Aplikasi
----------
1. [Install Node.js](https://nodejs.org/en/download/) -  [Composer](https://getcomposer.org/download/) - [Git](https://git-scm.com/downloads)
2. Buka terminal (GitBash, Powershell)
3. Paste syntax dibawah ini:

    ```
    git clone https://github.com/mesayusriana12/swakop-app.git
    cd swakop-app
    composer install
    npm install
    cp .env.example .env
    ```

4. Edit file ``.env`` sesuai kebutuhan (untuk keperluan database), contoh jika menggunakan mySQL:

    ```
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=swakop-app
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. Simpan perubahan pada file .env lalu kembali ke terminal, jalankan perintah berikut:

    ```
    php artisan migrate:fresh --seed
    npm run dev
    ```

6. Buka terminal baru (GitBash, Powershell)
7. Paste syntax dibawah ini:

    ```
    php artisan serve
    ```

8. Buka browser dan masukkan alamat URL dibawah ini:

    ```
    http://localhost:8000
    ```
