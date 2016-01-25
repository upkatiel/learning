<?php

namespace Drupal\weather\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\weather\WeatherCollection;
use Drupal\weather\WeatherManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Get a response code from any URL using Guzzle in Drupal 8!
 *
 * Usage:
 * In the head of your document:
 *
 * use Drupal\weather\Http\CustomGuzzleHttp;
 *
 * In the area you want to return the result, using any URL for $url:
 *
 * $check = new CustomGuzzleHttp();
 * $response = $check->performRequest($url);
 *
 **/

class WeatherController extends ControllerBase {
  /**
   * The catalog personal key.
   */
  protected $key;

  /**
   * @var WeatherManagerInterface $manager
   */
  protected $manager;

  /**
   * @var WeatherCollection $collection
   */
  protected $collection;

  public function __construct(WeatherManagerInterface $manager , WeatherCollection $collection) {
    $this->manager = $manager;
    $this->collection = $collection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('weather.manager'),
      $container->get('weather.collection')
    );
  }

  public function getWeather($date, $location) {
    $days = $_GET['days'];
    if (!isset($days)) {
      $days = 1;
    }
    $current_conditions = $_GET['current'];

    $date = new \DateTime($date);
    // Make sure days is an integer.
    $days = (int) $days;
    $iteration_date = clone $date;
    if ($current_conditions) {
      $collection = $this->manager->get($iteration_date, $location);
      $theme[] = [
        '#theme' => 'weather_current_conditions',
        '#collection' => $collection->current,
        '#location' => $location
      ];
    }
    for ($day = 0; $day < $days; $day++) {
      $collection = $this->manager->get($iteration_date, $location);
      $theme[] = [
        '#theme' => 'weather_date',
        '#collection' => $collection->getCollection(),
        '#date' => $iteration_date->format('D, dS F'),
      ];
      $iteration_date->modify('+1 days');
    }
    return $theme;
  }
}