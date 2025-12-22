# Minimal PHP 8.1 Multi-tenant CRM Core

Bu repo, tek domain üzerinde çalışan firma bazlı (multi-tenant) basit bir CRM çekirdeği örneğidir. Framework kullanılmaz, yalnızca düz PHP + MySQL 8 ve PDO tercih edilir.

## Klasör Yapısı
- `public/index.php`: Front controller ve router tetikleyici
- `app/Core`: Router, Auth ve DB çekirdeği
- `app/Controllers`: HTTP controller dosyaları
- `app/Middleware`: Middleware katmanı (AuthMiddleware, PermissionMiddleware)
- `app/Models`: Veri erişim sınıfları
- `app/Views`: Basit PHP view dosyaları
- `config`: Uygulama ve route tanımları
- `database/migrations.sql`: Şema ve seed SQL

## Kurulum
1. Depoyu klonlayın ve dizine girin.
2. `.env.example` dosyasını `.env` olarak kopyalayın ve MySQL bilgilerinizi doldurun:
   ```bash
   cp .env.example .env
   ```
3. Veritabanı oluşturun ve migration dosyasını çalıştırın:
   ```bash
   mysql -u <kullanici> -p -e "CREATE DATABASE crm_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u <kullanici> -p crm_app < database/migrations.sql
   ```
4. PHP yerel sunucusunu başlatın:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Tarayıcıda `http://localhost:8000/login` adresine gidin.

## Varsayılan Kullanıcı
Migration dosyası şu admin kullanıcısını ekler:
- E-posta: `admin@example.com`
- Şifre: `admin123!`

> Güvenlik için bu şifreyi giriş yaptıktan sonra mutlaka değiştirin.

## Route Özeti
- `GET /login`: Giriş formu
- `POST /login`: Kimlik doğrulama ve session oluşturma
- `GET /dashboard`: Oturum gerektirir
- `GET /products`: Oturum + `product.view` yetkisi gerektirir
- `POST /logout`: Oturum sonlandırma (CSRF korumalı)

## Güvenlik Notları
- PDO + prepared statements kullanılır.
- CSRF token kontrolü, `csrf_token()` helperı ile yapılır.
- Çıktılar `htmlspecialchars` ile escape edilir.
- Tüm iş tablolarında `company_id` zorunludur.

## Hata Yönetimi
- Veritabanı bağlantı hataları kullanıcıya basit mesaj, loglara detay verir.
- Auth akışında başarısız girişler için basit flash mesajı ve yönlendirme yapılır.

## Rol & Yetki Mantığı
- Roller `roles` tablosunda tutulur. `company_id` NULL ise sistem rolü, dolu ise firmaya özel roldür.
- Yetkiler `permissions` tablosunda `key` alanıyla saklanır.
- `role_permissions` ile rolün sahip olduğu izinler tanımlanır.
- Kullanıcıya rol ataması `user_roles` tablosu ile yapılır.
- `Auth::hasRole($name)` ve `Auth::hasPermission($key)` metodları, oturum açmış kullanıcı için firma bağlamında kontrol yapar.

## Paket ve Abonelik Yapısı
- Paketler `packages` tablosunda saklanır (Starter/Pro/Premium seed olarak eklenir).
- Şirket abonelikleri `subscriptions` tablosu ile takip edilir (`status`: trial, active, suspended, expired).
- Bu aşamada paket limitleri sadece altyapı olarak eklenmiştir, enforcement sonraki adımda yapılacaktır.

## Geliştirme Notları
- PHP 8.1 ile uyumludur.
- Ek tablo ve modüller için aynı multi-tenant desenini (company_id zorunlu veya NULL = sistem nesnesi) sürdürün.
