<?php
/**
 * Visdata view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Visdatas;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Visolutions\Component\Visforms\Administrator\View\ItemsViewBase;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

class HtmlView extends ItemsViewBase
{
    public $fields;

    function __construct($config = array()) {
        parent::__construct($config);
        $this->viewName     = 'visdatas';
        $this->editViewName = 'visdata';
	    $this->controllerName = 'visdatas';
    }

	protected function setMembers() {
        // todo: change implementation to default 0 everywhere including base class
        $this->fid = Factory::getApplication()->input->getInt('fid', 0);
        $this->canDo    = \VisformsHelper::getActions($this->fid);
        $this->fields   = $this->get('PublishedDatafields');
    }

    protected function getTitle() {
        return Text::_('COM_VISFORMS_VISFORM_DATA_RECORD_SETS');
    }

    protected function setToolbar() {
        $toolbar = Toolbar::getInstance();
        if ($this->canDo->get('core.edit.state') || $this->canDo->get('core.delete.data') || $this->canDo->get('core.edit.data') || $this->canDo->get('core.edit.own.data')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);
            $childBar = $dropdown->getChildToolbar();
            if ($this->canDo->get('core.edit.state')) {
                $childBar->publish("$this->controllerName.publish")->listCheck(true);
                $childBar->unpublish("$this->controllerName.unpublish")->listCheck(true);
                $childBar->checkin("$this->controllerName.checkin")->listCheck(true);
            }

            if ($this->canDo->get('core.delete.data')) {
                $childBar->delete("$this->controllerName.delete")
                    ->text('COM_VISFORMS_DELETE')
                    ->message('COM_VISFORMS_DELETE_DATASET_TRUE')
                    ->listCheck(true);
            }
            if ($this->canDo->get('core.edit.data') || $this->canDo->get('core.edit.own.data')) {
                $childBar->edit('visdata.edit')
                    ->listCheck(true);
                $childBar->standardButton('restore', 'COM_VISFORMS_RESET_DATA', "$this->controllerName.reset")
                    ->icon('icon-undo')
                    ->listCheck(true);
            }
            if ($this->canDo->get('core.export.data')) {
                $toolbar->appendButton('Standard', 'drawer', 'COM_VISFORMS_EXPORT', "$this->controllerName.export", false);
            }
            $toolbar->appendButton('Standard', 'file-2', 'COM_VISFORMS_BACK_TO_FORM', "$this->controllerName.form", false);
        }
    }

	protected function getSaveResultState() {
    	return true;
	}
}
