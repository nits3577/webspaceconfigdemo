<?php
/**
 * Visform field typefield
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

namespace Visolutions\Component\Visforms\Administrator\Field;
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/aef/aef.php';

/**
 * Form Field class for Visforms.
 * Supports list field types.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class VflayoutsField extends ListField
{

	protected $type = 'Vflayouts';

	protected function getInput() {
		// Get the field options.
		$options = (array) $this->getOptions();
		if (!empty(\VisformsAEF::checkAEF(\VisformsAEF::$subscription))) {
			$options[] = $this->createOption('bt4mcindividual', 'COM_VISFORMS_BOOTSTRAP4_MULTICOLUMN_INDIVIDUAL');
			$options[] = $this->createOption('uikit3', 'COM_VISFORMS_UIKIT3');
			$options[] = $this->createOption('bt3default', 'COM_VISFORMS_LAYOUT_BOOTSTRAP3_DEFAULT');
			$options[] = $this->createOption('btdefault', 'COM_VISFORMS_LAYOUT_BOOTSTRAP_DEFAULT');
			$options[] = $this->createOption('uikit2', 'COM_VISFORMS_UIKIT2');
		}

		$data = $this->getLayoutData();
		$data['options'] = (array) $options;
		return $this->getRenderer($this->layout)->render($data);
	}

	protected function getOptions() {
		return parent::getOptions();
	}

	protected function createOption($value, $text) {
		$option = new \stdClass();
		$option->value = $value;
		$option->text = Text::_($text);
		$option->disabled = false || ($this->readonly && $value != $this->value);
		$option->checked = false;
		$option->selected = false;
		return $option;
	}
}
