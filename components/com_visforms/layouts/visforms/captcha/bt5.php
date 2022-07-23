<?php
/**
 * Visforms captcha html for bootstrap 3 multi column layout
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

if (!empty($displayData)) :
	if (isset($displayData['form'])) :
		$form = $displayData['form'];
		$html = array();
		if (isset($form->captcha)) {
			switch ($form->captcha) {
				case 1 :
					echo LayoutHelper::render('visforms.captcha.bt5.visformscaptcha', array('form' => $form), null, array('component' => 'com_visforms'));
					break;
				case 2:
					echo LayoutHelper::render('visforms.captcha.bt5.recaptcha', array('form' => $form), null, array('component' => 'com_visforms'));
					break;
				default:
					break;
			}
		}
	endif;
endif; ?>

        