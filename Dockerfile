<<<<<<< Updated upstream
# 1. استخدام نسخة PHP الرسمية الخفيفة والمستقرة
FROM php:8.2-cli-alpine

# 2. تثبيت الأدوات اللازمة وإضافات GMP و BCMath الحريجة لعمل البوت ومكتبة CCXT
RUN apk add --no-cache \
    gmp-dev \
    git \
    unzip \
    && docker-php-ext-install gmp bcmath

# 3. تثبيت أداة Composer داخل السيرفر
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. تحديد مجلد العمل داخل السيرفر
WORKDIR /app

# 5. نسخ ملفات المشروع بالكامل إلى السيرفر
COPY . .

# 6. تثبيت المكتبات البرمجية تلقائياً
RUN composer install --no-dev --optimize-autoloader

# 7. الأمر النهائي لتشغيل البوت عند استيقاظ الـ Cron Job
=======
# 1. استخدام نسخة PHP الرسمية الخفيفة والمستقرة
FROM php:8.2-cli-alpine

# 2. تثبيت الأدوات اللازمة وإضافات GMP و BCMath الحريجة لعمل البوت ومكتبة CCXT
RUN apk add --no-cache \
    gmp-dev \
    git \
    unzip \
    && docker-php-ext-install gmp bcmath

# 3. تثبيت أداة Composer داخل السيرفر
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. تحديد مجلد العمل داخل السيرفر
WORKDIR /app

# 5. نسخ ملفات المشروع بالكامل إلى السيرفر
COPY . .

# 6. تثبيت المكتبات البرمجية تلقائياً
RUN composer install --no-dev --optimize-autoloader

# 7. الأمر النهائي لتشغيل البوت عند استيقاظ الـ Cron Job
>>>>>>> Stashed changes
CMD ["php", "bot.php"]