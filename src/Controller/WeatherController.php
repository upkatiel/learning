<?php

namespace Drupal\weather\Controller;

use Drupal\Core\Controller\ControllerBase;
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
  public function getWeather(\DateTime $date, $days_to_fore, $hourly_interval, $current_conditions, $location) {
    $collection = $this->manager->get($date, $location, $days_to_fore);
    $current = $this->collection->currentConditionTable($collection, $location);
    $table[] = array(
      '#type' => 'table',
      '#header' => $current['current_condition']['header'],
      '#rows' => $current['current_condition']['rows'],
      '#attributes' => array('id' => 'current-conditions'),
      '#empty' => t('No item available.'),
    );
    $days = $this->collection->weatherTable($collection, $days_to_fore);

    $day_table[] = array(
      '#type' => 'table',
      '#header' => $days['header'],
      '#rows' => $days['rows'],
      '#attributes' => array('id' => 'current-conditions'),
      '#empty' => t('No item available.'),
    );
    return ['#theme' => 'weather_conditions',
      '#location' => $location,
      '#current_conditions' => $table,
      '#current_conditions_title' => 'Current Conditions',
      '#day_condition' => $day_table,
      '#day_condition_title' => $date,
    ];
  }



}