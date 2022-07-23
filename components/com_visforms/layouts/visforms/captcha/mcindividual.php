<?php
/**
 * Visforms captcha html for multi column layout
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
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) : 
    if (isset($displayData['form'])) :
        $form  = $displayData['form'];
        $clear = (!empty($displayData['clear'])) ? true : false;
	    $context = (isset($form->context)) ? $form->context : '';
	    $name = $context . 'viscaptcha_response';
	    $errorDivClass = (isset($form->captcha) && $form->captcha == 2) ? 'fc-tbxrecaptcha_response_field' : 'fc-tbx'.$name.'_field';
        $html = array();
        if (isset($form->captcha))
        {
            $captchaHeight = ((!empty($form->viscaptchaoptions)) && (!empty($form->viscaptchaoptions['image_height'])) && (!($form->captcha == 2))) ? (int)$form->viscaptchaoptions['image_height'] : 0;
            $styleHeight = (!empty($captchaHeight)) ? 'style="height: ' . $captchaHeight . 'px; line-height: ' . $captchaHeight . 'px;" ' : '';
            $labelcolspan = (($form->captcha == 2) && (!empty($form->grecaptcha2label_bootstrap_size))) ? (int) $form->grecaptcha2label_bootstrap_size : ((empty($form->showcaptchalabel)) ? 3 : 1);
            $colspan = 12 - $labelcolspan;
            $tooltip = (!empty($form->captchacustominfo)) ? ' title="'. htmlspecialchars($form->captchacustominfo, ENT_COMPAT, 'UTF-8') .'" data-bs-toogle' : '';
            $captchalabel = (isset($form->captchalabel)) ? $form->captchalabel : "Captcha";
            //Create a div with the right class where we can put the validation errors into
	        if (empty($form->errormessagenopopup)) {
		        $html[] = '<div class="'.$errorDivClass.'"></div>';
	        }
            $html[] = '<div class="row-fluid required">';
            $html[] = (!(isset($form->showcaptchalabel)) || ($form->showcaptchalabel == 0)) ? '<label class="span'.$labelcolspan.'" ' . $styleHeight . 'id="captcha-lbl" for="recaptcha_response_field">' . $captchalabel . '</label>' : '<span class ="span'.$labelcolspan.' asterix-ancor"></span>';
            switch ($form->captcha)
            {
                case 1 :
                    $html[] = '<div class="span5">';
                    $html[] = LayoutHelper::render('visforms.captcha.viscaptchaimg', array('form' => $form), null, array('component' => 'com_visforms'));
                    $html[] = LayoutHelper::render('visforms.captcha.viscaptcharefresh', array('form' => $form), null, array('component' => 'com_visforms'));
                    $html[] = '</div><div class="span4" '.$styleHeight.'>';
	                $html[] = '<input class="visCSStop10'.(!empty($form->preventsubmitonenter) ? " noEnterSubmit": "") . ((!empty($form->captchacustominfo)) ? " visToolTip" : "").'" type="text" id="'.$name.'" name="'.$name.'" data-error-container-id="'.$errorDivClass.'" required="required"'.$tooltip.' />';
                    $html[] = '</div>';
                    break;
                case 2:
                    $captcha = JCaptcha::getInstance('recaptcha');
                    if (!empty($form->captchacustominfo)) {
                        $html[] = '<div class="visToolTip"' . $tooltip . '>';
                    }
                    $html[] = $captcha->display(null, 'dynamic_recaptcha_1', 'span'. $colspan . ' required');
                    if (!empty($form->captchacustominfo)) {
                        $html[] = '</div>';
                    }
                    break;
                default :
                    return '';
            }

            $html[] = '</div>';
            if (!empty($form->errormessagenopopup)) {
	            $html[] = '<div class="'.$errorDivClass.'"></div>';
            }
            if (!empty($form->captchacustominfo)) {
                HTMLHelper::_('visforms.visformsTooltip');
            }
        }
        echo implode('', $html);
    endif;  
endif; ?>

        