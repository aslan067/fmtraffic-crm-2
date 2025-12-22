# Minimal PHP 8.1 Multi-tenant CRM Core

Bu repo, tek domain üzerinde çalışan firma bazlı (multi-tenant) basit bir CRM çekirdeği örneğidir. Framework kullanılmaz, yalnızca düz PHP + MySQL 8 ve PDO tercih edilir.

## Klasör Yapısı
- `public/index.php`: Front controller ve router tetikleyici
- `app/Core`: Router, Auth ve DB çekirdeği
- `app/Controllers`: HTTP controller dosyaları
- `app/Middleware`: Middleware katmanı (AuthMiddleware)
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
- `POST /logout`: Oturum sonlandırma (CSRF korumalı)
- `GET /products`: Oturum + `permission:product.view` kontrolü ile korunur

## Güvenlik Notları
- PDO + prepared statements kullanılır.
- CSRF token kontrolü, `csrf_token()` helperı ile yapılır.
- Çıktılar `htmlspecialchars` ile escape edilir.
- Tüm iş tablolarında `company_id` zorunludur.

## Rol & Yetki Mantığı
- Roller sistem genelinde (`company_id` NULL) veya firmaya özel (`company_id` dolu) olabilir.
- Kullanıcı-rolleri `user_roles`, rol-yetkileri `role_permissions` üzerinden bağlanır.
- Yetkiler `permissions.key` (ör. `product.view`) değerleri ile kontrol edilir.
- `App\Core\Auth` içinde:
  - `hasRole($roleName)`: Kullanıcının rolünü doğrular.
  - `hasPermission($permissionKey)`: Rol-permission ilişkisi üzerinden yetki kontrolü yapar.
- Middleware kullanımı: `permission:<key>` formatı ile route tanımına eklenir. Örnek: `/products` rotası `permission:product.view` ister.

## Paket ve Abonelik Yapısı
- Paketler (`packages`) tüm sistem için tanımlanır; kullanıcı sınırı, ürün ve cari limitleri içerir.
- Abonelikler (`subscriptions`) firma + paket ilişkisini ve durumunu (trial/active/suspended/expired) tutar.
- Örnek Starter/Pro/Premium paketleri ve deneme aboneliği seed olarak eklenmiştir.
- Bu aşamada paket limitleri sadece altyapıdır, enforcement sonraki adımda.

## Hata Yönetimi
- Veritabanı bağlantı hataları kullanıcıya basit mesaj, loglara detay verir.
- Auth akışında başarısız girişler için basit flash mesajı ve yönlendirme yapılır.

## Geliştirme Notları
- PHP 8.1 ile uyumludur.
- Ek tablo ve modüller için aynı multi-tenant desenini (company_id zorunlu) sürdürün.
