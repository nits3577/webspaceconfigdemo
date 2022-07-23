<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

// displaydata: form, data, extension, htmltag, class, pparams
if (!empty($displayData) && isset($displayData['form']) && isset($displayData['text']) ) {
	$displayData['text'] = (empty($displayData['text'])) ? Text::_('JNO') : Text::_('JYES');
	$displayData['name'] = 'displayismfd';
	echo LayoutHelper::render('visforms.datas.fields.defaultoverhead', $displayData, null, array('component' => 'com_visforms'));
}