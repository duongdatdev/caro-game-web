# Hướng dẫn chạy dự án
## Yêu cầu
- PHP 8.x
- Composer
- Node.js và npm
## Cài đặt
1. Sao chép nội dung file .env.example thành `.env` và cấu hình thông tin phù hợp.
2. Cài đặt các gói PHP bằng lệnh:
```bash
composer install
```
3. Cài đặt các gói front-end
```bash
npm install
```
4. Làm theo các bước sau
    Comment dòng Config::load() trong app/Providers/AppServiceProvider.php sau đó chạy lệnh :
    ```bash
    php artisan vendor:publish --provider="OpenAdmin\Admin\Config\ConfigServiceProvider"
    php artisan migrate
    ```
## Chạy dự án:
1. Phía server: php artisan serve
2. Phía client: npm run dev