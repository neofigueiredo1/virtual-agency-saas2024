/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
//	config.filebrowserBrowseUrl      = '/admin/library/editor/ckeditor/filemanager/index.php',
//	config.filebrowserImageBrowseUrl = '/admin/library/editor/ckeditor/filemanager/index.php',
//	config.filebrowserFlashBrowseUrl = '/admin/library/editor/ckeditor/filemanager/index.php',
//	config.filebrowserUploadUrl      = '/admin/library/editor/ckeditor/filemanager/index.php',
//	config.filebrowserImageUploadUrl = '/admin/library/editor/ckeditor/filemanager/index.php',
//	config.filebrowserFlashUploadUrl = '/admin/library/editor/ckeditor/filemanager/index.php',

	config.removePlugins             = 'pastefromword';
	config.forcePasteAsPlainText     = true;
	config.autoParagraph             = false;
	config.enterMode                 = CKEDITOR.ENTER_BR;
	config.shiftEnterMode            = CKEDITOR.ENTER_BR;
	config.filebrowserBrowseUrl      = '/admin/library/editor/kcfinder-2.51/browse.php?type=files',
	config.filebrowserImageBrowseUrl = '/admin/library/editor/kcfinder-2.51/browse.php?type=images',
	config.filebrowserFlashBrowseUrl = '/admin/library/editor/kcfinder-2.51/browse.php?type=flash',
	config.filebrowserUploadUrl      = '/admin/library/editor/kcfinder-2.51/upload.php?type=files',
	config.filebrowserImageUploadUrl = '/admin/library/editor/kcfinder-2.51/upload.php?type=images',
	config.filebrowserFlashUploadUrl = '/admin/library/editor/kcfinder-2.51/upload.php?type=flash',

	// config.contentsCss            = '/cliente/cliente.css',
	config.height                    = 300;

	config.toolbar = 'sisadmin';

	config.toolbar_sisadmin =
	[
	    { name: 'document',    items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
	    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	    { name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	    '/',
	    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	    { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
	    { name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
	    '/',
	    { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	    { name: 'colors',      items : [ 'TextColor','BGColor' ] },
	    { name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];

};
