<?php

namespace Drupal\weather;
use Symfony\Component\Validator\Constraints\DateTime;

class WeatherStorageCache implements WeatherStorageInterface {

  public function get(\DateTime $date, $location, $days) {
    $CACHE_ID = md5($date . $location . $days);
    // Delete cache for debugging uses.
   // \Drupal::cache()->delete($CACHE_ID);
    if ($cache = \Drupal::cache()->get($CACHE_ID)) {
      $data = $cache->data;
    }
    else {
      $data = $this->set($date, $location, $days);
      $timestamp = new \DateTime();
      $timestamp->modify('+1 hour');
      \Drupal::cache()->set($CACHE_ID, $data, $timestamp->getTimestamp());
    }
    return $data;
  }

  public function set(\DateTime $date, $location, $days, WeatherCollection $weatherCollection) {
    /**
     * Weather API URL.
     */
    $WEATHER_API_URL = 'http://api.worldweatheronline.com/free/v2/weather.ashx?q='.$location.'&date='.$date.'&key=ade5f8b9d77a3eb016b7515df6464&format=json';
    // Create a HTTP client.
    $client = \Drupal::httpClient();
    // Create a request GET object.
    $response = $client->get($WEATHER_API_URL);
    #$request = $client->createRequest('GET', self::WEATHER_API_URL, []);
    if ($response->getStatusCode() == 200) {
      return json_decode($response->getBody(), TRUE);
    }
  }
}