<?php
/**
 * Visforms field url class
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
use Visolutions\Component\Visforms\Site\Model\VisformsModel as VisformsModelSite;

require_once(__DIR__ . '/text.php');

class VisformsFieldUrl extends VisformsFieldText
{
	protected function setField() {
		//preprocessing field
		$this->extractDefaultValueParams();
		$this->extractGridSizesParams();
		$this->extractRestrictions();
		$this->mendBooleanAttribs();
		$this->setIsConditional();
        // uses fillWith() from VisformsFieldText
        $fillWith = $this->fillWith();
        if ($fillWith !== false) {
            // if we have a special default value set in field declaration we use this
            $this->field->attribute_value = $fillWith;
        }
		$this->removeInvalidQueryValues();
		$this->setEditValue();
		$this->setConfigurationDefault();
		$this->setEditOnlyFieldDbValue();
		$this->setFieldDefaultValue();
		$this->setDbValue();
		$this->setRedirectParam();
		$this->escapeCustomRegex();
		$this->setCustomJs();
		$this->setFieldsetCounter();
		$this->setEnterKeyAction();
		$this->setShowRequiredAsterix();
	}

	protected function setFieldDefaultValue() {
		$field = $this->field;
		if ($this->form->displayState === VisformsModelSite::$displayStateIsNewEditData) {
			if ((isset($this->field->editValue))) {
				$this->field->attribute_value = $this->field->editValue;
			}
			$this->field->dataSource = 'db';
			return;
		}
		//if we have a POST Value, we use this
		if ((count($_POST) > 0) && isset($_POST['postid']) && ($_POST['postid'] == $this->form->id)) {
			//this will create a error message on form display
			$this->validateUserInput('postValue');
			//$_POST is not set if field was disabled when form was submitted
			if (isset($_POST[$field->name])) {
				$this->field->attribute_value = $this->postValue;
			} //use default values
			else {
				$this->field->attribute_value = $this->field->configurationDefault;
			}
			$this->field->dataSource = 'post';
			return;
		}

		//if we have a GET Value and field may use GET values, we uses this
		if (isset($field->allowurlparam) && ($field->allowurlparam == true)) {
			$urlparams = Factory::getApplication()->getUserState('com_visforms.urlparams.' . $this->form->context, null);
			if (!empty($urlparams) && (is_array($urlparams)) && (isset($urlparams[$this->field->name]))) {
				$queryValue = $urlparams[$this->field->name];
			}
			if (isset($queryValue)) {
				$this->field->attribute_value = $queryValue;
				$this->field->dataSource = 'query';
				return;
			}
		}

		//Nothing to do
		return;
	}

	protected function validateUserInput($inputType) {
		$type = $this->type;
		$value = $this->$inputType;
		//Empty value is valid
		if ((!isset($value)) || ($value === '')) {
			return;
		}
		//if a value is set we test it is a valid url
		if (VisformsValidate::validate($type, array('value' => $value))) {
			return;
		} else {
			//invalid user inputs - set field->isValid to false
			$this->field->isValid = false;
			//set the Error Message
			$error = VisformsMessage::getMessage($this->field->label, $type);
			$this->setErrorMessageInForm($error);
			return;
		}
	}

	protected function removeInvalidQueryValues() {
		$type = $this->type;
		$app = Factory::getApplication();
		$urlparams = $app->getUserState('com_visforms.urlparams.' . $this->form->context);
		if (empty($urlparams) || !is_array($urlparams) || !isset($urlparams[$this->field->name])) {
			return;
		}
		$queryValue = $urlparams[$this->field->name];
		//empty string is a valid value (= field value is not set)
		if (($queryValue !== '')) {
			$valid = VisformsValidate::validate($type, array('value' => $queryValue));
			if (empty($valid)) {
				//remove invalid queryValue ulrparams array and set urlparams to Null if the array is empty
				unset($urlparams[$this->field->name]);
				if (!(count($urlparams) > 0)) {
					$urlparams = null;
				}
				$app->setUserState('com_visforms.urlparams.' . $this->form->context, $urlparams);
			}
		}
	}
}