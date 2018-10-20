<?php

namespace Drupal\amp\Service;

use Drupal\amp\Service\DrupalAMP;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\amp\Routing\AmpContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Class AMPService.
 *
 * @package Drupal\amp
 */
class AMPService extends ServiceProviderBase  {

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The route amp context to determine whether a route is an amp one.
   *
   * @var \Drupal\amp\Routing\AmpContext
   */
  protected $ampContext;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Amp Config Settings.
   */
  protected $ampConfig;

  /**
   * AMP Theme Config Settings.
   */
  protected $themeConfig;

  /**
   * Constructs a CssCollectionRenderer.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Core messager service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(MessengerInterface $messenger, ConfigFactoryInterface $configFactory, AmpContext $ampContext, RendererInterface $renderer) {
    $this->messenger = $messenger;
    $this->configFactory = $configFactory;
    $this->ampContext = $ampContext;
    $this->renderer = $renderer;
    $this->ampConfig = $configFactory->get('amp.settings');
    $this->themeConfig = $configFactory->get('amp.theme');
  }

  /**
   * Map Drupal library names to the urls of the javascript they include.
   *
   * @return array
   *   An array keyed by library names of the javascript urls in each library.
   */
  protected function mapJSToNames() {
    $libraries = [];
    $definitions = \Drupal::service('library.discovery')->getLibrariesByExtension('amp');
    foreach ($definitions as $name => $definition) {
      if (!empty($definition['js'])) {
        $url = $definition['js'][0]['data'];
        $libraries[$url] = 'amp/' . $name;
      }
    }
    return $libraries;
  }

  /**
   * This is your starting point.
   * Its cheap to create AMP objects now.
   * Just create a new one every time you're asked for it.
   *
   * @return AMP
   */
  public function createAMPConverter() {
    return new DrupalAMP();
  }

  /**
   * Given an array of discovered JS requirements, add the related libraries.
   *
   * @param array $components
   *   An array of javascript urls that the AMP library discovered.
   *
   * @return array
   *   An array of the Drupal libraries that include this javascript.
   */
  public function addComponentLibraries(array $components) {
    $library_names = [];
    $map = $this->mapJSToNames();
    foreach ($components as $component_url) {
      if (isset($map[$component_url])) {
        $library_names[] = $map[$component_url];
      }
    }
    return $library_names;
  }

  /**
   * Passthrough to check route without also loading AmpContext.
   */
   public function isAmpRoute(RouteMatchInterface $routeMatch = NULL, $entity = NULL, $checkTheme = TRUE) {
     return $this->ampContext->isAmpRoute(RouteMatchInterface $routeMatch, $entity, $checkTheme);
   }

  /**
   * Helper to quickly get AMP theme config setting.
   */
  public function themeConfig($item) {
    return $this->themeConfig->get($item);
  }

  /**
   * Helper to quickly get AMP config setting.
   */
  public function ampConfig($item) {
    return $this->ampConfig->get($item);
  }

  /**
   * Display a development message.
   *
   * Determines if this is a page where a message should be displayed,
   * then renders the message.
   *
   * @param mixed $message
   *   Could be a render array, a string, or an array.
   * @param string $method
   *   The message method to use, defaults to 'addMessage'.
   *
   * @return
   *   No return, triggers a messenger message.
   */
  public function devMessage($message, $method = 'addMessage') {
    $current_page = \Drupal::request()->getRequestUri();
    if (!empty(stristr($current_page, 'development'))) {
      $rendered_message = \Drupal\Core\Render\Markup::create($message);
      $error_message = new TranslatableMarkup ('@message', array('@message' => $rendered_message));

      $this->messenger->$method($error_message);
    }
  }
}
