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

class FormLayoutStateBtdefault implements FormLayoutState {

	public function fixInvalidLayoutSelection($formLayout) {
		$hasBt2Layouts = VisformsAEF::checkAEF(VisformsAEF::$subscription);
		$form = $formLayout->getForm();
		if (empty($hasBt2Layouts)) {
			$form->formlayout = 'visforms';
			if (isset($form->displaysublayout)) {
				// displaysublayout not used. Set to default
			    $form->displaysublayout = 'horizontal';
			}
			$formLayout->updateForm($form);
			$formLayout->setFormLayoutState(new FormLayoutStateVisforms());
		}
		else {
			if (isset($form->displaysublayout)) {
				switch ($form->displaysublayout) {
					case "horizontal" :
						$form->formlayout = "bthorizontal";
						break;
					case "individual" :
						$form->formlayout = "mcindividual";
						break;
				}
			}
            // displaysublayout not used. Set to default
            $form->displaysublayout = 'horizontal';
			$formLayout->updateForm($form);
		}
		return $form;
	}

	public function setLayoutOptions($formLayout) {
		// nothing to do
		return $formLayout->getForm();
	}
}