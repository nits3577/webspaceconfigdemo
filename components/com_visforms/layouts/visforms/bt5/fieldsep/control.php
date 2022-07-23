<?php
/**
 * Visforms control html for fieldseparator for default layout
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
		//input
		$html = array();

		$html[] = '<hr ';
		if (!empty($field->attributeArray)) {
			//add all attributes
			$html[] = ArrayHelper::toString($field->attributeArray, '=', ' ', true);
		}
		if (!empty($field->noborder)) {
			$html[] = ' style="display:none;"';
		}
		$html[] = '/>';

		echo implode('', $html);
	endif;
endif; ?>