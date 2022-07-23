<?php
/**
 * Visforms control html for submit button for default layout
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

if (!empty($displayData)) :
	if (isset($displayData['field'])) :
		$field = $displayData['field'];
		$html = array();
		$html[] = '<input ';
		if (!empty($field->attributeArray)) {
			$html[] = ArrayHelper::toString($field->attributeArray, '=', ' ', true);
		}
		$html[] = '/>';
		echo implode('', $html);
	endif;
endif; ?>