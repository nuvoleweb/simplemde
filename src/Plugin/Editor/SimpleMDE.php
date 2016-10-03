<?php

namespace Drupal\simplemde\Plugin\Editor;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\editor\Plugin\EditorBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\Entity\Editor;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a SimpleMDE-based text editor for Drupal.
 *
 * @Editor(
 *   id = "simplemde",
 *   label = @Translation("SimpleMDE"),
 *   supports_content_filtering = TRUE,
 *   supports_inline_editing = TRUE,
 *   is_xss_safe = FALSE,
 *   supported_element_types = {
 *     "textarea"
 *   }
 * )
 */
class SimpleMDE extends EditorBase implements ContainerFactoryPluginInterface {

  /**
   * The module handler to invoke hooks on.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke hooks on.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, LanguageManagerInterface $language_manager, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->moduleHandler = $module_handler;
    $this->languageManager = $language_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultSettings() {
    return [
      'spellChecker' => FALSE,
      'promptURLs' => FALSE,
      'showIcons' => [
        'heading' => TRUE,
        'heading-smaller' => TRUE,
        'heading-bigger' => TRUE,
        'heading-1' => TRUE,
        'heading-2' => TRUE,
        'heading-3' => TRUE,
        'code' => TRUE,
        'quote' => TRUE,
        'unordered-list' => TRUE,
        'ordered-list' => TRUE,
        'clean-block' => TRUE,
        'link' => TRUE,
        'image' => TRUE,
        'table' => TRUE,
        'horizontal-rule' => TRUE,
        'preview' => TRUE,
        'side-by-side' => TRUE,
        'fullscreen' => TRUE,
        'guide' => TRUE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    $form['spellChecker'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Spellchecker'),
      '#description' => $this->t('If set enables the spell checker.'),
      '#default_value' => $settings['spellChecker'],
    ];
    $form['promptURLs'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prompt URLs'),
      '#description' => $this->t('If set an alert window appears asking for the link or image URL.'),
      '#default_value' => $settings['promptURLs'],
    ];
    $form['showIcons'] = [
      '#title' => $this->t('Available buttons'),
      '#type' => 'checkboxes',
      '#required' => TRUE,
      '#options' => $this->getButtons(),
      '#default_value' => $settings['showIcons'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getJSSettings(Editor $editor) {
    $settings = $editor->getSettings();
    $js_settings = $settings;
    $js_settings['showIcons'] = array_keys(array_filter($settings['showIcons']));
    $js_settings['spellChecker'] = (bool) $js_settings['spellChecker'];
    $js_settings['promptURLs'] = (bool) $js_settings['promptURLs'];
    return $js_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    $libraries = [
      'simplemde/simplemde',
      'simplemde/drupal.simplemde',
    ];
    return $libraries;
  }

  /**
   * Return set of available icons.
   *
   * @return array
   *    List of icons with value.
   */
  protected function getButtons() {
    return [
      'heading' => $this->t('Heading'),
      'heading-smaller' => $this->t('Smaller Heading'),
      'heading-bigger' => $this->t('Bigger Heading'),
      'heading-1' => $this->t('Big Heading'),
      'heading-2' => $this->t('Medium Heading'),
      'heading-3' => $this->t('Small Heading'),
      'code' => $this->t('Code'),
      'quote' => $this->t('Quote'),
      'unordered-list' => $this->t('Generic List'),
      'ordered-list' => $this->t('Numbered List'),
      'clean-block' => $this->t('Clean block'),
      'link' => $this->t('Create Link'),
      'image' => $this->t('Insert Image'),
      'table' => $this->t('Insert Table'),
      'horizontal-rule' => $this->t('Insert Horizontal Line'),
      'preview' => $this->t('Toggle Preview'),
      'side-by-side' => $this->t('Toggle Side by Side'),
      'fullscreen' => $this->t('Toggle Fullscreen'),
      'guide' => $this->t('Markdown Guide'),
    ];
  }
}
