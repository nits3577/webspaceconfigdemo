<?php
/**
 * Visforms html for admincontrollblock
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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

if (!empty($displayData)) :
	if (isset($displayData['form']) && isset($displayData['nbFields']) && isset($displayData['hasRequired'])) :
		$form = $displayData['form'];
		$nbFields = $displayData['nbFields'];
		$hasRequired = $displayData['hasRequired'];
		$summarypageid = $displayData['summarypageid'];
		$backButtonText = (!empty($form->backbtntext)) ? $form->backbtntext : Text::_('COM_VISFORMS_STEP_BACK');
		$backBtnClass = (!empty($form->backbtncssclass)) ? 'btn back_btn col-auto ' . $form->backbtncssclass : 'btn back_btn col-auto';
		$summaryButtonText = (!empty($form->summarybtntext)) ? $form->summarybtntext : Text::_('COM_VISFORMS_SUMMARY');
		$summayBtnClass = (!empty($form->summarybtncssclass)) ? 'btn summary_btn me-2 col-auto ' . $form->summarybtncssclass : 'btn summary_btn me-2 col-auto';
		$correctButtonText = (!empty($form->correctbtntext)) ? $form->correctbtntext : Text::_('COM_VISFORMS_CORRECT');
		$correctButtonClass = (!empty($form->correctbtncssclass)) ? 'btn correct_btn col-auto ' . $form->correctbtncssclass : 'btn correct_btn col-auto ';
		if (!empty($form->mpdisplaytype) && !empty($form->accordioncounter)) {
			echo '</div>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}

		echo '<div class="form-group row justify-content-between">';
		if (!empty($form->steps) && (int) $form->steps > 1) {
			echo ' <input type="button" class="' . $backBtnClass . '" value="' . $backButtonText . '" /> ';
		}
		echo '<input type="button" class="' . $summayBtnClass . '" value="' . $summaryButtonText . '" /> ';
		echo '</div>';
		echo '</fieldset>';
		echo '<fieldset class="fieldset-summarypage row">';
		if ((!empty($form->summarydescription)) && (!empty($form->summarydescriptionposition)) && ($form->summarydescriptionposition == 'top')) {
			echo '<div class="summarydesc">' . $form->summarydescription . '</div>';
		}
		echo '<div id="' . $summarypageid . '_summarypage"></div>';
		//Explantion for * if at least one field is requiered above captcha
		if ($hasRequired == true && $form->required == 'captcha') {
			echo LayoutHelper::render('visforms.requiredtext.bt5', array(), null, array('component' => 'com_visforms'));
		}
		if (isset($form->captcha) && ($form->captcha == 1 || $form->captcha == 2)) {
			echo LayoutHelper::render('visforms.captcha.bt5', array('form' => $form), null, array('component' => 'com_visforms'));
		}

		//Explantion for * if at least one field is requiered above submit
		if ($hasRequired == true && $form->required == 'bottom') {
			echo LayoutHelper::render('visforms.requiredtext.bt5', array(), null, array('component' => 'com_visforms'));
		}
		echo '<div class="clearfix"></div>';
		for ($i = 0; $i < $nbFields; $i++) {
			$field = $form->fields[$i];
			if (!empty($field->sig_in_footer)) {
				echo $field->controlHtml;
				echo '<div class="clearfix"></div>';
			}
		}
		echo '<div class="form-group row justify-content-center">';
		echo '<input type="button" class="' . $correctButtonClass . '" value="' . $correctButtonText . '" />';
		for ($i = 0; $i < $nbFields; $i++) {
			$field = $form->fields[$i];
			if (isset($field->isButton) && $field->isButton === true) {
				echo $field->controlHtml;
			}
		}
		echo '</div>';
		if ((!empty($form->displaysummarypage)) && ((!empty($form->summarydescription)) && (!empty($form->summarydescriptionposition)) && ($form->summarydescriptionposition == 'bottom'))) {
			echo '<div class="summarydesc">' . $form->summarydescription . '</div>';
		}
	endif;
endif; ?>