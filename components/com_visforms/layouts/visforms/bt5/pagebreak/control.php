<?php
/**
 * Visforms control html for pagebreak button for default layout
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
use Joomla\CMS\Language\Text;

if (!empty($displayData)) :
	if (isset($displayData['field'])) :
		$field = $displayData['field'];
		$mpDisplayType = $field->mpdisplaytype;
		//0 is multi page, 1 = accordion
		if (!empty($mpDisplayType) && ($mpDisplayType == 1)) :
			$firstPanelCollapsed = $field->firstpanelcollapsed;
			$html = array();
			$accordionid = (!empty($field->accordionid)) ? $field->accordionid : 'visformaccordion';
			$collapseid = 'collapse' . $field->id;
			$panelState = ((!empty($field->accordioncounter)) && ($field->accordioncounter == 1) && empty($firstPanelCollapsed)) ? 'show' : '';
			if ((!empty($field->accordioncounter)) && ($field->accordioncounter > 1)) {
				//close last row-fluid from previous accordion
				$html[] = '</div>';
				//close previous accordion
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '</div>';
			} else if ($field->accordioncounter == 1) {
				//close last div class="row" from view file which may contain fields that are placed before the first accordion
				$html[] = '</div>';
				//open accordion container
				$html[] = '<div class="accordion mb-3" id="' . $accordionid . '">';
			}
			$html[] = '<div class="accordion-group card">';
			$html[] = '<div class="accordion-heading card-header">';
			$html[] = '<h4 class=""><a class="accordion-toggle" data-bs-toggle="collapse" data-bs-target="#'.$collapseid.'">'.$field->label.'</a></h4>';
			$html[] = '</div>';
			$html[] = '<div id="' . $collapseid . '" class="accordion-body collapse ' . $panelState . '" data-bs-parent="#'.$accordionid.'">';
			$html[] = '<div class="accordion-inner card-body">';
			$html[] = '<div class="row">';
			echo implode('', $html);
		else :
            // Display back/next button left and right
            $buttonDisplayDirection = (!empty($field->fieldsetcounter)) && ($field->fieldsetcounter > 1) ? 'justify-content-between' : 'justify-content-end';
			$html = array();
			$html[] = '</div>';
			$html[] = '<div class="form-group row '.$buttonDisplayDirection.' pbBtnCon">';
			if ((!empty($field->fieldsetcounter)) && ($field->fieldsetcounter > 1)) {
				$backButtonText = (!empty($field->backbtntext)) ? $field->backbtntext : Text::_('COM_VISFORMS_STEP_BACK');
				$backBtnClass = (!empty($field->backbtncssclass)) ? 'btn back_btn col-auto ' . $field->backbtncssclass : 'btn back_btn col-auto ';
				//add a back button
				$html[] = '<input type="button" class="' . $backBtnClass . '" value="' . $backButtonText . '"/>';
			}
			$html[] = '<input ';
			if (!empty($field->attributeArray)) {
				//add all attributes
				$html[] = ArrayHelper::toString($field->attributeArray, '=', ' ', true);
			}
			$html[] = '/>';
			$html[] = '</div>';

			echo implode('', $html);
		endif;
	endif;
endif; ?>