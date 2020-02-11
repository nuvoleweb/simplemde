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
      'image_upload' => [
        'status' => TRUE,
      ],
      'spell_checker' => FALSE,
      'prompt_urls' => FALSE,
      'show_icons' => [
        'heading',
        'heading-smaller',
        'heading-bigger',
        'heading-1',
        'heading-2',
        'heading-3',
        'code',
        'quote',
        'unordered-list',
        'ordered-list',
        'clean-block',
        'link',
        'image',
        'table',
        'horizontal-rule',
        'preview',
        'side-by-side',
        'fullscreen',
        'guide',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    $settings = $editor->getSettings();

    $form_state->loadInclude('editor', 'admin.inc');
    $form['image_upload_section'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Image Upload'),
    ];
    $form['image_upload_section']['image_upload'] = editor_image_upload_settings_form($editor);
    $form['spell_checker'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Spellchecker'),
      '#description' => $this->t('If set enables the spell checker.'),
      '#default_value' => $settings['spell_checker'],
    ];
    $form['prompt_urls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prompt URLs'),
      '#description' => $this->t('If set an alert window appears asking for the link or image URL.'),
      '#default_value' => $settings['prompt_urls'],
    ];
    $form['show_icons'] = [
      '#title' => $this->t('Available buttons'),
      '#type' => 'checkboxes',
      '#required' => TRUE,
      '#options' => $this->getAvailableIcons(),
      '#default_value' => $settings['show_icons'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFormSubmit(array $form, FormStateInterface $form_state) {
    // Clean up configuration.
    $show_icon_key = ['editor', 'settings', 'show_icons'];
    $show_icons = $form_state->getValue($show_icon_key);
    $form_state->setValue($show_icon_key, array_keys(array_filter($show_icons)));
    $settings = &$form_state->getValue([
      'editor',
      'settings',
      'image_upload_section',
      'image_upload'
    ]);
    $form_state->get('editor')->setImageUploadSettings($settings);
  }

  // @codingStandardsIgnoreStart
  /**
   * {@inheritdoc}
   */
  public function getJSSettings(Editor $editor) {
    // @codingStandardsIgnoreEnd
    $settings = $editor->getSettings();

    $hide = array_keys($this->getAvailableIcons());
    $hide = array_filter($hide, function ($icon) use ($settings) {
      return !(isset($settings['show_icons']) && is_array($settings['show_icons']) && in_array($icon, $settings['show_icons']));
    });

    $js_settings['hideIcons'] = array_values($hide);
    $js_settings['showIcons'] = (array) $settings['show_icons'];
    $js_settings['spellChecker'] = (bool) $settings['spell_checker'];
    $js_settings['promptURLs'] = (bool) $settings['prompt_urls'];
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
   *   List of icons with label.
   */
  protected function getAvailableIcons() {
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
