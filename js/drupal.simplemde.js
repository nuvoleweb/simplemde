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
        var settings = getEditorSettings(textarea, format);
        var editor = new SimpleMDE(settings);
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
    handleImage: function (editor, formatName) {
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
        url: '/editor/dialog/image/' + formatName,
        progress: { type: 'throbber' },
        submit: {
          editor_object: existingValues
        }
      });
      simplemdeAjaxDialog.execute();
      Drupal.simplemde.saveCallback = function (data) {
        var cm = editor.codemirror;
        var output = '![' + data.attributes.alt + '](' + data.attributes.src + ')';
        cm.replaceSelection(output);
      };
    },
    handleLink: function (editor, formatName) {
      const dialogSettings = {
        title: "Insert Link",
        dialogClass: "editor-link-dialog ui-dialog--narrow",
        autoResize: true,
        width: "auto"
      };
      const existingValues = { text: '', href: '' };
      var simplemdeAjaxDialog = Drupal.ajax({
        dialog: dialogSettings,
        dialogType: 'modal',
        selector: '.simplemde-dialog-loading-link',
        url: '/editor/dialog/link/' + formatName,
        progress: { type: 'throbber' },
        submit: {
          editor_object: existingValues
        }
      });
      simplemdeAjaxDialog.execute();
      Drupal.simplemde.saveCallback = function (data) {
        var cm = editor.codemirror;
        var text = cm.getSelection();
        if(text.length === 0){
          text = Drupal.t('enter link title here');
        }
        var output = '[' + text + '](' + data.attributes.href + ')';
        cm.replaceSelection(output);
      };
    }
  };

  function getEditorSettings(textarea, format) {
    const editorSettings = format.editorSettings;
    var toolbar = editorSettings.showIcons.map(function (name) {
      if (name === 'image') {
        return {
          name: "drupalimage",
          action: function (editor) { Drupal.simplemde.handleImage(editor, format.format); },
          className: "fa fa-picture-o",
          title: "Insert Image",
        };
      } else if(name === 'link'){
        return {
          name: "drupallink",
          action: function (editor) { Drupal.simplemde.handleLink(editor, format.format); },
          className: "fa fa-link",
          title: "Insert Link",
        };
      }
      return name;
    });
    var settings = { toolbar: toolbar, forceSync: true, element: textarea };
    var excludeSettings = ['showIcons'];// showIcons is converted into toolbar
    for (var config in editorSettings) {
      if (excludeSettings.indexOf(config) === -1) {
        settings[config] = editorSettings[config];
      }
    }
    return settings;
  }

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
