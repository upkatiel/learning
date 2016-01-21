<?php

namespace Drupal\weather;

class WeatherManager implements WeatherManagerInterface {

  protected $storage;

  public function __construct(WeatherStorageInterface $storage , $date, $location, $days) {
    $this->storage = $storage;
  }
  public function get(\DateTime $date, $location, $days) {
    //Ask for the data from storage
    if (!$weather = $this->storage->get($date, $location, $days)) {

      // Request


      $this->storage->set($date, $location, $days, $weather);
    }
    return $weather;
  }
}