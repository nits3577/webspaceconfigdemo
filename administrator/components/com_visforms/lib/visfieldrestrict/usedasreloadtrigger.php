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

class VisformsVisfieldRestrictUsedAsReloadTrigger extends VisformsVisfieldRestrict {

	public function __construct($value, $id, $name, $fid = null) {
		$this->type = 'usedAsReloadTrigger';
		parent::__construct($value, $id, $name, $fid);
	}

	protected function addRestricts() {
		if (is_array($this->value)) {
			foreach ($this->value as $value) {
				if ((strpos($value, 'field') === 0)) {
					$restrict = array();
					$restrict['type'] = $this->type;
					$restrict['restrictedId'] = parent::getRestrictedId($value);
					$restrict['restrictorId'] = $this->id;
					$restrict['restrictorName'] = $this->name;
					$this->restricts[] = $restrict;
				}
			}
		}
	}
}