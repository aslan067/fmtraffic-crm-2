# Minimal PHP 8.1 Multi-tenant CRM Core

Bu repo, tek domain üzerinde çalışan firma bazlı (multi-tenant) basit bir CRM çekirdeği örneğidir. Framework kullanılmaz, yalnızca düz PHP + MySQL 8 ve PDO tercih edilir.

## Bootstrap & Path Standardı
- `BASE_PATH`, projenin fiziksel konumunu dinamik olarak temsil eder ve `public/index.php` içinde `define('BASE_PATH', realpath(__DIR__))` ile tanımlanır.
- Autoload ve tüm dosya erişimleri `BASE_PATH` üzerinden kurulduğu için proje **public_html**, **public_html/crm2** veya farklı bir sunucu altında aynı şekilde çalışır.
- `config/app.php` yalnızca konfigürasyon array'i döndürür; sınıf başlatmaz veya servis çağırmaz. Index sıralaması: hata raporlama → `BASE_PATH` → autoload → `config/app.php` yükleme → `Auth` oturum/nesne → `Router` dispatch.

## Klasör Yapısı
- `public/index.php`: Front controller ve router tetikleyici
- `app/Core`: Router, Auth ve DB çekirdeği
- `app/Controllers`: HTTP controller dosyaları
- `app/Middleware`: Middleware katmanı (AuthMiddleware)
- `app/Models`: Veri erişim sınıfları
- `app/Views`: Basit PHP view dosyaları
- `config`: Uygulama ve route tanımları
- `database/migrations.sql`: Şema ve seed SQL

## Bootstrap ve Giriş Noktası
- `public/index.php` tek giriş noktasıdır; uygulamanın tamamı buradan bootstrap edilir.
- Bootstrap adımları sırasıyla: hata raporlama, minimal autoload, `.env` yükleme, `config/app.php` okuma, çekirdek sınıfları hazır hale getirme, `Auth` oturum başlatma/nesne oluşturma, `Router` başlatma ve dispatch.
- `config/app.php` yalnızca yapılandırma değerleri döndürür; class instantiate veya servis çalıştırma içermez.
- Autoload kuralları App\\ namespace'ini otomatik olarak `app/` dizinine eşler, manuel `require` ihtiyacını azaltır.

## Limit Enforcement vs Feature Enforcement
- **Limit enforcement** (LimitService): Aktif abonelik ve paket limitlerine göre *nicelik* kontrolleri yapar. Örn. kullanıcı/ürün/cari ekleme sınırı.
- **Feature enforcement** (FeatureService + FeatureMiddleware): Paketin belirli modülleri (product, cari, offer, sale, purchase, stock) içerip içermediğini kontrol eder. “Yetki var ama paket yok” durumunu engeller.

> Bir işlemin yapılabilmesi için **yetki + paket + feature** koşullarının tamamı sağlanmalıdır.

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

## Veritabanı Senkronizasyonu (Önemli)
- Her güncellemeden sonra `database/migrations.sql` dosyasını mevcut veritabanına tekrar uygulayın. Böylece ürün kodu ve ürün grubu gibi kolonlar geride kalmaz.
- `products` veya `users` tabloları güncel şema ile uyumlu değilse SQL sorguları `p.code` / `p.product_group_id` kolonlarını bulamaz ve 500 hatası üretir.
- Super admin mimarisi gereği `users.company_id` alanı **NULL** olabilir; foreign key kısıtları `ON DELETE SET NULL` olacak şekilde ayarlanmalıdır. Migration dosyası bu uyumu otomatik hale getirir.
- Bu düzeltme p.code / p.description SQL hatalarını kalıcı olarak çözer.

## Bilinen Kurulum Gereksinimleri
- **Super Admin hesabı manuel olarak oluşturulmalıdır.** Kurulumdan sonra `is_super_admin = 1` olacak şekilde bir kullanıcı ekleyin veya güncelleyin. Örnek SQL:
  ```sql
  INSERT INTO users (company_id, name, email, password_hash, status, is_super_admin)
  VALUES (NULL, 'Super Admin', 'superadmin@example.com', '$2y$12$Xh43DJXNBMzBK00dsr7mxOwyJxBh5pbC.cQGALpSavrX5CBQNpEj2', 'active', 1);
  ```
  Hazır hash `superadmin123!` şifresi içindir; farklı bir şifre kullanacaksanız `password_hash()` ile yeniden üretin.
- **Ürün Yönetimi için `database/migrations.sql` dosyasını mutlaka uygulayın.** Dosya, `products.product_group_id` kolonunu eklemek için uyumluluk sorgularını içerir; bu kolon eksik olduğunda ürün işlemleri 500 hatasına düşer.

### Bu düzeltmeler hangi hataları çözer?
- Middleware katmanında oluşan yönlendirme döngüleri artık 403 yanıtı ve “Bu modüle erişim yetkiniz yok.” mesajıyla durduruluyor.
- Super Admin hesabı bulunmadığında giriş denemeleri için daha belirgin “kullanıcı yok” uyarısı gösteriliyor.
- `products.product_group_id` kolonunun eksik olduğu veritabanlarında yaşanan 500 hataları için migration dosyası otomatik kolon ekleme/güncelleme adımlarını içeriyor.

## Ürün Yönetimi (MVP)
- **Kapsam:** `product_groups` ve `products` tabloları company_id zorunlu olacak şekilde tanımlıdır; ürün kodu firma bazında tekil, liste fiyatı zorunlu, stok miktarı negatif olamaz. Ürünler ürün gruplarına bağlanabilir; grup seçimi aktif gruplarla kısıtlanır ve form üzerinden yeni grup açılabilir.
- **Limit & feature ilişkisi:** Tüm ürün rotaları `auth` + `feature:product` + ilgili `permission:product.*` middleware’leri ile korunur. `LimitService::canAddProduct(company_id)` kontrolü `store` işleminde çalışır; limit doluysa “Ürün limitiniz dolmuştur. Paket yükseltiniz.” mesajı gösterilir ve kayıt yapılmaz.
- **Neden pasife alma var:** Silme yerine statü `passive` olarak güncellenir; geçmiş teklif/satış/satınalma kayıtlarıyla ilişki kopmaz, audit trail korunur.

## Cari Yönetimi (MVP)
- Tablolar:
  - `caris`: `type (customer|supplier|both)`, `name`, `tax_office`, `tax_number`, `status (active|passive)`, `company_id`
  - `contacts`: Cari bazlı iletişim kişileri
- Feature & permission: `feature:cari` + `permission:cari.view|cari.create|cari.edit` zorunludur.
- Limit kontrolü: `LimitService::canAddCari(company_id)` çağrısı `store` öncesinde yapılır; limit doluysa ekleme yapılmaz.
- CRUD kapsamı: Listeleme, oluşturma, düzenleme, pasife alma (silme yok).
- Rotalar: `/caris`, `/caris/create`, `/caris/store`, `/caris/{id}/edit`, `/caris/{id}/update`, `/caris/{id}/deactivate`

## Route Özeti
- `GET /login`: Giriş formu
- `POST /login`: Kimlik doğrulama ve session oluşturma
- `GET /dashboard`: Oturum gerektirir
- `POST /logout`: Oturum sonlandırma (CSRF korumalı)
- `GET /products`: Oturum + `permission:product.view` + `feature:product` kontrolü ile korunur
- `GET /products/create` & `POST /products/store`: Oturum + `feature:product` + `permission:product.create`
- `GET /products/{id}/edit` & `POST /products/{id}/update` & `POST /products/{id}/deactivate`: Oturum + `feature:product` + `permission:product.edit`
- `GET /caris`: Oturum + `feature:cari` + `permission:cari.view`
- `GET /caris/create` & `POST /caris/store`: Oturum + `feature:cari` + `permission:cari.create`
- `GET /caris/{id}/edit` & `POST /caris/{id}/update` & `POST /caris/{id}/deactivate`: Oturum + `feature:cari` + `permission:cari.edit`
- `GET /users/create` & `POST /users`: Paket limitine tabi kullanıcı oluşturma
- `GET /super-admin/companies`: Super Admin firma yönetimi
- `POST /super-admin/companies`: Super Admin firma oluşturma + paket başlatma
- `POST /super-admin/subscriptions`: Super Admin paket atama / abonelik güncelleme

## Ürün Yönetimi kısa test senaryosu
1. `admin@example.com` ile giriş yapın ve pakette `feature:product` bulunduğundan emin olun.
2. Dashboard menüsünden **Ürünler** sayfasına gidin; boş liste mesajını veya mevcut ürünleri görün.
3. “Yeni Ürün” butonu ile zorunlu alanları doldurun, isterseniz “Yeni Grup Adı” alanına bir değer yazın ve kaydedin; hem ürün hem grup oluşturulur.
4. Aynı ürün kodu ile tekrar kayıt deneyin; kod tekilliği uyarısını alın.
5. Ürünü düzenleyip durumu **pasif** yapın veya “Pasif Et” aksiyonunu kullanın; liste durumu `passive` olarak görünür.
6. Paket limiti dolu bir senaryo için `LimitService::canAddProduct` false döndüğünde “Ürün limitiniz dolmuştur. Paket yükseltiniz.” mesajı gösterilir ve kayıt yapılmaz.

## Company admin ürün test senaryosu
1. Company admin ile login ol.
2. `/products` aç.
3. Ürün listesi sorunsuz yüklenmeli.
4. Yeni ürün ekle:
   - `code` zorunlu.
   - Aynı firmada tekil olmalı.

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
- `Auth::isSuperAdmin()`: Sistem sahibini temsil eder; tüm yetkilere sahiptir ve company_id olmadan çalışır.
- Middleware kullanımı: `permission:<key>` formatı ile route tanımına eklenir. Örnek: `/products` rotası `permission:product.view` ister.

## Dinamik Menü & Yetki Senkronizasyonu
- Dashboard/menü tarafında öğeler, kullanıcının rol yetkilerine ve paket feature’larına göre gösterilir; yetkisi veya feature’ı olmayan öğeler UI’da gizlenir.
- UI gizleme yalnızca UX içindir; gerçek güvenlik backend middleware katmanında (AuthMiddleware + FeatureMiddleware) sağlanır.
- Zincir: Auth -> (FeatureService ile paket feature kontrolü) + (Permission kontrolü) → Menüde gösterme/ gizleme kararını verir.

## Super Admin vs Firma Admin
- **Super Admin**: Sistemin sahibidir, herhangi bir `company_id`'ye bağlı değildir. Tüm firmaları görür, firma oluşturur, paket atar, abonelik başlatır veya askıya alır. `/super-admin/*` rotalarına erişebilir.
- **Firma Admin**: Kendi `company_id` kapsamındaki kaynaklara erişir. Paket limitlerine tabidir ve yeni kullanıcı eklerken LimitService kontrolleri devreye girer.

## Paket ve Abonelik Yapısı
- Paketler (`packages`) tüm sistem için tanımlanır; kullanıcı sınırı, ürün ve cari limitleri içerir.
- Abonelikler (`subscriptions`) firma + paket ilişkisini ve durumunu (trial/active/suspended/expired) tutar.
- Örnek Starter/Pro/Premium paketleri ve deneme aboneliği seed olarak eklenmiştir.
- `App\Services\SubscriptionService` aktif aboneliği ve paket limitlerini çeker; `isSubscriptionActive` ile durum kontrol edilir.
- `App\Services\LimitService` kullanıcı/ürün/cari ekleme limitlerini merkezi olarak kontrol eder ve abonelik yoksa veya limit doluysa false döner.
- Kullanıcı eklemeden önce `LimitService::canAddUser($companyId)` çağrılır; limit doluysa ekleme yapılmaz ve “Paket kullanıcı limitiniz dolmuştur. Paket yükseltiniz.” mesajı gösterilir.

## Hata Yönetimi
- Login akışında aşağıdaki durumlar ayrı ayrı yakalanır ve flash mesajı ile kullanıcıya aktarılır:
  - Veritabanı bağlantısı kurulamıyor → “Sistem geçici olarak kullanılamıyor (DB bağlantısı yok).”
  - Kullanıcı bulunamadı
  - Şifre yanlış
  - Kullanıcı pasif
  - Beklenmeyen sistem hatası (loglanır, kullanıcıya sade mesaj gösterilir)
- CSRF hataları hem login hem CRUD formlarında yakalanır ve kullanıcıya net mesaj gösterilir.
- DB bağlantı hataları artık fatal çıkış yerine yakalanabilir exception üretir; login ekranında kırmızı uyarı olarak gösterilir.

## Geliştirme Notları
- PHP 8.1 ile uyumludur.
- Ek tablo ve modüller için aynı multi-tenant desenini (company_id zorunlu) sürdürün.
- Bu aşamadan sonra sistem gerçek SaaS davranışı gösterir; aktif abonelik ve paket limitleri olmadan kullanıcı/ürün/cari ekleme engellenir.
