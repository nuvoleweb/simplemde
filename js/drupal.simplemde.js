/**
 * @file
 * SimpleMDE implementation of {@link Drupal.editors} API.
 */

(function (Drupal, SimpleMDE) {

  'use strict';

  /**
   * @namespace
   */
  Drupal.editors.simplemde = {

    /**
     * Editor attach callback.
     *
     * @param {HTMLElement} element
     *   The element to attach the editor to.
     * @param {string} format
     *   The text format for the editor.
     *
     * @return {bool}
     *   Whether the editor was successfully attached.
     */
    attach: function (element, format) {
      var settings = format.editorSettings;
      settings.element = document.getElementById(element.id);
      var editor = new SimpleMDE(settings);
      return !!editor;
    },

    /**
     * Editor detach callback.
     *
     * @param {HTMLElement} element
     *   The element to detach the editor from.
     * @param {string} format
     *   The text format used for the editor.
     * @param {string} trigger
     *   The event trigger for the detach.
     *
     * @return {bool}
     *   Whether the editor was successfully detached.
     */
    detach: function (element, format, trigger) {

    },

    /**
     * Reacts on a change in the editor element.
     *
     * @param {HTMLElement} element
     *   The element where the change occurred.
     * @param {function} callback
     *   Callback called with the value of the editor.
     */
    onChange: function (element, callback) {

    }

  };

})(Drupal, SimpleMDE);
