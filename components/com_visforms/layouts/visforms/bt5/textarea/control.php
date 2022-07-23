<?php
/**
 * Visforms control html of textarea field for default layout
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
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) :
	if (isset($displayData['field'])) :
		$field = $displayData['field'];
		$html = array();
		//We inclose textareas with HTML-Editor that are not readonly in a div
		if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor) {
			// textarea element to which the tooltip is attached will be disabled
			// attach tooltip to new enclosing div
			$tooltip = (!empty($field->custominfo)) ? ' title="'.htmlspecialchars($field->custominfo, ENT_COMPAT, 'UTF-8').'" data-bs-toggle="tooltip"' : '';
			$tooltipClass = (!empty($field->custominfo)) ? ' visToolTip' : '';
			// we do not need to include HTMLHelper::_('visforms.visformsTooltip'), because that was already done on the textarea
			$html[] = '<div class="editor'.$tooltipClass.'"'.$tooltip.'>';
		}
		$html[] = '<textarea ';
		if (!empty($field->attributeArray)) {
			$html[] = ArrayHelper::toString($field->attributeArray, '=', ' ', true);
		}
		$html[] = '>';
		$html[] = $field->initvalue;
		$html[] = '</textarea>';
		//field is a textarea with html Editor we have to close the div
		if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor) {
			$html[] = '</div>';
		}
		$input = implode('', $html);
		echo $input;
	endif;
endif;
?>
   
