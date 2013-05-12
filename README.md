## KCFinder
#### A PHP-based AJAX file and media manager

#### Credits

Original project by Pavel Tzonkov http://kcfinder.sunhater.com

#### Description

KCFinder is a web-based file manager, built on a PHP backend and AJAX-powered
UI. It has file upload features, path locking for different file types (when
integrated within various WYSIWYG editors).

#### Requirements

* (server) PHP 5.3+
* (server) (optional) PHP-GD
* (client) any modern web browser
* (client) JavaScript turned on

#### Features

* File system manipulation (move||copy||delete files or directories)
* Thumbnail generation for images (.thumbs directory in the upload path)
* Path locking for file types - specify a path for every filetype you need to
  upload in a separate location (i.e. *PNG*,*JPG*,*GIF* stay in **images** directory,
  *WAV*,*OGG*,*MP3* files in **audio** directory and so on, on a config-based
  behaviour).
* Easy (and documented) integration with a number of WYSIWYG editors (i.e. CKEditor, FCKeditor,
  TinyMCE etc.)
* Ajax engine with JSON responses
* Multiple files upload (does not work in IE!)
* Upload files using HTML5 drag and drop (for Firefox and Chrome only!)
* Download multiple files or a folder as single ZIP file
* Select multiple files with the Ctrl/Command key
* Clipboard for copying, moving and downloading multiple files
* Easy to integrate and configure in web applications
* Option to select and return several files. For custom applications only
* Resize uploaded images. Configurable maximum image resolution
* Configurable thumbnail resolution
* Visual CSS themes
* Multilanguage system
* Slideshow using arrow keys

#### Documentation

For now, the only documentation is available at the official web page,
http://kcfinder.sunhater.com - will be expanded as this fork goes on, and maybe
integrated in the local wiki system.

#### Further development

Right now, the GitHub repository serves as a branch of the original project,
aiming to send patches and features upstream. If, for any reason, they will not
be integrated or ignored, there is a possibility of complete forking and
renaming, as it will become a full-featured fork.

#### Roadmap

The features I aim for right now are (not necessarily in this order):

* Image editing (crop, resize, rotate) from within the KCFinder window
* User authentication option (avoiding guest sessions as much as possible)
* Support for mounting (remote) FTP locations
* Code editing plugin with syntax highlight (enabling it to be used as a small
  multipurpose tool)

#### Repository and workflow

This repository follows the git flow model (https://github.com/nvie/gitflow) and
uses Vincent Driessen's branching recommendations. Thus, the main stable branch
is named **master**, release is prepared on **release** branches, active
development is taking place on **develop** branch and feature branches are
prefixed with **feature/<name>**.  More details in Vincent's article:
http://nvie.com/git-model

