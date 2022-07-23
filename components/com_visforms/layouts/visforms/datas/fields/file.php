<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData) && isset($displayData['text']) && isset($displayData['field'])) {
	$field = $displayData['field'];
	$text = $displayData['text'];
	$viewType = (!empty($displayData['viewType'])) ? $displayData['viewType'] : 'row';
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="' . $displayData['class'] . '"' : '';
	$showLink = (isset($field->showlink) && !empty($field->showlink)) ? true : false;
	$isImage = VisformsmediaHelper::isImage(HTMLHelper::_('visforms.getUploadFileName', $text));
	$showAsImage = (($viewType === 'row' && !empty($field->displayImgAsImgInDetail)) || ($viewType === 'column' && !empty($field->displayImgAsImgInList))) ? true : false;
	if (!empty($showAsImage) && $isImage) {
		$text = '<img src="'.JUri::root(true) . '/' . HTMLHelper::_('visforms.getUploadFilePath', $text) . '" />';
		echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
	}
	else if (empty($text) || !$showLink) {
		$text = HTMLHelper::_('visforms.getUploadFileName', $text);
		$text = basename($text);
		echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
	} else {
		$text = HTMLHelper::_('visforms.getUploadFileLink', $text);
		echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
	}
}