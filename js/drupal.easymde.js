/**
 * @file
 * EasyMDE implementation of {@link Drupal.editors} API.
 */

(function (Drupal, EasyMDE) {

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
      var textarea = document.getElementById(element.id);
      if(!textarea.classList.contains('easymde-processed')) {
        textarea.classList.add('easymde-processed');
        var settings = format.editorSettings;
        settings.element = textarea;
        settings.forceSync = true;
        var editor = new EasyMDE(settings);
        return !!editor;
      }
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
      callback();
    }

  };

})(Drupal, EasyMDE);
