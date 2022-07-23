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

$requiredText = (!empty($displayData) && !empty($displayData['requiredText'])) ? $displayData['requiredText'] : Text::_('COM_VISFORMS_REQUIRED');
$form = (!empty($displayData) && !empty($displayData['form']) && isset($displayData['form']->formlayout)) ? $displayData['form']->formlayout : 'bthorizontal';
switch ($form) {
	case 'bt3horizontal':
	case 'editbt3horizontal':
		echo '<div class="form-group">';
		echo '<label class="control-label col-sm-3 vis_mandatory">' . $requiredText . ' *</label>';
		echo '</div>';
		break;
	default :
		echo '<div class="control-group">';
		echo '<label class="control-label vis_mandatory">' . $requiredText . ' *</label>';
		echo '</div>';
		break;
}