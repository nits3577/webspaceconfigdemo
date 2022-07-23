<?php
/**
 * Visforms label html for text field for bootstrap default layout
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

if (!empty($displayData)) :
	if (isset($displayData['field'])) :
		$field = $displayData['field'];
		$labelClass = $field->labelClass;
		$labelClass .= (!empty($field->labelCSSclass)) ? ' ' . $field->labelCSSclass : '';
		$labelClass = trim($labelClass);
		$html = array();
		$html[] = '<label class="' . $labelClass . '" id="' . $field->name . 'lbl" for="field' . $field->id . '">';
		$html[] = $field->label;
		$html[] = '</label>';
		echo implode('', $html);
	endif;
endif; ?>