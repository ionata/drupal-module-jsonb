<?php

/**
 * @file
 * Contains \Drupal\jsonb\Plugin\Field\FieldWidget\JsonbWidget.
 */

namespace Drupal\jsonb\Plugin\Field\FieldWidget;

use Drupal\Component\Serialization\Json as Json;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'jsonb_textarea' widget.
 *
 * @FieldWidget(
 *   id = "jsonb_textarea",
 *   label = @Translation("JSONB Object"),
 *   field_types = {
 *     "jsonb",
 *     "json",
 *   }
 * )
 */
class JsonbWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['#element_validate'][] = array(get_called_class(), 'validateJsonStructure');
    return $widget;
  }

  /**
   * Validates the input to see if it is a properly formatted JSON object. If not, PgSQL will throw fatal errors upon insert.
   *
   * @param $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param $form
   */
  public static function validateJsonStructure(&$element, FormStateInterface $form_state, $form) {
    if (mb_strlen($element['value']['#value'])) {
      $value = Json::decode($element['value']['#value']);

      if (json_last_error() !== JSON_ERROR_NONE) {
        $form_state->setError($element['value'], t('@name must contain a valid JSON object.', ['@name' => $element['#title']]));
      }
    }
  }
}
