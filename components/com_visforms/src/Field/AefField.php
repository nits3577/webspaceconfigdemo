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
namespace  Visolutions\Component\Visforms\Site\Field;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\Field\HiddenField;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/aef/aef.php';

class AefField extends HiddenField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Aef';

	public function renderField($options = array()) {
		return $this->getInput();
	}

	protected function getInput() {
		$feature = $this->getAttribute('feature', 8);
		$minversion = $this->getAttribute('version', '');
		if (empty($minversion)) {
			$featureexists = \VisformsAEF::checkAEF($feature);
			if (!empty($featureexists)) {
				$this->value = "1";
			} 
			else {
				$this->value = "0";
			}
		} 
		else {
			$installedversion = \VisformsAEF::getVersion($feature);
			if (!empty($installedversion) && (version_compare($installedversion, $minversion, 'ge'))) {
				$this->value = "1";
			} 
			else {
				$this->value = "0";
			}
		}
		return parent::getInput();
	}

	protected function getLabel() {
		return '';
	}
}
