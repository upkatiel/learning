<?php
/**
 * Created by PhpStorm.
 * User: Katie Lacy
 * Date: 19/01/2016
 * Time: 14:55
 */
/**
 * @file
 * Contains \Drupal\weather\Form\WeatherTable.
 */

namespace Drupal\weather\Form;

use Drupal\weather\Controller\WeatherController;
use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;

/**
 * Implements an example form.
 */
class WeatherTable extends WeatherController {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $json_decoded = new WeatherController();
    $json_decoded->content();
    if ($json_decoded) {

      print_r($json_decoded);
      exit;
      $compt = 0;
      foreach ($json_decoded as $key => $weather) {
        $rows[$compt]['URL'] = $weather['current_condition'][0]['FeelsLikeC'];
        $rows[$compt]['language'] = $weather['current_condition'][0]['FeelsLikeC'];
        $rows[$compt]['subcategoryfr'] = $weather['current_condition'][0]['FeelsLikeC'];
        $rows[$compt]['friendlysizekey'] = $weather['current_condition'][0]['FeelsLikeC'];
        $rows[$compt]['productdomaincodedescriptionfr'] = $weather['current_condition'][0]['FeelsLikeC'];
        $rows[$compt++]['companyname'] = $weather['current_condition'][0]['FeelsLikeC'];
      }
    }


    $header = array(
      'URL',
      'Language',
      'Category Description',
      'FriendlySizeKey',
      'Product',
      'Company'
    );

    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => array('id' => 'book-outline'),
      '#empty' => t('No item available.'),
    );
    drupal_render($table);
    return 'weather_form';
  }
}
