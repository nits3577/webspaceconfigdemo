<?php
/**
 * Visforms control html for select for default layout
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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

if (!empty($displayData)) :
	if (isset($displayData['field'])) :
		$field = $displayData['field'];
		$k = count($field->opts);
		$html = array();
		if (!empty($field->render_as_datalist)) {
			if ((!empty($field->attributeArray)) && isset($field->attributeArray['class'])) {
				// a bit dirty, but we have to remove a potential "form-control" in class attribute
				$field->attributeArray['class'] = str_replace('form-control', '', $field->attributeArray['class']);
			}
			$attribs = (!empty($field->attributeArray)) ? ArrayHelper::toString($field->attributeArray, '=', ' ', true) : '';
			$html[] = '<table id="field' . $field->id . '" ' . $attribs . '>';
			for ($j = 0; $j < $k; $j++) {
				$html[] = '<tr>';
				$html[] = '<td>';
				$html[] = $field->opts[$j]['label'];
				$html[] = '</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}
		else {
			$checked = array();
            // add a first empty option if necessary
            $options = HTMLHelper::_('visformsselect.createEmptyOption', $field, array());
			for ($j = 0; $j < $k; $j++) {
				$optKey = array();
				if ($field->opts[$j]['selected'] != false) {
					$checked[] = $field->opts[$j]['value'];
				}
				if (!empty($field->opts[$j]['disabled'])) {
					$optKey['disable'] = true;
				}
                $option = new stdClass();
                $option->value = $field->opts[$j]['value'];
                $option->text = $field->opts[$j]['label'];
                if (!empty($field->opts[$j]['labelclass'])) {
                    $option->class = $field->opts[$j]['labelclass'];
                }
                if (!empty($field->opts[$j]['disabled'])) {
                    $option->disable = true;
                }
                $options[] = $option;
			}
			$html[] = HTMLHelper::_('select.genericlist', $options, $field->name . '[]', array('id' => 'field' . $field->id, 'list.attr' => $field->attributeArray, 'list.select' => $checked));
		}
		echo implode('', $html);
	endif;
endif; ?>