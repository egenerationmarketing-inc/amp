<?php

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'amp_text' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_text",
 *   label = @Translation("AMP Text"),
 *   description = @Translation("Display AMP text."),
 *   field_types = {
 *     "string",
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *   },
 * )
 */
class AmpTextFormatter extends TextDefaultFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    /** @var AMPService $amp_service */
    $amp_service = \Drupal::service('amp.utilities');
    /** @var AMP $amp */
    $amp = $amp_service->createAMPConverter();

    foreach ($elements as $delta => $item) {
      $amp->loadHtml($item['#text']);
      $elements[$delta]['#text'] = $amp->convertToAmpHtml();
      if (!empty($amp->getComponentJs())) {
        $elements[$delta]['#attached']['library'] = $amp_service->addComponentLibraries($amp->getComponentJs());
      }
    }
    return $elements;

  }
}


