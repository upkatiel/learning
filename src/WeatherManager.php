<?php

namespace Drupal\weather;

use Symfony\Component\Config\Definition\Exception\Exception;

class WeatherManager implements WeatherManagerInterface {

  protected $storage;

  public $collections;

  public function __construct(WeatherStorageInterface $storage ) {
    $this->storage = $storage;
  }

  public function get(\DateTime $date, $location) {
    $existing_weather = NULL;
      if (!($weather = $this->storage->get($date, $location))) {
        if ($weather = $this->getCollection($date, $location)) {
          $this->storage->set($date, $location, $weather);
        }
      }
      return $weather;
  }
  public function getCollection(\DateTime $date, $location) {
    $weather_collection = NULL;
    $WEATHER_API_URL = 'http://api.worldweatheronline.com/free/v2/weather.ashx?q='.$location.'&date='.$date->format('Y-m-d').'&key=ade5f8b9d77a3eb016b7515df6464&format=json';
    // Create a HTTP client.
    $client = \Drupal::httpClient();
    // Create a request GET object.
    $response = $client->get($WEATHER_API_URL);
    if ($response->getStatusCode() == 200) {
      $raw = json_decode($response->getBody(), TRUE);
      foreach ($raw as $data => $interval) {
        //$date = $interval['weather'][0]['date'];
        $day_date = new \DateTime();
        if ($interval['weather']) {
          $collections = $this->setIntervals($date, $interval);
          $this->collections = $collections;
          return $collections;
        }
        else {
          throw new Exception("Sorry we don't have hourly data for this time right now.");
        }
      }
    }
  }

  protected function getIntervals(\DateTime $date, $location) {
    $collection = $this->getCollection($date, $location);
  }

  public function setCurrentCondition(\DateTime $date, $interval){
    $currentCondition = new WeatherInterval($date, [
      'temp' => $interval['current_condition'][0]['temp_C'],
      'visibility' => $interval['current_condition'][0]['visibility'],
      'weatherIconUrl' => $interval['current_condition'][0]['weatherIconUrl'][0]['value'],
      'dateTime' => $date->format('Y-m-d H:i'),
    ]);
    return $currentCondition;
  }

  protected function setIntervals(\DateTime $date, $interval) {
    $weather_collection = new WeatherCollection();
    foreach ($interval['weather'][0]['hourly'] as $hourly => $hourly_data) {
      $time = $hourly_data['time'];
      $time = strlen($time) >= 3 ? substr($time, 0, -2) : $time;
      $interval_date = clone $date;
      $interval_date->setTime($time, 0);
      $new_interval = new WeatherInterval($interval_date, [
        'temp' => $hourly_data['tempC'],
        'visibility' => $hourly_data['visibility'],
        'weatherIconUrl' => $hourly_data['weatherIconUrl'][0]['value'],
        'dateTime' => $interval_date->format('Y-m-d H:i'),
      ]);
      $weather_collection->addInterval($new_interval);
    }
    $interval_date = new \DateTime('now');

    $currentCondition = $this->setCurrentCondition($interval_date,$interval);
    $weather_collection->currentConditions($currentCondition);
    return $weather_collection;
  }
}