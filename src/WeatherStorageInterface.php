<?php

namespace Drupal\weather;
interface WeatherStorageInterface {

  public function get(\DateTime $date, $location, $days);
  public function set(\DateTime $date, $location, $days, WeatherCollection $weather);
}