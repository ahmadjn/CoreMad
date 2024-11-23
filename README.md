# CoreMad

CoreMad adalah package PHP yang menyediakan metode-metode mudah untuk:
- Deteksi Bot/Crawler
- Deteksi tipe perangkat (Mobile/Tablet/Computer)
- Deteksi negara berdasarkan IP address
- Deteksi parameter tracking (Google Ads, Facebook)

## Persyaratan

- PHP 8.0 atau lebih tinggi
- Composer untuk instalasi
- Database MaxMind GeoIP2 (untuk deteksi negara)

## Instalasi

Instalasi package menggunakan Composer:
```bash
composer require coremad/core
```

## Fitur

### 1. Deteksi Bot/Crawler
- Menggunakan library `jaybizzle/crawler-detect`
- Dapat mendeteksi berbagai jenis bot dan crawler
- Mendapatkan nama bot jika terdeteksi

### 2. Deteksi Device
- Menggunakan library `mobiledetect/mobiledetectlib`
- Deteksi smartphone, tablet, dan desktop
- Support berbagai user agent header

### 3. Deteksi Negara
- Menggunakan MaxMind GeoIP2 database
- Mendapatkan kode dan nama negara
- Mendapatkan kode dan nama benua
- Validasi format IP address (IPv4 & IPv6)

### 4. Deteksi Parameter Tracking
- Google Ads Click ID (gclid)
- Facebook Click ID (fbclid)
- Google Ads Source (gad_source)

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

### Deteksi Parameter Tracking

Package ini juga dapat mendeteksi parameter tracking dari Google Ads dan Facebook:

```php
// Cek Google Click ID (gclid)
if ($coreMad->hasGoogleClickId()) {
    echo "Google Click ID: " . $coreMad->getGoogleClickId();
}

// Cek Facebook Click ID (fbclid)
if ($coreMad->hasFacebookClickId()) {
    echo "Facebook Click ID: " . $coreMad->getFacebookClickId();
}

// Cek Google Ads Source (gad_source)
if ($coreMad->hasGoogleAdsSource()) {
    echo "Google Ads Source: " . $coreMad->getGoogleAdsSource();
}

// Dapatkan semua parameter tracking sekaligus
$trackingParams = $coreMad->getTrackingParams();
print_r($trackingParams);
/* Output:
[
    'gclid' => 'google_click_id_value',
    'fbclid' => 'facebook_click_id_value',
    'gad_source' => 'google_ads_source_value'
]
*/
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
- [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect) v1.2 - untuk deteksi bot
- [mobiledetect/mobiledetectlib](https://github.com/serbanghita/Mobile-Detect) v3.74 - untuk deteksi perangkat
- [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) v2.13 - untuk deteksi negara

## Versioning

Package ini menggunakan [Semantic Versioning](https://semver.org/):
- MAJOR version (X.0.0) - perubahan yang tidak backward compatible
- MINOR version (0.X.0) - penambahan fitur yang backward compatible
- PATCH version (0.0.X) - bug fixes yang backward compatible

## Security

Jika Anda menemukan masalah keamanan, mohon jangan buat issue publik.
Silakan kirim email ke ahmadjn01@gmail.com

## Kontribusi

Kontribusi selalu diterima! Berikut langkah-langkahnya:
1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## Lisensi

Package ini dilisensikan di bawah [Lisensi MIT](LICENSE).

## Author

- **Ahmad Jamaluddin**
- Email: ahmadjn01@gmail.com
- GitHub: [ahmadjn](https://github.com/ahmadjn)

## Support

Jika Anda menemukan bug atau memiliki permintaan fitur:
1. Buat issue di GitHub repository
2. Kirim email ke ahmadjn01@gmail.com
3. Submit pull request dengan perbaikan

## Changelog

### [1.0.0] - 2024-03-XX
- Initial release
- Fitur deteksi bot/crawler
- Fitur deteksi device
- Fitur deteksi negara
- Fitur deteksi parameter tracking
