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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) :
	if (isset($displayData['form'])) :
		$form = $displayData['form'];
		if (isset($form->captcha)) {
			$ctrlGroupBtClasses = $form->ctrlGroupBtClasses;
			$indentedBtClasses = $form->indentedBtClasses;
			$captchaLabelClasses = $form->captchaLabelClasses;
			$html = array();
			$captchalabel = (!empty($form->captchalabel)) ? $form->captchalabel : Text::_('COM_VISFORMS_CAPTCHA_LABEL');
			$captcha = JCaptcha::getInstance('recaptcha');
			$input = $captcha->display(null, 'dynamic_recaptcha_1', 'required text-left');
			$html[] = '<div class="form-group row required">';
			$html[] = '<label class="' . $captchaLabelClasses . '" id="captcha-lbl" for="recaptcha_response_field">' . $captchalabel . '</label>';
			$html[] = '<div class="' . $ctrlGroupBtClasses . '">';
			if (!empty($form->captchacustominfo)) {
				$html[] = '<div class="visToolTip" title="' . htmlspecialchars($form->captchacustominfo, ENT_COMPAT, 'UTF-8') . '" data-bs-toogle>';
			}
			$html[] = $input;
			if (!empty($form->captchacustominfo)) {
				$html[] = '</div>';
			}
			$html[] = '</div>';
			$html[] = '</div>';
			//Create a div with the right class where we can put the validation errors into
			$html[] = '<div class="row">';
			$html[] = '<div class="' . $indentedBtClasses . '">';
			$html[] = '<div class="fc-tbxrecaptcha_response_field"></div>';
			$html[] = '</div>';
			$html[] = '</div>';
            if (!empty($form->captchacustominfo)) {
                HTMLHelper::_('visforms.visformsTooltip');
            }
		}
		echo implode('', $html);
	endif;
endif; ?>