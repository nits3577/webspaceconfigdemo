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

class VisformsPlaceholderEntryTextarea extends VisformsPlaceholderEntry {

	public function getReplaceValue() {
		if (!empty($this->field->keepBr)) {
			return HTMLHelper::_('visforms.replaceLinebreaks', $this->rawData, "<br />");
		}
		else {
			return HTMLHelper::_('visforms.replaceLinebreaks', $this->rawData, " ");
		}
	}

}