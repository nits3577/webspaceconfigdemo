<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) :
	if (isset($displayData['form'])) :
		$form = $displayData['form'];
		$html = array();
		$context = (isset($form->context)) ? $form->context : '';
		$name = $context . 'viscaptcha_response';
		$errorDivClass = 'fc-tbx' . $name . '_field';
		if (isset($form->captcha)) {
			$ctrlGroupBtClasses = $form->ctrlGroupBtClasses;
			$indentedBtClasses = $form->indentedBtClasses;
            $captchaLabelClasses = $form->captchaLabelClasses . ' align-self-center';
			$captchalabel = (!empty($form->captchalabel)) ? $form->captchalabel : Text::_('COM_VISFORMS_CAPTCHA_LABEL');
			$inputClass = (!empty($form->captchacustominfo)) ? 'form-control col visToolTip' : 'form-control';
			$input = '<input class="' . $inputClass . '" type="text" id="' . $name . '" name="' . $name . '" data-error-container-id="' . $errorDivClass . '" required="true"';
			$input .= (!empty($form->showcaptchalabel)) ? ' placeholder="' . $captchalabel . '"' : '';
			$input .= (!empty($form->captchacustominfo)) ? ' title="' . htmlspecialchars($form->captchacustominfo, ENT_COMPAT, 'UTF-8') . '" data-bs-toogle' : '';
			$input .= '/>';
			$html[] = '<div class="form-group row required">';
			$html[] = '<label class="' . $captchaLabelClasses . '" id="captcha-lbl" for="' . $name . '">' . $captchalabel . '</label>';
			$html[] = '<div class="' . $ctrlGroupBtClasses . ' justify-content-evenly">';
			$html[] = LayoutHelper::render('visforms.captcha.viscaptchaimg', array('form' => $form, 'class' => ' col  mb-3'), null, array('component' => 'com_visforms'));
			$html[] = LayoutHelper::render('visforms.captcha.viscaptcharefresh', array('form' => $form, 'class' => 'col align-self-center'), null, array('component' => 'com_visforms'));
            $html[] = '<span class=" col d-inline-flex align-self-center">';
			$html[] = $input;
            $html[] = '</span>';
			$html[] = '</div>';
			$html[] = '</div>';
			//Create a div with the right class where we can put the validation errors into
			$html[] = '<div class="row">';
			$html[] = '<div class="' . $indentedBtClasses . '">';
			$html[] = '<div class="' . $errorDivClass . '"></div>';
			$html[] = '</div>';
			$html[] = '</div>';
		}
		if (!empty($form->captchacustominfo)) {
            HTMLHelper::_('visforms.visformsTooltip');
        }
		echo implode('', $html);
	endif;
endif; ?>