<?php

namespace CoreMad\Core;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Detection\MobileDetect as Mobile_Detect;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Exception;

class CoreMad
{
  /**
   * @var CrawlerDetect
   */
  private $crawlerDetect;

  /**
   * @var Mobile_Detect
   */
  private $mobileDetect;

  /**
   * @var Reader|null
   */
  private $geoIpReader;

  public function __construct(?string $geoIpDbPath = null)
  {
    $this->crawlerDetect = new CrawlerDetect();
    $this->mobileDetect = new Mobile_Detect();
    $this->geoIpReader = null;

    if ($geoIpDbPath) {
      try {
        $this->geoIpReader = new Reader($geoIpDbPath);
      } catch (InvalidDatabaseException $e) {
        throw new Exception("Failed to load GeoIP database: " . $e->getMessage());
      }
    }
  }

  /**
   * Check if current user agent is a bot/crawler
   */
  public function isBot(): bool
  {
    return $this->crawlerDetect->isCrawler();
  }

  /**
   * Get bot/crawler name if detected
   */
  public function getBotName(): ?string
  {
    return $this->isBot() ? $this->crawlerDetect->getMatches() : null;
  }

  /**
   * Check if device is mobile (smartphone)
   */
  public function isMobile(): bool
  {
    return $this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet();
  }

  /**
   * Check if device is tablet
   */
  public function isTablet(): bool
  {
    return $this->mobileDetect->isTablet();
  }

  /**
   * Check if device is desktop computer
   */
  public function isComputer(): bool
  {
    return !$this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet();
  }

  /**
   * Get user agent from various possible headers
   *
   * @return string
   */
  private function getUserAgent(): string
  {
    $userAgentKeys = [
      "HTTP_USER_AGENT", // Standard
      "HTTP_X_OPERAMINI_PHONE_UA", // Opera Mini
      "HTTP_X_DEVICE_USER_AGENT", // Samsung
      "HTTP_X_ORIGINAL_USER_AGENT", // UC Browser
      "HTTP_X_SKYFIRE_PHONE", // Skyfire
      "HTTP_X_BOLT_PHONE_UA", // Bolt
      "HTTP_X_DEVICE_STOCK_UA", // Device Stock
      "HTTP_X_UCBROWSER_DEVICE_UA", // UC Browser
      "HTTP_FROM", // Email
      "HTTP_X_SCANNER", // QR Code
      "HTTP_X_REQUESTED_WITH", // AJAX requests
      "HTTP_X_CSRF_TOKEN", // CSRF token
      "HTTP_X_WAP_PROFILE", // WAP devices
      "HTTP_PROFILE", // Another WAP header
      "HTTP_X_NOKIA_IPADDRESS", // Nokia devices
      "HTTP_X_NOKIA_GATEWAY_ID", // Nokia devices
      "HTTP_X_ORANGE_ID", // Orange mobile devices
      "HTTP_X_VODAFONE_3GPDPCONTEXT", // Vodafone devices
      "HTTP_X_HUAWEI_USERID", // Huawei devices
      "HTTP_X_NETWORK_TYPE", // Network type
      "HTTP_X_MOBILE_GATEWAY", // Mobile gateway
    ];

    foreach ($userAgentKeys as $key) {
      if (!empty($_SERVER[$key])) {
        return $_SERVER[$key];
      }
    }

    return $_SERVER['HTTP_USER_AGENT'] ?? '';
  }

  /**
   * Validate IP address
   *
   * @param string $ip
   * @return bool
   */
  private function isValidIp(string $ip): bool
  {
    // Check if IP is valid IPv4
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
      return true;
    }

    // Check if IP is valid IPv6
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
      return true;
    }

    return false;
  }

  /**
   * Get country information from IP address with validation
   *
   * @param string $ip
   * @return array|null
   * @throws Exception
   */
  public function getCountryFromIp(string $ip): ?array
  {
    if (!$this->geoIpReader) {
      throw new Exception("GeoIP database not initialized");
    }

    // Validate IP address first
    if (!$this->isValidIp($ip)) {
      throw new Exception("Invalid IP address format");
    }

    try {
      $record = $this->geoIpReader->country($ip);
      return [
        'country_code' => $record->country->isoCode,
        'country_name' => $record->country->name,
        'continent_code' => $record->continent->code,
        'continent_name' => $record->continent->name
      ];
    } catch (AddressNotFoundException $e) {
      return null;
    } catch (Exception $e) {
      throw new Exception("Error getting country info: " . $e->getMessage());
    }
  }

  /**
   * Check if URL has Google Ads Click ID (gclid)
   *
   * @return bool
   */
  public function hasGoogleClickId(): bool
  {
    return isset($_GET['gclid']) && !empty($_GET['gclid']);
  }

  /**
   * Get Google Ads Click ID (gclid)
   *
   * @return string|null
   */
  public function getGoogleClickId(): ?string
  {
    return $_GET['gclid'] ?? null;
  }

  /**
   * Check if URL has Facebook Click ID (fbclid)
   *
   * @return bool
   */
  public function hasFacebookClickId(): bool
  {
    return isset($_GET['fbclid']) && !empty($_GET['fbclid']);
  }

  /**
   * Get Facebook Click ID (fbclid)
   *
   * @return string|null
   */
  public function getFacebookClickId(): ?string
  {
    return $_GET['fbclid'] ?? null;
  }

  /**
   * Check if URL has Google Ads Source parameter (gad_source)
   *
   * @return bool
   */
  public function hasGoogleAdsSource(): bool
  {
    return isset($_GET['gad_source']) && !empty($_GET['gad_source']);
  }

  /**
   * Get Google Ads Source parameter (gad_source)
   *
   * @return string|null
   */
  public function getGoogleAdsSource(): ?string
  {
    return $_GET['gad_source'] ?? null;
  }

  /**
   * Get all tracking parameters if exist
   *
   * @return array
   */
  public function getTrackingParams(): array
  {
    return [
      'gclid' => $this->getGoogleClickId(),
      'fbclid' => $this->getFacebookClickId(),
      'gad_source' => $this->getGoogleAdsSource()
    ];
  }
}
