<?php
/**
 * Visforms HTML class for reset button
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
require_once(__DIR__ . '/submit.php');

/**
 * Create HTML of a reset button according to it's type
 *
 * @package		Joomla.Site
 * @subpackage	com_visforms
 * @since		1.6
 */
class VisformsHtmlReset extends VisformsHtmlSubmit
{
    public function __construct($field, $decorable, $attribute_type)
    {
        $attribute_type = "reset";
        parent::__construct($field, $decorable, $attribute_type);
    }

    // bootstrap 5
    public function setControlBt5HtmlClasses($field) {
        $field->attribute_class = (!empty($this->field->fieldCSSclass)) ? ' btn col-auto ms-2 ' : ' btn btn-danger col-auto ms-2';
        return $field;
    }

    // bootstrap 4
	public function setControlHtmlClasses($field) {
		$field->attribute_class = (!empty($this->field->fieldCSSclass)) ? ' btn ' : ' btn btn-danger';
		return $field;
	}

	// uikit 3
	public function setUikit3ControlHtmlClasses($field) {
		$field->attribute_class = (!empty($this->field->fieldCSSclass)) ? '' : ' uk-button uk-button-danger';
		return $field;
	}

	// uikit 2
	public function setUikit2ControlHtmlClasses($field) {
		$field->attribute_class = (!empty($this->field->fieldCSSclass)) ? '' : ' uk-button uk-button-danger';
		return $field;
	}
}