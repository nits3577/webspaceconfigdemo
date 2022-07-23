<?php
/**
 * Visforms control html for radio for bootstrap default layout
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
		$k = count($field->opts);
		$checked = "";
		$inputAttributes = (!empty($field->attributeArray)) ? ArrayHelper::toString($field->attributeArray, '=', ' ', true) : '';
		$asList = (isset($field->display) && $field->display == 'LST') ? true : false;
		for ($j = 0; $j < $k; $j++) {
			// option specific label class
			$labelClass = (!empty($field->opts[$j]['labelclass'])) ? $field->opts[$j]['labelclass'] . ' ' : '';
			$labelClass .= (!empty($asList)) ? '' : 'form-check-label';
			if ($field->opts[$j]['selected'] != false) {
				$checked = 'checked="checked" ';
			} else {
				$checked = "";
			}
			if (!empty($field->opts[$j]['disabled'])) {
				$disabled = ' disabled="disabled" data-disabled="disabled" ';
			} else {
				$disabled = "";
			}
			$html[] = '<div class="form-check' . ((empty($asList)) ? ' form-check-inline' : '') . '">';

			$html[] = '<input id="field' . $field->id . '_' . $j . '" name="' . $field->name . '" value="' . $field->opts[$j]['value'] . '" ' . $checked . $disabled . $inputAttributes . ' aria-labelledby="' . $field->name . 'lbl ' . $field->name . 'lbl_' . $j . '" data-error-container-id="fc-tbxfield' . $field->id . '" />';
            $html[] = '<label class="' . $labelClass . ' ' . $field->labelCSSclass . '" id="' . $field->name . 'lbl_' . $j . '" for="field' . $field->id . '_' . $j . '">';
            $html[] = $field->opts[$j]['label'] . '</label>';
			$html[] = '</div>';
		}
		$input = implode('', $html);

		echo $input;
	endif;
endif; ?>