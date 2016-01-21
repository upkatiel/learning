<?php

namespace Drupal\weather;

interface WeatherManagerInterface {
  public function get(\DateTime $date, $location, $days);
}