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

class FieldgridwidthField extends ListField
{
	protected $type = 'Fieldgridwidth';

	protected function getOptions() {
		$options = array();
		//extract form id
		$fid = Factory::getApplication(array('ignore_request' => true))->input->getInt('fid', 0);
		$model = new VisformModel(); //JModelLegacy::getInstance('Visform', 'VisformsModel', array('ignore_request' => true));
		$visform = $model->getItem($fid);
		$layout = $visform->layoutsettings['formlayout'];
		if ($layout == 'bt4mcindividual' || $layout == 'bt5') {
			$options[] = $this->createOptionObj(1,12,12);
			$options[] = $this->createOptionObj(2,6, 12);
			$options[] = $this->createOptionObj(3, 4, 12);
			$options[] = $this->createOptionObj(4, 3, 12);
			$options[] = $this->createOptionObj(6, 2, 12);
			$options[] = $this->createOptionObj(12,1, 12);
		}
		if ($layout == 'uikit3') {
			$options[] = $this->createOptionObj(1, 6,6);
			$options[] = $this->createOptionObj(2, 3, 6);
			$options[] = $this->createOptionObj(3, 2, 6);
			$options[] = $this->createOptionObj(6, 1, 6);
		}
		if ($layout == 'uikit2') {
			$options[] = $this->createOptionObj(1, 10,10);
			$options[] = $this->createOptionObj(2, 5,10);
			$options[] = $this->createOptionObj(5, 2,10);
			$options[] = $this->createOptionObj(10, 1,10);
		}
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}

	private function createOptionObj($num, $fraction, $max) {
		$o = new \StdClass();
		$o->value = $num;
		$o->text = $fraction . ' / ' . $max . ' '. Text::_('COM_VISFORMS_OF_LINE_WIDTH');
		$o->disabled = false;
		$o->checked = false;
		$o->selected = false;
		return $o;
	}
}
