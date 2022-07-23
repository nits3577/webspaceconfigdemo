<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2019 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
require_once __DIR__ . '/bt5base.php';

class VisformsBt5FieldHelper extends VisformsBt5BaseHelper {

	protected $field;

	public function setField($field) {
		$this->field = $field;
	}

	// set bootstrap width classes for control (from label width)
	public function getCtClasses() {
        $field = $this->field;
        // width always 12 in total
        $classes = ($field->labelBootstrapWidth != "12") ? 'offset-' . $field->labelBootstrapWidth . ' col-' . (12 - $field->labelBootstrapWidth) : 'col-12';
        foreach ($this->breakPoints as $breakPoint) {
            $name = 'labelBootstrapWidth' . $breakPoint;
            $lcBreakPoint = lcfirst($breakPoint);
            $classes .= ($field->$name != "12") ? ' offset-' . $lcBreakPoint . '-' . $field->$name . ' col-' . $lcBreakPoint . '-' . (12 - $field->$name) : '';
        }
        return $classes;
	}

	// set bootstrap width classes for label
	public function getLabelClass() {
        $field = $this->field;
        $labelClass = 'col-' . $field->labelBootstrapWidth;
        foreach ($this->breakPoints as $breakPoint) {
            $name = 'labelBootstrapWidth' . $breakPoint;
            $lcBreakPoint = lcfirst($breakPoint);
            $labelClass .= ($field->$name != "12") ? ' col-' . $lcBreakPoint . '-' . $field->$name : '';
        }
        $labelClass .= (!empty($field->show_label)) ? ' asterix-ancor sr-only' : ' asterix-ancor';
        return $labelClass;
	}
}