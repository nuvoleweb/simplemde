# SimpleMDE

[![Build Status](https://travis-ci.org/nuvoleweb/simplemde.svg?branch=8.x-1.x)](https://travis-ci.org/nuvoleweb/simplemde)

Drupal integration for <a href="https://simplemde.com/">SimpleMDE Markdown Editor</a>:

> A drop-in JavaScript textarea replacement for writing beautiful and understandable Markdown. 
The WYSIWYG-esque editor allows users who may be less experienced with Markdown to use familiar toolbar buttons and shortcuts. In addition, the syntax is rendered while editing to clearly show the expected result. Headings are larger, emphasized words are italicized, links are underlined, etc. SimpleMDE is one of the first editors to feature both built-in autosaving and spell checking.

![](https://www.drupal.org/files/simplemde.png)

## Installation

Prerequisite : you need Libraries (https://www.drupal.org/project/libraries) installed and enabled.

Download on https://simplemde.com/ the zip or the tar.gz version.

Unzip it to have (having your_drupal the directory where drupal is installed) :

```
  your_drupal/libraries/simplemde-markdown-editor/dist/simplemde.min.js
  your_drupal/libraries/simplemde-markdown-editor/dist/simplemde.min.css
```  

## Activation

Configuration > Text formats and editors

Click on + Add text format

- Name it "Markdown" (or sthg like that), 
- Roles : tick each you want to use
- Text editor : choose SimpleMDE
- Available buttons : make a selection based on your requirements. If you're a beginner with Markdown, we could suggest you to check Heading, List, Image and Toggle Preview at least
- Enabled filters : check Markdown
- Save

## If the editor does not show

Remember to reconstruct the cache eventually through /admin/config/development/performance and click Clear all caches.



