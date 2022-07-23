<?php
/**
 * Visforms field text class
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
use Joomla\CMS\User\UserHelper;
use Visolutions\Component\Visforms\Site\Model\VisformsModel as VisformsModelSite;
use Joomla\CMS\HTML\HTMLHelper;

require_once JPATH_ROOT . '/administrator/components/com_visforms/lib/visformsSql.php';

class VisformsFieldText extends VisformsField
{
	public function __construct($field, $form) {
		parent::__construct($field, $form);
		//store potentiall query Values for this field in the session
		$this->setQueryValue();
		$this->postValue = $this->input->post->get($field->name, '', 'STRING');
	}

	protected function setField() {
		//preprocessing field
		$this->extractDefaultValueParams();
		$this->extractGridSizesParams();
		$this->extractRestrictions();
		$this->mendBooleanAttribs();
		$this->setIsConditional();
		$fillWith = $this->fillWith();
		if ($fillWith !== false) {
			//if we have a special default value set in field declaration we use this
			$this->field->attribute_value = $fillWith;
		}
		$this->setEditValue();
		$this->setConfigurationDefault();
		$this->setEditOnlyFieldDbValue();
		$this->setFieldDefaultValue();
		$this->setDbValue();
		$this->setRedirectParam();
		$this->escapeCustomRegex();
		$this->setCustomJs();
		$this->setFieldsetCounter();
		$this->mendInvalidUncheckedValue();
		$this->setEnterKeyAction();
		$this->setShowRequiredAsterix();
	}

	protected function setFieldDefaultValue() {
		$field = $this->field;
		//Could we just start with attribute_value = config default and only change it, if a post is set?
		if ($this->form->displayState === VisformsModelSite::$displayStateIsNewEditData) {
			if ((isset($this->field->editValue))) {
				$this->field->attribute_value = $this->field->editValue;
			}
			$this->field->dataSource = 'db';
			return;
		}
		//if we have a POST Value, we use this
		if ((count($_POST) > 0) && isset($_POST['postid']) && ($_POST['postid'] == $this->form->id)) {
			if (isset($_POST[$field->name])) {
				$this->field->attribute_value = $this->postValue;
			}
			//reset to configuration default
			//set to empty, if the field is disabled and the form is re-displayed it will be reset to the default after business logic is performed
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
				//only return, if a query value exists; This value is already validate!
				$this->field->attribute_value = $queryValue;
				$this->field->dataSource = 'query';
				return;
			}
		}
		//Nothing to do
		return;
	}

	protected function setDbValue() {
		if (isset($this->field->dataSource) && $this->field->dataSource == 'post') {
			$this->field->dbValue = $this->postValue;
		}
	}

	protected function setEditOnlyFieldDbValue() {
		$this->field->editOnlyFieldDbValue = $this->field->configurationDefault;
	}

	protected function setRedirectParam() {
		if (isset($this->field->dataSource) && $this->field->dataSource == 'post' && (!empty($this->field->addtoredirecturl))) {
			$this->field->redirectParam = $this->postValue;
		}
	}

	protected function fillWith() {
		$field = $this->field;
		if ((isset($field->fillwith) && $field->fillwith != "")) {
		    // process fillwith sql first
		    if ($field->fillwith == 'sql') {
                try {
                    $sqlHelper = new \VisformsSql($field->defaultvalue_sql);
                    $value = $sqlHelper->getItemsFromSQL('loadResult');
                    return $value;
                }
                catch (\Exception $e) {
                    return '';
                }
            }
		    // process fillwith user data
			// if we edit stored user inputs we want to use the user profile of the user who submitted the form as default fill with values
			if (($this->form->displayState === VisformsModelSite::$displayStateIsNewEditData) || ($this->form->displayState === VisformsModelSite::$displayStateIsRedisplayEditData)) {
				$data = $this->form->data;
				$userId = $data->created_by;
				if (!empty($userId)) {
					$user = Factory::getUser($userId);
				}
			}
			// use user profile of logged in user
			else {
				$user = Factory::getApplication()->getIdentity();
				$userId = $user->get('id');
			}
			if ($userId != 0) {
				if ($field->fillwith == "0") {
					return false;
				}
				if ($field->fillwith == "1") {
					return $user->get('name');
				}
				if ($field->fillwith == "2") {
					return $user->get('username');
				}
				preg_match('/^CF(\d+)$/', $field->fillwith, $matches);
				if ($matches && !empty($matches[1])) {
					$customfieldid = $matches[1];
					return HTMLHelper::_('visforms.getCustomUserFieldValue', $customfieldid, $user);
				}
				$userProfile = UserHelper::getProfile($userId);
				if ((!(empty($userProfile->profile))) && (is_array($userProfile->profile))) {
					if (!(empty($userProfile->profile[$field->fillwith]))) {
						return $userProfile->profile[$field->fillwith];
					}
				}
			}
		}
		return false;
	}

	protected function setConfigurationDefault() {
		$task = $this->input->getCmd('task', '');
		$this->field->configurationDefault = $this->field->attribute_value;
		if (!$this->isEditOrSaveEditTask) {
			$urlparams = Factory::getApplication()->getUserState('com_visforms.urlparams.' . $this->form->context, null);
			if (!empty($urlparams) && (is_array($urlparams)) && (isset($urlparams[$this->field->name]))) {
				$queryValue = $urlparams[$this->field->name];
			}
			//if form was originally called with valid url params, reset to this url params
			$this->field->configurationDefault = (isset($this->field->allowurlparam) && ($this->field->allowurlparam == true) && isset($queryValue)) ? $queryValue : $this->field->attribute_value;
		}
	}
}