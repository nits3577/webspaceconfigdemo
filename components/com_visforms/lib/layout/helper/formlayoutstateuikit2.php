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
require_once JPATH_ROOT . '/components/com_visforms/lib/layout/helper/uikit2form.php';

class FormLayoutStateUikit2 implements FormLayoutState {

	protected $breakPoints = array('Sm', 'Md', 'Lg');
	protected $form;
	protected $formHelper;

	public function fixInvalidLayoutSelection($formLayout) {
		$form = $formLayout->getForm();
		if (empty(VisformsAEF::checkAEF(VisformsAEF::$subscription))) {
			$form->formlayout = 'visforms';
			$formLayout->updateForm($form);
			$formLayout->setFormLayoutState(new FormLayoutStateVisforms());
		}
		return $form;
	}

	public function setLayoutOptions($formLayout) {
		$this->form = $formLayout->getForm();
		if (empty($this->form->captchaLabelUikit2Width) || ($this->form->displaysublayout != 'individual')) {
			if ($this->form->displaysublayout == 'stacked' || $this->form->displaysublayout == 'individual') {
				$this->form->captchaLabelUikit2Width = "10";
			}
			else {
				$this->form->captchaLabelUikit2Width = "3";
			}
		}
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'captchaLabelUikit2Width' . $breakPoint;
			if (empty($this->form->$name) || ($this->form->displaysublayout != 'individual')) {
				if ($this->form->displaysublayout == 'stacked' || $this->form->displaysublayout == 'individual') {
					$this->form->$name = "6";
				}
				else {
					$this->form->$name = "2";
				}
			}
		}
		$this->formHelper = new VisformsUikit2FormHelper();
		$this->formHelper->setForm($this->form);
		$this->getCtrlGroupClasses();
		$this->getCaptchaLabelClasses();
		$this->setButtonClass();
		$formLayout->updateForm($this->form);
		return $this->form;
	}

	protected function setButtonClass() {
		if (empty($this->form->summarybtncssclass)) {
			$this->form->summarybtncssclass = 'uk-button uk-button-primary uk-margin-right';
		}
		if (empty($this->form->correctbtncssclass)) {
			$this->form->correctbtncssclass = 'uk-button uk-button-primary uk-margin-right';
		}
		if (empty($this->form->backbtncssclass)) {
			$this->form->backbtncssclass = 'uk-button uk-button-primary uk-margin-right';
		}
		if (empty($this->form->savebtncssclass)) {
			$this->form->savebtncssclass = 'uk-button uk-button-primary uk-margin-right';
		}
		if (empty($this->form->cancelbtncssclass)) {
			$this->form->cancelbtncssclass = 'uk-button uk-button-danger uk-margin-right';
		}
	}

	protected function getCtrlGroupClasses() {
		$this->form->ctrlGroupBtClasses =  $this->formHelper->getCtrlGroupUikit2Classes();
	}


	protected function getCaptchaLabelClasses() {
		$this->form->captchaLabelClasses = $this->formHelper->getLabelClass();
	}
}