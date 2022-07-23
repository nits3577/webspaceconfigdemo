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

use Joomla\CMS\HTML\HTMLHelper;

class VisformsPlaceholderEntrySelect extends VisformsPlaceholderEntry {

	protected static $customParams = array (
		'DISPLAYOPTIONVALUE' => 'COM_VISFORMS_PLACEHOLDER_PARAM_OPTION_VALUE'
	);

	public function getReplaceValue() {
		if (isset($this->rawData) && ($this->rawData === '')) {
			return '';
		}
		$customParams = self::$customParams;
		if (!empty($this->param) && array_key_exists($this->param, $customParams)) {
			switch ($this->param) {
				case 'DISPLAYOPTIONVALUE' :
					return HTMLHelper::_('visformsselect.removeNullbyte', $this->rawData);
				default:
					return implode(', ', HTMLHelper::_('visformsselect.mapDbValueToOptionLabel', $this->rawData, $this->field->list_hidden));
			}
		}
		// legacy for old field option useoptionvalueinplaceholder
		else {
			if (!empty($this->field->useoptionvalueinplaceholder)) {
				return HTMLHelper::_('visformsselect.removeNullbyte', $this->rawData);
			}
		}
		// default return is file name
		return implode(', ', HTMLHelper::_('visformsselect.mapDbValueToOptionLabel', $this->rawData, $this->field->list_hidden));
	}
}