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

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData) && isset($displayData['text'])) {
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="' . $displayData['class'] . '"' : '';
	$text = $displayData['text'];
	PluginHelper::importPlugin('content');
	if (empty($text)) {
		echo '<' . $htmlTag . $class . '>' . $text . '</' . $htmlTag . '>';
	} else {
		echo  HTMLHelper::_('content.prepare','<' . $htmlTag . $class . '><a href="mailto:' . $text . '">' . $text . '</a></' . $htmlTag . '>');
	}
}