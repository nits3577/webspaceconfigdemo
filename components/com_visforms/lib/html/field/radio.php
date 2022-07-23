<?php
/**
 * Visforms HTML class for radios
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
 * Create HTML of a select according to it's type
 *
 * @package        Joomla.Site
 * @subpackage     com_visforms
 * @since          1.6
 */
class VisformsHtmlRadio extends VisformsHtml
{
	public function __construct($field, $decorable, $attribute_type) {
		if (is_null($attribute_type)) {
			$attribute_type = "radio";
		}
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
					//due to the way Joomla! creates a radio control we have to add value manually
					unset($attributeArray['value']);
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
			}
		}
		return $attributeArray;
	}

	// bootstrap 2
	public function setBootstrapSpanClasses($field) {
		return $field;
	}

	// bootstrap 2 and 3
	public function removeNoBootstrapClasses($field) {
		return $field;
	}

    // bootstrap 5
    public function setControlBt5HtmlClasses($field) {
        $field->attribute_class = ' form-check-input';
        return $field;
    }

	// bootstrap 4
	public function setControlHtmlClasses($field) {
		$field->attribute_class = ' form-check-input';
		return $field;
	}

	// uikit 3
	public function setUikit3ControlHtmlClasses($field) {
		$field->attribute_class = " uk-radio";
		return $field;
	}

	// uikit 2
	public function setUikit2ControlHtmlClasses($field) {
		$field->attribute_class = " uk-radio uk-width-1-1";
		return $field;
	}

	public function removeUnsupportedShowLabel($field) {
		unset($field->show_label);
		return $field;
	}
}