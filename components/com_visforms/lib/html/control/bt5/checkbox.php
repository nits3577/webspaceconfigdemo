<?php
/**
 * Visforms create control HTML class
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;

class VisformsHtmlControlBt5Checkbox extends VisformsHtmlControl
{
	public function getControlHtml() {
		$field = $this->field->getField();
		$clabel = $this->createlabel();
		$field->clabel = $clabel;
		$ccustomtext = $this->getCustomText();
		$field->ccustomtext = $ccustomtext;
		$field->inverseBtLabelClasses = $this->getCtClasses();
		return LayoutHelper::render('visforms.bt5.checkbox.control', array('field' => $field), null, array('component' => 'com_visforms'));
	}

	public function createLabel() {
		//label is part of the control
		return '';
	}
}

        