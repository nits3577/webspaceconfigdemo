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

class FormLayoutStateVisforms implements FormLayoutState {
	public function fixInvalidLayoutSelection($formLayout) {
        $form = $formLayout->getForm();
		if ($form->formlayout !== 'visforms') {
            $form->formlayout = 'visforms';
            $form->displaysublayout = 'horizontal';
            $formLayout->updateForm($form);
            $formLayout->setFormLayoutState(new FormLayoutStateVisforms());
        }
		return $form;
	}

	public function setLayoutOptions($formLayout) {
		// nothing to do
		return $formLayout->getForm();
	}
}