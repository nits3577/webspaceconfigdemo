<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2019 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

if (!empty($displayData)) :
	if (isset($displayData['form'])) :
		$form = $displayData['form'];
		$class = 'captcharefresh' . $form->id . ((isset($displayData['class'])) ? ' ' . $displayData['class'] : '');
		echo '<img alt="' . Text::_('COM_VISFORMS_REFRESH_CAPTCHA') . '" class="' . $class . '" src="' . URI::root(true) . '/components/com_visforms/captcha/images/refresh.gif' . '" align="absmiddle" style="cursor:pointer"> &nbsp;';
		$script = "jQuery(window).on('load', function() {jQuery('.captcharefresh".$form->id."').trigger('click');});";
		Factory::getApplication()->getDocument()->addScriptDeclaration($script);
	endif;
endif;