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
   * Get country information from IP address
   */
  public function getCountryFromIp(string $ip): ?array
  {
    if (!$this->geoIpReader) {
      throw new Exception("GeoIP database not initialized");
    }

    try {
      $record = $this->geoIpReader->country($ip);
      return [
        'country_code' => $record->country->isoCode,
        'country_name' => $record->country->name,
        'continent_code' => $record->continent->code,
        'continent_name' => $record->continent->name
      ];
    } catch (Exception $e) {
      return null;
    }
  }
}
