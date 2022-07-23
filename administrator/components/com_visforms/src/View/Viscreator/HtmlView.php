<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 * @since        Joomla 3.6.2
 */

namespace Visolutions\Component\Visforms\Administrator\View\Viscreator;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Visolutions\Component\Visforms\Administrator\Model\VisfieldModel;
use Visolutions\Component\Visforms\Administrator\View\ItemViewBase;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends ItemViewBase
{
	public $sidebar;
	public $typefield;

    protected function setMembers() {
        $this->editViewName   = "viscreator";
        $this->controllerName = 'viscreator';
        $this->hideButtons    = true;

	    // get creator typefield from field model
	    $fieldModel = new VisfieldModel(array('ignore_request' => true)); //JModelLegacy::getInstance('Visfield', 'VisformsModel', array('ignore_request' => true));
	    $fieldForm  = $fieldModel->getForm();
	    $this->typefield = $fieldForm->getField('typefield', '');
    }

    protected function getTitle() {
        return Text::_('COM_VISFORMS_FORM_CREATOR');
    }

    protected function setToolbar() {
    	// side bar
	    \VisformsHelper::addSubmenu('viscreator');
	    $this->sidebar = HTMLHelper::_('sidebar.render');
	    // tool bar
	    ToolbarHelper::link('javascript:visHelper.navigate(1);', 'COM_VISFORMS_CREATOR_BUTTON_OPEN_FORM', 'file');
	    ToolbarHelper::link('javascript:visHelper.navigate(2);', 'COM_VISFORMS_CREATOR_BUTTON_OPEN_FIELDS', 'list');
	    if (\VisformsAEF::checkAEF(\VisformsAEF::$subscription)) {
		    ToolbarHelper::link('javascript:visHelper.navigate(3);', 'COM_VISFORMS_CREATOR_BUTTON_OPEN_PDF_TEMPLATES', 'file-2');
	    }
	    ToolbarHelper::link('javascript:visHelper.navigate(4);', 'COM_VISFORMS_CREATOR_BUTTON_OPEN_FORM_DATA', 'archive');
	    ToolbarHelper::link('javascript:visHelper.navigate(5);', 'COM_VISFORM_CREATOR_BUTTONS_CREATE_MAIN_MENU', 'home');
	    ToolbarHelper::link('javascript:visHelper.navigate(6);', 'COM_VISFORM_CREATOR_BUTTONS_CREATE_USER_MENU', 'user');
	    ToolbarHelper::link('javascript:visHelper.navigate(7);', 'COM_VISFORMS_RESET', 'unpublish');
	    //JToolbarHelper::link('javascript:visHelper.test();', 'Test', 'screen');
	    //JToolbarHelper::cancel("visform.cancel", 'COM_VISFORMS_CLOSE');
    }

	protected function setHideMainMenu() {
		Factory::getApplication()->input->set('hidemainmenu', 0);
	}
}