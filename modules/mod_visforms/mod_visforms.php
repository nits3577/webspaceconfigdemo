<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2021 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Visolutions\Module\Visforms\Site\Helper\VisformsHelper as modVisformsHelper;
/** @var object $module */
/** @var object $params */
/** @var object $app */
$base_dir = JPATH_SITE . '/components/com_visforms';
include_once JPATH_ADMINISTRATOR . '/components/com_visforms/include.php';

// load com_visforms language files (active language and default en-GB)
$language = Factory::getApplication()->getLanguage();
$language->load('com_visforms', JPATH_SITE);

$params->set('context', 'modvisform' . $module->id);
$visforms = modVisformsHelper::getForm($params);
if (empty($visforms)) {
	echo Text::_('COM_VISFORMS_FORM_MISSING');
	return false;
}
//check if user access level allows view
$user = Factory::getApplication()->getIdentity();
$groups = $user->getAuthorisedViewLevels();
$access = (isset($visforms->access) && in_array($visforms->access, $groups)) ? true : false;
if ($access == false) {
    $app->setUserState('com_visforms.' . $visforms->context, null);
	echo Text::_('COM_VISFORMS_ALERT_NO_ACCESS');
	return false;
}

$menu_params = $params;
$correspondingMenuId = $params->get('connected_menu_item', '');
$formLink =  "index.php?option=com_visforms&task=visforms.send&id=" . $visforms->id . ((!empty($correspondingMenuId)) ? "&Itemid=" . $correspondingMenuId : "");

$app = Factory::getApplication();
$input = $app->getInput();
$shared_session = Factory::getConfig()->get('shared_session', false);
$isYTB = $input->get('customizer', null);
// check, if we are in Yootehem Builder context and pass information
$previewOnly = ($isYTB && !$shared_session);
if ($previewOnly) {
    echo Text::_("MOD_VISFORMS_YOOTHEME_BUILDER_VIEW_NO_SHARED_SESSION");
    $params->set('previewOnly', $previewOnly);
}
require ModuleHelper::getLayoutPath('mod_visforms', $params->get('layout', 'default'));
