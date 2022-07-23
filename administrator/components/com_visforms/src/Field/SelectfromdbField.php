<?php

/**
 * Visform field Selectfromdb
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 */
namespace Visolutions\Component\Visforms\Administrator\Field;

// no direct access
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class SelectfromdbField extends ListField
{

	public $type = 'Selectfromdb';

	protected function getInput() {
		$textfieldname = "a.title";
		$where = array();
		$table = '';
		$order = '';
		$valueprefix = '';
		$textprefix = '';
		// Initialize JavaScript field attributes.
		if (!(empty($this->element['table']))) {
			$table = $this->getAttribute('table');
			unset($this->element['table']);
		}
		if (!(empty($this->element['textfieldname']))) {
			$textfieldname = $this->getAttribute('textfieldname');
			unset($this->element['textfieldname']);
		}
		if (!(empty($this->element['where']))) {
			$where[] = $this->getAttribute('where');
			unset($this->element['where']);
		}
		if (!(empty($this->element['singleform']))) {
			$where[] = 'fid = ' . $this->form->getData()->get('id');;
			unset($this->element['singleform']);
		}
		if (!(empty($this->element['order']))) {
			$order = $this->getAttribute('order');
			unset($this->element['order']);
		}
		if (!(empty($this->element['textprefix']))) {
			$textprefix = $this->getAttribute('textprefix');
			unset($this->element['textprefix']);
		}
		if (!(empty($this->element['valueprefix']))) {
			$valueprefix = $this->getAttribute('valueprefix');
			unset($this->element['valueprefix']);
		}
		// Get the field options.
        $options = array_merge($this->getOptions(), $this->addSubscriptionOptions());
		$options = HTMLHelper::_('visforms.createSelectFromDb', $table, $options, $textfieldname, $where, $order, $textprefix, $valueprefix);
		$data = $this->getLayoutData();
		$data['options'] = (array) $options;
		return $this->getRenderer($this->layout)->render($data);
	}

	// there is just no useful way to add conditional options in the form definition xml
    // in f_text_fillwith, f_hidden_fillwith
	private function addSubscriptionOptions() {
		$subOptions = array();
		$options = array();
		if (empty(\VisformsAEF::checkAEF(\VisformsAEF::$subscription))) {
			return $subOptions;
		}
		switch ($this->fieldname) {
			case 'f_hidden_fillwith' :
				$options = array(
					'url' => 'COM_VISFORMS_CURRENT_PAGE_URL',
					'2' => 'COM_VISFORMS_CONNECTED_USER_NAME',
					'3' => 'COM_VISFORMS_CONNECTED_USER_USERNAME',
					'usermail' => 'COM_VISFORMS_CONNECTED_USER_EMAIL',
					'address1' => 'COM_VISFORMS_CONNECTED_USER_PROFILE_FIELD_CITY',
					'address2' => 'COM_VISFORMS_CONNECTED_USER_PROFILE_FIELD_REGION',
					'city' => 'COM_VISFORMS_CONNECTED_USER_NAME',
					'region' => 'COM_VISFORMS_CONNECTED_USER_PROFILE_FIELD_COUNTRY',
					'postal_code' => 'COM_VISFORMS_CONNECTED_USER_PROFILE_FIELD_POSTAL_CODE',
					'phone' => 'COM_VISFORMS_CONNECTED_USER_PROFILE_FIELD_PHONE'
				);
                if (Factory::getApplication()->getIdentity()->authorise('core.create.sql.statement', 'com_visforms')) {
                    $options['sql'] = 'COM_VISFORMS_SQL_STATEMENT';
                }
				break;
            case 'f_text_fillwith' :
            case 'f_email_fillwith' :
            case 'f_number_fillwith':
            case 'f_url_fillwith':
            case 'f_date_fillwith':
                if (Factory::getApplication()->getIdentity()->authorise('core.create.sql.statement', 'com_visforms')) {
                    $options['sql'] = 'COM_VISFORMS_SQL_STATEMENT';
                }
                break;
			default:
				break;
		}
        foreach ($options as $key => $label) {
            $option = new \stdClass();
            $option->value = $key;
            $option->text = Text::_($label);
            $subOptions[] = $option;
        }
		return $subOptions;
	}
}
