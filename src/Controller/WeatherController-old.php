<?php

namespace Drupal\weather\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use Drupal\weather\WeatherManagerInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\image\Entity;

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

  public function cache($date , $days_to_fore, $hourly_interval, $location){
    $data = [];
    for ($x = 0; $x <= $days_to_fore; $x++) {
      $newdate = strtotime ( '+'.$x.' day' , strtotime ( $date ) ) ;
      $newdate = date ( 'Y-m-j' , $newdate );
      $CACHE_ID = md5($newdate . $location . $hourly_interval);



      if ($cache = \Drupal::cache()->get($CACHE_ID)) {
        $data[] = $cache->data;
      }
      else {
        $data[] = $this->response($newdate, $location, $hourly_interval);
        \Drupal::cache()->set($CACHE_ID, $data);
      }
    }
    return array($data);
  }

  /**
   * Generates an example page.
   */
  public function response($date, $location, $hourly_interval) {
    /**
     * Weather API URL.
     */
    $WEATHER_API_URL = 'http://api.worldweatheronline.com/free/v2/weather.ashx?q='.$location.'&date='.$date.'&tp='.$hourly_interval.'&key=ade5f8b9d77a3eb016b7515df6464&format=json';
    // Create a HTTP client.
    $client = \Drupal::httpClient();

    // Create a request GET object.
    $response = $client->get($WEATHER_API_URL);
    #$request = $client->createRequest('GET', self::WEATHER_API_URL, []);
    if ($response->getStatusCode() == 200) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function content($date, $days_to_fore, $hourly_interval, $current_conditions, $location) {
    $json_decoded = $this->cache($date , $days_to_fore, $hourly_interval, $location);
    if ($json_decoded) {
      $compt = 0;
      $weather_array = [];
      foreach ($json_decoded as $key => $data) {
        $rows[$compt]['weatherDesc'] = $data[0]['current_condition'][0]['weatherDesc'][0]['value'];
        $rows[$compt]['temp'] = $data[0]['current_condition'][0]['temp_C'];
        $rows[$compt]['observation_time'] = $data[0]['current_condition'][0]['observation_time'];
        $compt++;

        foreach ($data as $data_key => $weather) {
          $weather_array[] = $weather;
        }
      }
    }
//
//
//
//        $rows[$compt]['weatherDesc'] = $weather['current_condition'][0]['weatherDesc'][0]['value'];
//        $rows[$compt]['temp'] = $weather['current_condition'][0]['temp_C'];
//        $rows[$compt]['observation_time'] = $weather['current_condition'][0]['observation_time'];
//        $compt++;
//      }
//      $compt = 0;
//    }
    $table[] = array(
      '#type' => 'markup',
      '#markup' => '<h2>Current Conditions</h2>',
    );
    $table[] = array(
      '#type' => 'table',
      '#header' => array('Description', 'Temperature (C)' , 'Observation Time' ),
      '#rows' => $rows,
      '#attributes' => array('id' => 'current-conditions'),
      '#empty' => t('No item available.'),
    );
    return $table;
  }

  public function __construct(WeatherManagerInterface $manager) {
    $this->manager = $manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('weather.manager')
    );
  }
}