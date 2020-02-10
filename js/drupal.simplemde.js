/**
 * @file
 * SimpleMDE implementation of {@link Drupal.editors} API.
 */

(function (Drupal, SimpleMDE, $) {

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
      if (!textarea.classList.contains('simplemde-processed')) {
        textarea.classList.add('simplemde-processed');
        var settings = format.editorSettings;
        settings.element = textarea;
        settings.forceSync = true;
        var toolbar = settings.showIcons.map(function (name) {
          if (name === 'image') {
            return {
              name: "drupalimage",
              action: function (editor) { Drupal.simplemde.handleImageUpload(editor, format); },
              className: "fa fa-picture-o",
              title: "Insert Image",
            };
          }
          return name;
        });
        var editor = new SimpleMDE({ toolbar: toolbar, forceSync: true, element: textarea, spellChecker: settings.spellChecker });
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

  Drupal.simplemde = {
    saveCallback: null,  
    handleImageUpload: function (editor, format) {
      const dialogSettings = {
        title: "Insert Image",
        dialogClass: "editor-image-dialog ui-dialog--narrow",
        autoResize: true,
        width: "auto"
      };
      const existingValues = { src: '', alt: '' };
      var simplemdeAjaxDialog = Drupal.ajax({
        dialog: dialogSettings,
        dialogType: 'modal',
        selector: '.simplemde-dialog-loading-link',
        url: '/editor/dialog/image/' + format.format,
        progress: { type: 'throbber' },
        submit: {
          editor_object: existingValues
        }
      });
      simplemdeAjaxDialog.execute();
      Drupal.simplemde.saveCallback = function (data) {
        var cm = editor.codemirror;
        var output = '';
        output = '![' + data.attributes.alt + '](' + data.attributes.src + ')';
        cm.replaceSelection(output);
      };
    }
  };

  $(window).on('editor:dialogsave', function (e, values) {
    if (Drupal.simplemde.saveCallback) {
      Drupal.simplemde.saveCallback(values);
    }
  });

  $(window).on('dialog:afterclose', function (e, dialog, $element) {
    if (Drupal.simplemde.saveCallback) {
      Drupal.simplemde.saveCallback = null;
    }
  });

})(Drupal, SimpleMDE, jQuery);
