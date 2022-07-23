<?php
/**
 * Visforms view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Visforms;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Visolutions\Component\Visforms\Administrator\Model\VisdatasModel;
use Visolutions\Component\Visforms\Administrator\Model\VispdfsModel;
use Visolutions\Component\Visforms\Administrator\View\ItemsViewBase;
use Joomla\CMS\HTML\HTMLHelper;

class HtmlView extends ItemsViewBase
{
	public $update_message;
	public $hasPdf;
	public $datasModel;
	public $pdfsModel;

    function __construct($config = array()) {
        parent::__construct($config);
        $this->viewName     = 'Visforms';
        $this->editViewName = 'Visform';
	    $this->hasPdf       = \VisformsAEF::checkAEF(\VisformsAEF::$subscription);
        HTMLHelper::_('jquery.framework');
    }

    protected function setMembers() {
        $this->canDo = \VisformsHelper::getActions();

        // show update message once
        $this->update_message = $this->app->getUserState('com_visforms.update_message');
        if (isset($this->update_message)) {
            $this->app->setUserState('com_visforms.update_message', null);
        }
        // load datas model
	    $this->datasModel = new VisdatasModel( array('ignore_request' => true));
        // load pdfs model: get pdfs total of each form
	    if($this->hasPdf) {
		    $this->pdfsModel = new VispdfsModel(array('ignore_request' => true));
	    }
    }

    protected function getTitle() {
        return Text::_('COM_VISFORMS_SUBMENU_FORMS');
    }

    protected function setToolbar() {
        $toolbar = Toolbar::getInstance('toolbar');
        if ($this->canDo->get('core.create')) {
            $toolbar->addNew('visform.add');
        }
        if ($this->canDo->get('core.edit.state') || $this->canDo->get('core.delete') || $this->canDo->get('core.edit') || $this->canDo->get('core.edit.own') ||
            ($this->user->authorise('core.create', 'com_visforms')
                && $this->user->authorise('core.edit', 'com_visforms')
                && $this->user->authorise('core.edit.state', 'com_visforms'))) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
        }
        if ($this->canDo->get('core.edit.state')) {

            $childBar->publish('visforms.publish')->listCheck(true);
            $childBar->unpublish('visforms.unpublish')->listCheck(true);
            $childBar->checkin('visforms.checkin')->listCheck(true);
            if ($this->user->authorise('core.create', 'com_visforms')
                && $this->user->authorise('core.edit', 'com_visforms')
                && $this->user->authorise('core.edit.state', 'com_visforms')) {
                $childBar->popupButton('batch')
                    ->text('JTOOLBAR_BATCH')
                    ->selector('collapseModal')
                    ->listCheck(true);
            }
        }
        if ($this->canDo->get('core.delete')) {
            $childBar->delete('visforms.delete')
                ->text('COM_VISFORMS_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }
        if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')) {
            $childBar->edit('visform.edit');
        }

        if (\VisformsAEF::checkAEF(\VisformsAEF::$subscription) && $this->user->authorise('core.create', 'com_visforms')
            && $this->user->authorise('core.edit', 'com_visforms')
            && $this->user->authorise('core.edit.state', 'com_visforms')) {
            $childBar->popupButton('export')
                ->text('COM_VISFORMS_EXPORT_FORM_DEFINITION')
                ->icon('icon-drawer')
                ->selector('exportFormModal')
                ->listCheck(true);
        }
        if (\VisformsAEF::checkAEF(\VisformsAEF::$subscription) && $this->user->authorise('core.create', 'com_visforms')) {
            $toolbar->popupButton('import')
                ->text('COM_VISFORMS_IMPORT_FORM_DEFINITION')
                ->icon('icon-file')
                ->selector('importFormModal');
        }
    }

	// implementation

	public function getPdfsTotal($fid) {
    	if(isset($this->pdfsModel)) {
		    return $this->pdfsModel->getItemsTotal($fid);
	    }
	    return 0;
	}

	public function getDatasTotal($fid) {
    	if(isset($this->datasModel)) {
		    return $this->datasModel->getItemsTotal($fid);
	    }
	    return 0;
	}
}
