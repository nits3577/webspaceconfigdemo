<?php
/**
 * Visforms Layout class Bootstrap default
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

class VisformsHtmllayoutBt5 extends VisformsHtmllayout
{
	public function prepareHtml() {
		//attach error messages array for javascript validation to field
		$this->setFieldCustomErrorMessageArray();
		$this->cleanFieldProperties();
		$this->getFieldGroupBootstrapClasses();
		$this->setControlBt5HtmlClasses();
		$this->setToolTip();
		$this->setErrorId();
		$this->setFieldAttributeArray();
		$this->setFieldValidateArray();
		$this->setFieldControlHtml();
		return $this->field;
	}

	protected function setControlBt5HtmlClasses() {
	    // Bootstrap 5 specific field class (i.e. form-select)
        if (method_exists($this->fieldHtml, 'setControlBt5HtmlClasses')) {
            $this->field = $this->fieldHtml->setControlBt5HtmlClasses($this->field);
        }
		else {
			//default implementation
			$this->field->attribute_class = " form-control ";
		}
	}

	protected function setFieldControlHtml() {
		//get Instance of field html control class according to field type and layout type
		$ocontrol = VisformsHtmlControl::getInstance($this->fieldHtml, $this->type);
		if (!(is_object($ocontrol))) {
			//throw an error
		}
		else {
			//instanciate decorators
			$control = new VisformsHtmlControlDecoratorbt5($ocontrol);
		}
		//set field property
		$this->field->controlHtml = $control->getControlHtml();
	}

	protected function cleanFieldProperties() {
		$breakpoints = array('Sm', 'Md', 'Lg', 'Xl', 'Xxl');
		$this->field->attribute_class = "";
		if (!isset($this->field->show_label)) {
			$this->field->show_label = 0;
		}
		if (empty($this->field->fieldsPerRow) || ($this->subType != 'individual')) {
			$this->field->fieldsPerRow = "1";
		}
		if (empty($this->field->labelBootstrapWidth) || ($this->subType != 'individual')) {
			if ($this->subType == 'stacked' || $this->subType == 'individual') {
				$this->field->labelBootstrapWidth = "12";
			}
			else {
				$this->field->labelBootstrapWidth = "3";
			}
		}
		foreach ($breakpoints as $breakpoint) {
			$fieldsPerRow = 'fieldsPerRow' . $breakpoint;
			if (empty($this->field->$fieldsPerRow) || ($this->subType != 'individual')) {
				$this->field->$fieldsPerRow = "1";
			}
			$labelBootstrapWidth = 'labelBootstrapWidth' . $breakpoint;
			if (empty($this->field->$labelBootstrapWidth) || ($this->subType != 'individual')) {
				if ($this->subType == 'stacked' || $this->subType == 'individual') {
					$this->field->$labelBootstrapWidth = "12";
				}
				else {
					$this->field->$labelBootstrapWidth = "3";
				}
			}

		}
		if (!isset($this->field->custominfo)) {
			$this->field->custominfo = "";
		}
	}

	protected function getFieldGroupBootstrapClasses() {
		$this->field->fieldGroupBootstrapClasses = 'col-' . (12 / $this->field->fieldsPerRow);
		if ($this->field->fieldsPerRowSm != "1") {
			$this->field->fieldGroupBootstrapClasses .= ' col-sm-' . (12 / $this->field->fieldsPerRowSm);
		}
		if ($this->field->fieldsPerRowMd != "1") {
			$this->field->fieldGroupBootstrapClasses .= ' col-md-' . (12 / $this->field->fieldsPerRowMd);
		}
		if ($this->field->fieldsPerRowLg != "1") {
			$this->field->fieldGroupBootstrapClasses .= ' col-lg-' . (12 / $this->field->fieldsPerRowLg);
		}
		if ($this->field->fieldsPerRowXl != "1") {
			$this->field->fieldGroupBootstrapClasses .= ' col-xl-' . (12 / $this->field->fieldsPerRowXl);
		}
        if ($this->field->fieldsPerRowXxl != "1") {
            $this->field->fieldGroupBootstrapClasses .= ' col-xxl-' . (12 / $this->field->fieldsPerRowXxl);
        }
		if (isset($this->field->controlGroupCSSclass)) {
			$this->field->fieldGroupBootstrapClasses .= ' ' . trim($this->field->controlGroupCSSclass);
		}
	}
}