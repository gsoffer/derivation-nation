/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	// The toolbar groups arrangement, optimized for two toolbar rows.

	config.toolbar = [
		[ 'Maximize', 'Preview' ] , [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ], 
		[ 'SimpleLink', 'Table', 'Image', 'fastimage' ], [ 'CodeSnippet', 'Mathjax', 'Symbol' ], 
		'/', 
		[ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
		[ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ], 
		[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent' ], [ 'Blockquote', 'Footnotes' ]
	];

	// Remove some buttons provided by the plugins
	config.removeButtons = 'Strike';

	// Simplify the dialog windows
	config.removeDialogTabs = 'image:advanced;image:Link';

	// Configure fast image for image uploading:
	config.extraPlugins = 'fastimage';

	// Restricts Code Snippet languages
	config.codeSnippet_languages = {
		mathematica: 'Mathematica',
		matlab: 'Matlab',
		haskell: 'Haskell',
		tex: 'Tex',
		scilab: 'Scilab',
		stata: 'Stata',
		r: 'R',
		python: 'Python',
		perl: 'Perl',
		ruby: 'Ruby',
		java: 'Java',
		scala: 'Scala',
		php: 'PHP',
		javascript: 'JavaScript',
		coffeescript: 'CoffeeScript',
		cplusplus: 'C++', 
		csharp: 'C#',
		objectivec: 'Objective C',
		swift: 'Swift',
		sql: 'SQL',
		vbscript: 'VBScript'
	};

	// Change the color of the text editor box:
	config.uiColor = '#F8F8F8';

};

