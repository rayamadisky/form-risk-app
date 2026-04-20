FROM php:8.2-cli

# Install tool tambahan & ekstensi PostgreSQL (wajib buat nyambung ke Supabase)
RUN apt-get update -y && apt-get install -y libpq-dev unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Ambil Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Pindah ke dalam folder server
WORKDIR /app

# Copy semua file Laravel lu dari GitHub ke server
COPY . .

# Install kebutuhan Laravel
RUN composer install --no-dev --optimize-autoloader

# Abaikan perintah bawaan, kita suruh server bikin tabel & nyalain aplikasi
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT