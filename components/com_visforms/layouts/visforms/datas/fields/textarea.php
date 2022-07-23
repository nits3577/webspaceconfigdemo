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
use Joomla\String\StringHelper;

if (!empty($displayData) && isset($displayData['text'])) {
	$text = $displayData['text'];
	$extension = (!empty($displayData['extension'])) ? $displayData['extension'] : 'component';
	$viewType = (!empty($displayData['viewType'])) ? $displayData['viewType'] : 'row';
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="' . $displayData['class'] . '"' : '';
	$pparams = (!empty($displayData['pparams'])) ? $displayData['pparams'] : array();
	if ($viewType === 'column') {
		switch ($extension) {
			case 'vfdataview' :
				if (isset($pparams['maxtextlength']) && !empty($pparams['maxtextlength']) && (StringHelper::strlen($text) > $pparams['maxtextlength'])) {
					$text = StringHelper::substr($text,0,$pparams['maxtextlength'])."...";
				}
				break;
			default:
				if (StringHelper::strlen($text) > 255) {
					$text = StringHelper::substr($text, 0, 255) . "...";
				}
				break;
		}
	}
	if (!empty($displayData['field']->keepBr)) {
		$text = HTMLHelper::_('visforms.replaceLinebreaks', $text, "<br />");
	}
	else {
		$text = HTMLHelper::_('visforms.replaceLinebreaks', $text, " ");
	}
	echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
}