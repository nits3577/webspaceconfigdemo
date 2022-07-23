<?php

/**
 * editor class	for visforms
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class VisformsEditorHelper
{
	public static function initEditor() {
    	$basePath = 'media/vendor/tinymce';
		$app = Factory::getApplication();
		$language = $app->getLanguage();
		$text_direction = 'ltr';
		if ($language->isRTL()) {
			$text_direction = 'rtl';
		}
		$entity_encoding = 'raw';

		$langMode = 1;
		$langPrefix = 'en';

		if ($langMode) {
			$langPrefix = substr($language->getTag(), 0, strpos($language->getTag(), '-'));
		}
		$newlines = 1;

		if ($newlines) {
			// br
			$forcenewline = "force_br_newlines : true, force_p_newlines : false, forced_root_block : '',";
		} else {
			// p
			$forcenewline = "force_br_newlines : false, force_p_newlines : true, forced_root_block : 'p',";
		}

		$invalid_elements = 'script,applet,iframe';
		$relative_urls = "false";
		$load = "\t<script type=\"text/javascript\" src=\"" .
			Uri::root() . $basePath .
			"/tinymce.min.js\"></script>\n";

		$return = $load .
			"\t<script type=\"text/javascript\">
            tinyMCE.init({
                    // General
                    branding: false,
                    directionality: \"$text_direction\",
                    plugins : \"autosave charmap\",
                    language : \"" . $langPrefix . "\",
                    // use custom selector, added to textarea element in components/com_visforms/lib/html/field/textarea.php
                    selector: \".visforms-editor-tinymce\",
                    mode : \"specific_textareas\",
                    deselector: \"visforms-editor-tinymce\",
                    schema: \"html5\",
                    // Cleanup/Output
                    inline_styles : true,
                    gecko_spellcheck : true,
                    entity_encoding : \"$entity_encoding\",
                    $forcenewline
                    // URL
                    relative_urls : $relative_urls,
                    remove_script_host : false,
                    // Layout                  
                    document_base_url : \"" . Uri::root() . "\",
                    menubar : \"insert\",
                    setup: function (ed) {ed.on('change', function (ed) {updateText(ed); });
                    //add function that will update content of tinyMCE on submit
                    ed.on('submit', function (ed) { return updateText(ed); });}
            });
            function updateText(ed) {
                    //get id of textarea which belongs to the editor
                    var inputId = ed.target.id;
                    //copy editor content into textarea
                    tinyMCE.triggerSave();
                    //validate content of textarea
                    return jQuery('#' + inputId).valid();
                };
            </script>";
		$doc = Factory::getApplication()->getDocument();
		$doc->addCustomTag($return);
		return true;
	}
}
