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

class FormLayoutStateBt5 implements FormLayoutState {

	protected $breakPoints = array('Sm', 'Md', 'Lg', 'Xl', 'Xxl');
	protected $form;

	public function fixInvalidLayoutSelection($formLayout) {
        $hasIndividualLayout = VisformsAEF::checkAEF(VisformsAEF::$subscription);
        $form = $formLayout->getForm();
        if (empty($hasIndividualLayout)) {
            if (isset($form->displaysublayout) && $form->displaysublayout == 'individual') {
                // reset to default
                $form->displaysublayout = 'horizontal';
                $formLayout->updateForm($form);
            }
        }
        return $form;
	}

	public function setLayoutOptions($formLayout) {
		$this->form = $formLayout->getForm();
		if (empty($this->form->captchaLabelBootstrapWidth) || ($this->form->displaysublayout != 'individual')) {
			if ($this->form->displaysublayout == 'stacked' || $this->form->displaysublayout == 'individual') {
				$this->form->captchaLabelBootstrapWidth = "12";
			}
			else {
				$this->form->captchaLabelBootstrapWidth = "3";
			}
		}
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'captchaLabelBootstrapWidth' . $breakPoint;
			if (empty($this->form->$name) || ($this->form->displaysublayout != 'individual')) {
				if ($this->form->displaysublayout == 'stacked' || $this->form->displaysublayout == 'individual') {
					$this->form->$name = "12";
				}
				else {
					$this->form->$name = "3";
				}
			}
		}
		$this->getCtrlGroupBtClasses();
		$this->getIndentedBtClasses();
		$this->getCaptchaLabelClasses();
		$this->setButtonClass();
		$formLayout->updateForm($this->form);
		return $this->form;
	}

	protected function setButtonClass() {
		if (empty($this->form->summarybtncssclass)) {
			$this->form->summarybtncssclass = 'btn-info col-auto';
		}
		if (empty($this->form->correctbtncssclass)) {
			$this->form->correctbtncssclass = 'btn-info col-auto';
		}
		if (empty($this->form->backbtncssclass)) {
			$this->form->backbtncssclass = 'btn-info col-auto';
		}
		if (empty($this->form->savebtncssclass)) {
			$this->form->savebtncssclass = 'btn-primary col-auto';
		}
		if (empty($this->form->cancelbtncssclass)) {
			$this->form->cancelbtncssclass = 'btn-secondary col-auto';
		}
	}

	protected function getCtrlGroupBtClasses() {
		$classes = (!empty($this->form->showcaptchalabel) && $this->form->captchaLabelBootstrapWidth != "12") ? 'offset-' . $this->form->captchaLabelBootstrapWidth . ' col-' . (12 - $this->form->captchaLabelBootstrapWidth) : (($this->form->captchaLabelBootstrapWidth != "12") ? ' col-' . (12 - $this->form->captchaLabelBootstrapWidth) : ' col-12');
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'captchaLabelBootstrapWidth' . $breakPoint;
			$lcBreakPoint = lcfirst($breakPoint);
			$classes .= (!empty($this->form->showcaptchalabel) && $this->form->$name != "12") ? ' offset-' . $lcBreakPoint . '-' . $this->form->$name . ' col-' . $lcBreakPoint . '-' . (12 - $this->form->$name) : ((empty($this->form->showcaptchalabel) && $this->form->$name != "12") ? ' col-' . $lcBreakPoint . '-' . (12 - $this->form->$name) : '');
		}
		$this->form->ctrlGroupBtClasses = $classes;
	}

	// use for indentation of error div
	protected function getIndentedBtClasses() {
		$indentedBtClasses = ($this->form->captchaLabelBootstrapWidth != "12") ? 'offset-' . $this->form->captchaLabelBootstrapWidth . ' col-' . (12 - $this->form->captchaLabelBootstrapWidth) : 'col-12';
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'captchaLabelBootstrapWidth' . $breakPoint;
			$lcBreakPoint = lcfirst($breakPoint);
			$indentedBtClasses .= ($this->form->$name != "12") ? ' offset-' . $lcBreakPoint . '-' . $this->form->$name . ' col-' . $lcBreakPoint . '-' . (12 - $this->form->$name) : '';
		}
		$this->form->indentedBtClasses =  $indentedBtClasses;
	}

	protected function getCaptchaLabelClasses() {
		$labelClasses = 'col-' . $this->form->captchaLabelBootstrapWidth;
		foreach ($this->breakPoints as $breakPoint) {
			$name = 'captchaLabelBootstrapWidth' . $breakPoint;
			$lcBreakPoint = lcfirst($breakPoint);
			$labelClasses .= ($this->form->$name != "12") ? ' col-' . $lcBreakPoint . '-' . $this->form->$name : '';
		}
		$labelClasses .= (!empty($this->form->showcaptchalabel)) ? ' asterix-ancor sr-only' : ' asterix-ancor';
		$this->form->captchaLabelClasses =  $labelClasses;
	}
}