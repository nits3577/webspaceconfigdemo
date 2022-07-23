<?php
/**
 * Visforms HTML class for checkbox fields
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

use Joomla\CMS\HTML\HTMLHelper;

/**
 * Create HTML of a checkbox field according to it's type
 *
 * @package        Joomla.Site
 * @subpackage     com_visforms
 * @since          1.6
 */
class VisformsHtmlCheckbox extends VisformsHtml
{
	/**
	 *
	 * Constructor
	 *
	 * @param object $field field object as extracted from database
	 */
	public function __construct($field, $decorable, $attribute_type) {
		if (is_null($decorable)) {
			$decorable = false;
		}
		$attribute_type = 'checkbox';
		parent::__construct($field, $decorable, $attribute_type);
	}

    // create an array of html attributes used on the control
	public function getFieldAttributeArray() {
		$attributeArray = array('class' => '');
		if (!empty($this->field->disableEnterKey)) {
			$attributeArray['class'] = 'noEnterSubmit ';
		}
		//attributes are stored in xml-definition-fields with name that ends on _attribute_attributename (i.e. _attribute_checked).
		//each form field is represented by a fieldset in xml-definition file
		//each form field should have in xml-definition file a field with name that ends on _attribute_class. default " " or class-Attribute values for form field
		foreach ($this->field as $name => $value) {
			if (!is_array($value)) {
				if (strpos($name, 'attribute_') !== false) {
					if ($name == 'attribute_required') {
						$attributeArray['aria-required'] = 'true';
					}
					if ($value || $name == 'attribute_class') {
						$newname = str_replace('attribute_', "", $name);
						if ($newname == "class") {
							$value = $attributeArray[$newname] . $value . (!empty($value) ? ' ' : '') . $this->field->fieldCSSclass;
							$attributeArray[$newname] .= $value;
						}
						else {
							$attributeArray[$newname] = $value;
						}
					}
				}
				if ($name == 'name') {
					$attributeArray['name'] = $value;
				}
				if ($name == 'id') {
					$value = 'field' . $value;
					$attributeArray['id'] = $value;
					$attributeArray['data-error-container-id'] = 'fc-tbx' . $value;
				}
				if (($name == 'isDisabled') && ($value == true)) {
					$attributeArray['class'] .= " ignore";
					$attributeArray['disabled'] = "disabled";
				}
				if (($name == 'isDisplayChanger') && ($value == true)) {
					$attributeArray['class'] .= " displayChanger";
				}
				if (($name == 'isValid') && ($value == false)) {
					$attributeArray['class'] .= " error";
				}
				$attributeArray['aria-labelledby'] = $this->field->name . 'lbl';
			}
		}
		return $attributeArray;
	}

	// Set the individual field type specific span attributes for bt 2 multi column layout (don't set for checkboxes)
	public function setBootstrapSpanClasses($field) {
		return $field;
	}

	// Remove the individual field type specific non bootstrap class attributes (no removal for buttons)
	// bootstrap 2 and 3
	public function removeNoBootstrapClasses($field) {
		return $field;
	}
    // bootstrap 5
    public function setControlBt5HtmlClasses($field) {
        $field->attribute_class = " form-check-input";
        return $field;
    }

	// bootstrap 4
	public function setControlHtmlClasses($field) {
		$field->attribute_class = " form-check-input";
		return $field;
	}

	// uikit 3
	public function setUikit3ControlHtmlClasses($field) {
		$field->attribute_class = " uk-checkbox";
		return $field;
	}

	// uikit 2
	public function setUikit2ControlHtmlClasses($field) {
		$field->attribute_class = " uk-checkbox";
		return $field;
	}
}