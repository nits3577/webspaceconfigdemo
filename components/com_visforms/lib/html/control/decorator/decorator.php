<?php
/**
 * Visforms decorator class for HTML controls
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

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Decorate HTML control according to layout
 *
 * @package		Joomla.Site
 * @subpackage	com_visforms
 * @since		1.6
 */
abstract class VisformsHtmlControlDecorator extends VisformsHtmlControl
{
	protected $control;
	protected $breakPoints;

	public function __construct($control) {
        $this->control = $control;
		$this->breakPoints = array('Sm', 'Md', 'Lg', 'Xl');
    }

	public function getControlHtml() {
		$field = $this->control->field->getField();
		$layout = $this->control->layout;
		$app = Factory::getApplication();
		$html = '';

		PluginHelper::importPlugin('visforms');
		//Trigger onVisformsBeforeHtmlPrepare event to allow changes on field properties before control html is created
		$app->triggerEvent('onVisformsBeforeHtmlPrepare', array('com_visforms.field', &$field, $layout));
		if ($this->control->field->getDecorable() == true) {
			//return decorated html string
			$html = $this->decorate();
		} 
		else {
			//return html string
			$html = $this->control->getControlHtml();
		}

		//Trigger onVisformsAfterHtmlPrepare event to allow changes on field properties after control html is created
		$app->triggerEvent('onVisformsAfterHtmlPrepare', array('com_visforms.field', &$field, &$html, $layout));

		return $html;
	}

	// bootstrap 4 and bootstrap 5
	protected function getCtrlGroupBtClasses() {
		$field = $this->control->field->getField();
		// show_label: 0 => show 1 => hide !
		$classes = (!empty($field->show_label) && $field->labelBootstrapWidth != "12") ? 'offset-' . $field->labelBootstrapWidth . ' col-' . (12 - $field->labelBootstrapWidth) : (($field->labelBootstrapWidth != "12") ? ' col-' . (12 - $field->labelBootstrapWidth) : ' col-12');
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'labelBootstrapWidth' . $breakPoint;
			$lcBreakPoint = lcfirst($breakPoint);
			$classes .= (!empty($field->show_label) && $field->$name != "12") ? ' offset-' . $lcBreakPoint . '-' . $field->$name . ' col-' . $lcBreakPoint . '-' . (12 - $field->$name) : ((empty($field->show_label) && $field->$name != "12") ? ' col-' . $lcBreakPoint . '-' . (12 - $field->$name) : '');
		}
		return $classes;
	}

	// use for indentation of error div in bootstrap 4 and bootstrap 5
	protected function getIndentedBtClasses() {
		$field = $this->control->field->getField();
		$indentedBtClasses = ($field->labelBootstrapWidth != "12") ? 'offset-' . $field->labelBootstrapWidth . ' col-' . (12 - $field->labelBootstrapWidth) : 'col-12';
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'labelBootstrapWidth' . $breakPoint;
			$lcBreakPoint = lcfirst($breakPoint);
			$indentedBtClasses .= ($field->$name != "12") ? ' offset-' . $lcBreakPoint . '-' . $field->$name . ' col-' . $lcBreakPoint . '-' . (12 - $field->$name) : '';
		}
		return $indentedBtClasses;
	}

	abstract protected function decorate();
}
?>