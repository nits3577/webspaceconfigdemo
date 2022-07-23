<?php
/**
 * Visform field parentoptionslist
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\ListField;
use Visolutions\Component\Visforms\Administrator\Model\VisformModel;

class LabelgridwidthField extends ListField
{
	protected $type = 'Labelgridwidth';
	protected $formLayout;

	public function setup(\SimpleXMLElement $element, $value, $group = null) {
		$this->setFormLayout();
		// Fix for JCE triggering preparing events on forms multiple times
		$test = isset ($element['default']);
		if (($this->formLayout == 'bt4mcindividual' || $this->formLayout == 'bt5') && !$test) {
			$value = (empty($value) || $value > 12) ? 12 : $value;
			$element->addAttribute('default', "12");
		}
		if (($this->formLayout == 'uikit3') && !$test) {
			$value = (empty($value)|| $value > 6) ? 6 : $value;
			$element->addAttribute('default', 6);
		}
		if (($this->formLayout == 'uikit2') && !$test) {
			$value = (empty($value) || $value > 10) ? 10 : $value;
			$element->addAttribute('default', 10);
		}
		return parent::setup($element, $value, $group);
	}

	protected function getOptions() {
		$options = array();
		//extract form id
		if ($this->formLayout == 'bt4mcindividual' || $this->formLayout == 'bt5') {
			$options[] = $this->createOptionObj(1,12);
			$options[] = $this->createOptionObj(2,12);
			$options[] = $this->createOptionObj(3,12);
			$options[] = $this->createOptionObj(4,12);
			$options[] = $this->createOptionObj(6,12);
			$options[] = $this->createOptionObj(7,12);
			$options[] = $this->createOptionObj(8,12);
			$options[] = $this->createOptionObj(9,12);
			$options[] = $this->createOptionObj(10,12);
			$options[] = $this->createOptionObj(11,12);
			$options[] = $this->createOptionObj(12,12, true);
		}
		if ($this->formLayout == 'uikit3') {
			$options[] = $this->createOptionObj(1, 6);
			$options[] = $this->createOptionObj(2, 6);
			$options[] = $this->createOptionObj(3, 6);
			$options[] = $this->createOptionObj(4, 6);
			$options[] = $this->createOptionObj(5, 6);
			$options[] = $this->createOptionObj(6, 6, true);
		}
		if ($this->formLayout == 'uikit2') {
			$options[] = $this->createOptionObj(1, 10);
			$options[] = $this->createOptionObj(2, 10);
			$options[] = $this->createOptionObj(3, 10);
			$options[] = $this->createOptionObj(4, 10);
			$options[] = $this->createOptionObj(5, 10);
			$options[] = $this->createOptionObj(6, 10);
			$options[] = $this->createOptionObj(7, 10);
			$options[] = $this->createOptionObj(8, 10);
			$options[] = $this->createOptionObj(9, 10);
			$options[] = $this->createOptionObj(10, 10, true);
		}
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}

	private function createOptionObj($num, $max, $selected = false) {
		$o = new \StdClass();
		$o->value = $num;
		$o->text = $num . ' / ' . $max . ' '. Text::_('COM_VISFORMS_OF_CONTROL_WIDTH');
		$o->disabled = false;
		$o->checked = ($num === $max) ? true : false; //$selected;
		$o->selected = ($num === $max) ? true : false; //$selected;
		return $o;
	}

	protected function setFormLayout() {
		$fid = Factory::getApplication()->input->getInt('fid', 0);
		if(empty($fid)) {
			$this->formLayout = 'visforms';
			return;
		}
		$model = new VisformModel(array('ignore_request' => true));//JModelLegacy::getInstance('Visform', 'VisformsModel', array('ignore_request' => true));
		$visform = $model->getItem($fid);
		$this->formLayout = $visform->layoutsettings['formlayout'];
	}
}
