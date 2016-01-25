<?php
/**
 * Created by PhpStorm.
 * User: Katie Lacy
 * Date: 19/01/2016
 * Time: 14:55
 */
/**
 * @file
 * Contains \Drupal\weather\Form\WeatherForm.
 */

namespace Drupal\weather\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\weather\Http\weatherHttpRequest;
use Drupal\weather\Controller;

/**
 * Implements an example form.
 */
class WeatherForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $date = [
      0 => t('Today'),
      1 => t('Tomorrow'),
      2 => t('Enter Date'),
    ];
    $days_to_show = [];
    for ($day = 0; $day < 11; $day++) {
      $days_to_show[] = $day;
    }
    $form['weather']['address'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Town / City'),
      '#attributes' => [
        'maxlength' => 50,
        'placeholder' => 'Enter a Town / City'
      ],
    ];

    $form['weather']['date'] = [
      '#type' => 'radios',
      '#title' => t('Date *'),
      '#options' => $date,
      '#required' => TRUE,
      '#default_value' => 0,
      '#description' => t('When a poll is closed, visitors can no longer vote for it.')
    ];
    $form['weather']['date_picker'] = [
      '#type' => 'date',
      '#default_value' => date('Y-m-d'),
    ];
    $form['weather']['days_to_fore'] = [
      '#title' => t('Days To Forecast'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => $days_to_show,
    ];
    $form['weather']['current_conditions'] = [
      '#type' => 'checkbox',
      '#title' => t('Show current conditions'),
    ];
    $form['weather']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => array('btn-block'),
      ]
    ];
    $form['#attributes']['class'][] = 'col-md-12';
    $form['#attached']['library'][] = 'weather/weather-form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if (strlen($form_state->getValue('phone_number')) < 3) {
//      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $location = $values['address'];
    $date = $values['date'];
    $date_picker = $values['date_picker'];
    $days_to_fore = $values['days_to_fore'];
    $current_conditions = $values['current_conditions'];
    switch ($date) {
      case 1:
        $datetime = new \DateTime('tomorrow');
        $date = $datetime->format('Y-m-d');
        break;
      case 2:
        $date = $date_picker;
        break;
      default:
        $datetime = new \DateTime('now');
        $date = $datetime->format('Y-m-d');
    }

    $form_state->setRedirect('weather.cache' ,
      array(
        'date' => $date,
        'location' => $location,
        'days' => $days_to_fore,
        'current' => $current_conditions,
      )
    );
  }
}
