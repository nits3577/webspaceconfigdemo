<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;

class VisformsHelper
{
	public static $extension = 'com_visforms';

	// ToDo could be renamed, because it has no longer to do with the old Joomla! addSubmenu function
    // Does not need parameter, refactor
	public static function addSubmenu($vName, $fid = 0, $saveResult = false) {
        PluginHelper::importPlugin('visforms');
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param int        The category ID.
	 * @param int        The article ID.
	 *
	 * @return    JObject
	 * @since    1.6
	 */
	public static function getActions($formId = 0, $fieldId = 0) {
		$user = Factory::getApplication()->getIdentity();
		$result = new JObject;
		if (empty($formId) && empty($fieldId)) {
			$assetName = 'com_visforms';
		}
		else if (empty($fieldId)) {
			$assetName = 'com_visforms.visform.' . (int) $formId;
		}
		else {
			$assetName = 'com_visforms.visform.' . (int) $formId . '.visfield.' . (int) $fieldId;
		}
		$actions = JAccess::getActionsFromFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_visforms/access.xml'), "/access/section[@name='component']/");
		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}
		return $result;
	}

	/**
	 * Checks if the file can be uploaded
	 *
	 * @param array  $file File information
	 * @param string $err  An error message to be returned
	 *
	 * @return  boolean
	 *
	 * @since   3.2
	 */
	public static function canUpload($file, $allowedExtensions = "") {
		if (empty($file['name'])) {
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_UPLOAD_INPUT'), 'error');
			return false;
		}
		// Media file names should never have executable extensions buried in them.
		$executable = array(
			'exe', 'phtml', 'java', 'perl', 'py', 'asp', 'dll', 'go', 'jar',
			'ade', 'adp', 'bat', 'chm', 'cmd', 'com', 'cpl', 'hta', 'ins', 'isp',
			'jse', 'lib', 'mde', 'msc', 'msp', 'mst', 'pif', 'scr', 'sct', 'shb',
			'sys', 'vb', 'vbe', 'vbs', 'vxd', 'wsc', 'wsf', 'wsh'
		);
		$explodedFileName = explode('.', $file['name']);
		if (count($explodedFileName) > 2) {
			foreach ($executable as $extensionName) {
				if (in_array($extensionName, $explodedFileName)) {
					$app = Factory::getApplication();
					$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_EXECUTABLE'), 'error');
					return false;
				}
			}
		}
		if ($file['name'] !== File::makeSafe($file['name']) || preg_match('/\s/', File::makeSafe($file['name']))) {
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_WARNFILENAME'), 'error');
			return false;
		}
		$format = strtolower(File::getExt($file['name']));
		$allowable = array($allowedExtensions);
		if ($format == '' || $format == false || (!in_array($format, $allowable))) {
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_WARNFILETYPE'), 'error');
			return false;
		}
		// Max upload size set to 2 MB for Template Manager
		$maxSize = (int) (2 * 1024 * 1024);
		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_WARNFILETOOLARGE'), 'error');
			return false;
		}
		$xss_check = file_get_contents($file['tmp_name'], false, null, -1, 256);
		$html_tags = array(
			'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink', 'blockquote',
			'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del', 'dfn', 'dir', 'div',
			'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html',
			'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext', 'link', 'listing',
			'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object', 'ol', 'optgroup', 'option',
			'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar', 'small', 'spacer', 'span', 'strike',
			'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var', 'wbr', 'xml',
			'xmp', '!DOCTYPE', '!--'
		);
		foreach ($html_tags as $tag) {
			// a tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if (stristr($xss_check, '<' . $tag . ' ') || stristr($xss_check, '<' . $tag . '>')) {
				$app = Factory::getApplication();
				$app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_WARNIEXSS'), 'error');
				return false;
			}
		}
		return true;
	}

	public static function addCommonViewStyleCss() {
		$doc = Factory::getApplication()->getDocument();
		$css =
			' .icon-visform {background:url(../administrator/components/com_visforms/images/visforms_logo_32.png) no-repeat;}' .
			' [class^="icon-visform"] {height: 32px !important; line-height: 32px !important; width: 32px !important; vertical-align: middle;}';
		$doc->addStyleDeclaration($css);
		$doc->addStyleSheet(JURI::root(true) . "/administrator/components/com_visforms/css/visforms.css");
	}

	// registry helper functions
	public static function registryArrayFromString($settings = '') {
		if (empty($settings)) {
			return array();
		}
		try {
			$registry = new Registry;
			$registry->loadString($settings);
			return $registry->toArray();
		}
		catch (RuntimeException $e) {
			return array();
		}
	}

	public static function registryStringFromArray($values = array()) {
		if (is_string($values)) {
			return $values;
		}
		try {
			$registry = new Registry;
			$registry->loadArray($values);
			return (string) $registry;
		}
		catch (RuntimeException $e) {
			return '';
		}
	}

	public static function registryObjectFromString($settings = '') {
		if (empty($settings)) {
			return new \stdClass;
		}
		try {
			$registry = new Registry;
			$registry->loadString($settings);
			return $registry->toObject();
		}
		catch (RuntimeException $e) {
			return new \stdClass;
		}
	}

	// form title functions
	public static function showTitleWithPreFix($title) {
		if (!empty($title)) {
			$title = ' - ' . $title;
		}
		JToolbarHelper::title(Text::_('COM_VISFORMS') . $title, 'visform');
	}

	public static function appendTitleAppendixFormat($text) {
		return ' <small><small>[ ' . $text . ' ]</small></small>';
	}

	public static function getFormattedServerDateTime($value, $format = 'Y-m-d H:i:s') {
		if ($value && $value !== Factory::getDbo()->getNullDate()) {
			$date = Factory::getDate($value, 'UTC');
			$date->setTimezone(new DateTimeZone(Factory::getConfig()->get('offset')));
			$value = $date->format($format, true, false);
		}
		return $value;
	}

	public static function checkValueIsEmpty($test, $type, $checkForEmptyCal = false) {
		switch ($type) {
			case 'calculation' :
				if ($checkForEmptyCal) {
					return self::checkNumberValueIsZero($test);
				}
				else {
					return false;
				}
			case 'location' :
				return self::checkLocationValueIsEmpty($test);
			default:
				return empty($test);
		}
	}

	public static function checkNumberValueIsZero($test) {
		$number = str_replace(",", ".", $test);
		$isZero = (0 == $number) ? true : false;
		return $isZero;
	}

	public static function checkLocationValueIsEmpty($test) {
		if (empty($test)) {
			return true;
		}
		if (($test['lat'] === '') || ($test['lng'] === '')) {
			return true;
		}
		return false;
	}

	public static function getDataLogFilename($formid) {
		$logPath = 'visforms_datalogs';
		return $logPath . '/form_' . $formid . '.php';
	}
}