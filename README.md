## Proje Gereksinimleri

- PHP v8.1 veya üzeri.
- PHP v8.1 ile uyumlu çalışan Composer
- Docker

## Kurulum Adımları

Laravel bağımlılıklarını composer yardımıyla kuralım.

``
composer install
``

Docker container kurulum

``
./vendor/bin/sail up
``

Database Migration

``
./vendor/bin/sail artisan migrate:refresh --seed
``

Gerekli kurulumlar yapıldıktan sonra proje dizininde yer alan
``
Idesoft.postman_collection.json
``
postman koleksiyonunu import ederek ilgili servisleri kullanmaya başlayabilirsiniz.

Auth servisinden auth olup koleksiyon ayarlarından 
``
ACCESS_TOKEN
``
değerini güncellemeyi unutmayınız.

Router List
- sipariş ekleme
- sipariş silme
- sipariş listeleme
- indirim hesaplama


Gerekli bütün servisler postman koleksiyonuna eklenmiştir.

- Kullanıcının yaptığı alışveriş toplamı değeri sipariş alındıktan veya sipariş silindikten sonra laravel Horizon kuyruğuna aktarılarak arkaplanda eklenmektedir/çıkarılmaktadır.
- sİndirim yapısı modüler şekilde hazırlanmış olup ekleme/çıkarılma yapılabilecek şekilde yazılmıştır.
