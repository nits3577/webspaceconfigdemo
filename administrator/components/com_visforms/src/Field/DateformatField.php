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
namespace Visolutions\Component\Visforms\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\ListField;
use Visolutions\Component\Visforms\Administrator\Model\VisdatasModel;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class DateformatField extends ListField
{
	public $type = 'DateFormat';

	protected function getInput() {
		$storedDateExists = false;
        //get field defaultvalues
        $model = new VisdatasModel();
        $datas = $model->getItems();
        $id = Factory::getApplication()->input->getInt('id', 0);
        //as soon as user inputs are stored we do not allow to change date format
        if ((isset($datas)) && is_array($datas) && (count($datas) > 0)) {
            $fname = 'F'.$id;
            foreach ($datas as $data) {
                if (isset($data->$fname) && ($data->$fname != '')) {
	                $this->onchange = 'formatFieldDateChange(this, \'' . $this->value. '\', \'' . Text::sprintf(("COM_VISFORMS_DATEFORMAT_CANNOT_BE_CHANGED_JS")) . '\')';
	                $storedDateExists = true;
                    break;
                }
            }
            if (!$storedDateExists) {
	            $this->onchange = 'formatFieldDateChange(this, \'' . $this->value. '\', \'\')';
            }
        }
        else {
	        $this->onchange = 'formatFieldDateChange(this, \'' . $this->value. '\', \'\')';
        }


		$data = $this->getLayoutData();
		$data['options'] = (array) $this->getOptions();
		return $this->getRenderer($this->layout)->render($data);
	}

	protected function getOptions() {
		// Initialise variables.
		$options = array();
		$options[0]       = HTMLHelper::_('select.option',  'd.m.Y;%d.%m.%Y', 'DD.MM.YYYY');
		$options[1]         = HTMLHelper::_('select.option',  'm/d/Y;%m/%d/%Y','MM/DD/YYYY');
		$options[2]         = HTMLHelper::_('select.option',  'Y-m-d;%Y-%m-%d','YYYY-MM-DD');

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
