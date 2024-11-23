# CoreMad

CoreMad adalah package PHP yang menyediakan metode-metode mudah untuk:
- Deteksi Bot/Crawler
- Deteksi tipe perangkat (Mobile/Tablet/Computer)
- Deteksi negara berdasarkan IP address

## Persyaratan

- PHP 8.0 atau lebih tinggi
- Composer untuk instalasi
- Database MaxMind GeoIP2 (untuk deteksi negara)

## Instalasi

Instalasi package menggunakan Composer:
```bash
composer require coremad/core
```

## Persiapan Database GeoIP

Untuk menggunakan fitur deteksi negara, Anda perlu:

1. Daftar akun di [MaxMind](https://www.maxmind.com/en/geolite2/signup)
2. Download database GeoLite2 Country dari MaxMind
3. Simpan file database (GeoLite2-Country.mmdb) di server Anda
4. Catat path ke file database untuk digunakan saat inisialisasi CoreMad

## Penggunaan

### Inisialisasi
```php
use CoreMad\Core\CoreMad;
// Tanpa deteksi negara
$coreMad = new CoreMad();
// Dengan deteksi negara (sertakan path ke database GeoIP)
$coreMad = new CoreMad('/path/to/GeoLite2-Country.mmdb');
```

### Deteksi Bot/Crawler
```php
// Cek apakah pengunjung adalah bot
if ($coreMad->isBot()) {
// Pengunjung adalah bot
$botName = $coreMad->getBotName();
echo "Bot terdeteksi: " . $botName;
} else {
echo "Pengunjung adalah manusia";
}
```

### Deteksi Perangkat
```php
// Cek tipe perangkat
if ($coreMad->isMobile()) {
echo "Pengguna menggunakan smartphone";
} elseif ($coreMad->isTablet()) {
echo "Pengguna menggunakan tablet";
} elseif ($coreMad->isComputer()) {
echo "Pengguna menggunakan komputer desktop";
}
```

### Deteksi Negara
```php
try {
// Deteksi negara berdasarkan IP
$countryInfo = $coreMad->getCountryFromIp('8.8.8.8');
if ($countryInfo) {
echo "Kode Negara: " . $countryInfo['country_code'] . "\n";
echo "Nama Negara: " . $countryInfo['country_name'] . "\n";
echo "Kode Benua: " . $countryInfo['continent_code'] . "\n";
echo "Nama Benua: " . $countryInfo['continent_name'];
} else {
echo "Informasi negara tidak ditemukan";
}
} catch (Exception $e) {
echo "Error: " . $e->getMessage();
}
```

## Penanganan Error

Package ini menggunakan exception untuk menangani error. Beberapa error yang mungkin terjadi:

1. Database GeoIP tidak ditemukan atau rusak:
```php
try {
$coreMad = new CoreMad('/path/invalid/GeoLite2-Country.mmdb');
} catch (Exception $e) {
// Handle error
echo "Error: " . $e->getMessage();
}
```
2. IP address tidak valid atau tidak ditemukan:
```php
try {
$countryInfo = $coreMad->getCountryFromIp('invalid.ip.address');
} catch (Exception $e) {
// Handle error
echo "Error: " . $e->getMessage();
}
```

## Dependencies

Package ini menggunakan beberapa library pihak ketiga:
- [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect) untuk deteksi bot
- [mobiledetect/mobiledetectlib](https://github.com/serbanghita/Mobile-Detect) untuk deteksi perangkat
- [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) untuk deteksi negara

## Kontribusi

Kontribusi selalu diterima! Silakan buat pull request di repository GitHub.

## Lisensi

Package ini dilisensikan di bawah [Lisensi MIT](LICENSE).

## Author

- **Ahmad Jamaluddin**
- Email: ahmadjn01@gmail.com

## Support

Jika Anda menemukan bug atau memiliki permintaan fitur, silakan buat issue di repository GitHub.
