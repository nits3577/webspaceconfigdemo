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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\ListField;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/aef/aef.php';

/**
 * Form Field class for Visforms.
 * Supports list field types. 
 * Prevents a user from changing the selected option if the field has restrictions (it's values are used in conditional field parameter of other fields)
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class ConditionalchangablelistField extends ListField
{
	protected $type = 'ConditionalChangableList';

	protected function getInput() {
		// initialize JavaScript field attributes
        // different onclick handler if field is has restrictions statement
        $form = $this->form; 
        $label = $form->getFieldAttribute($this->fieldname, 'label');
        if (empty($label)) {
            $label = $form->getFieldAttribute($this->fieldname, 'label', null, 'defaultvalue');
        }

        // get restrictions
        $restrictions = $this->form->getData()->get('restrictions');
        if (!empty($restrictions) && $restrictions) {
            $rFieldNames = array ();
            foreach ($restrictions as $r => $value) {
                $rFieldNames[] = implode(', ', array_keys($value));           
            }
            $fieldNames = implode(', ', $rFieldNames);
            // as long as the restrictions are not empty we do not allow to change the typefield
	        $this->onchange = ' fieldUsed(this, \'' . $this->value. '\', \'' . Text::sprintf(("COM_VISFORMS_FIELD_HAS_RESTICTIONS_JS"), $fieldNames, Text::_($label)) . '\')';
        }

		$data = $this->getLayoutData();
		$data['options'] = (array) $this->getAllOptions();
		return $this->getRenderer($this->layout)->render($data);
	}

	protected function getOptions() {
		return parent::getOptions();
	}

	protected function createAefTypefieldOption($value, $label) {
		$option = new \StdClass();
		$option->value = $value;
		$option->text = Text::_($label);
		$option->disabled = false || ($this->readonly && $value != $this->value);
		$option->checked = false;
		$option->selected = false;
		return $option;
	}

	// interface

	public function getAllOptions() {
		// get xml defined options
		$options = (array) $this->getOptions();

		// add AEF dependent options
		if (!empty(\VisformsAEF::checkAEF(\VisformsAEF::$subscription)) && ($this->fieldname == 'typefield')) {
			$options[] = $this->createAefTypefieldOption('pagebreak', 'COM_VISFORMS_FIELD_PAGE_BREAK');
			$options[] = $this->createAefTypefieldOption('calculation', 'COM_VISFORMS_FIELD_CALCULATION');
			$options[] = $this->createAefTypefieldOption('location', 'COM_VISFORMS_FIELD_LOCATION');
			$options[] = $this->createAefTypefieldOption('signature', 'COM_VISFORMS_FIELD_SIGNATURE');
			$options[] = $this->createAefTypefieldOption('radiosql', 'COM_VISFORMS_FIELD_RADIO_FROM_SQL');
			$options[] = $this->createAefTypefieldOption('selectsql', 'COM_VISFORMS_FIELD_SELECT_FROM_SQL');
			$options[] = $this->createAefTypefieldOption('multicheckboxsql', 'COM_VISFORMS_FIELD_MULTICHECKBOX_FROM_SQL');
		}
		return $options;
	}

	public function getCreatorInput($value = '', $disabled = false) {
		$this->disabled = $disabled;
		$this->class = 'creator-typefield';
		$this->onchange = '';
		$this->value = $value;
		return parent::getInput();
	}
}
