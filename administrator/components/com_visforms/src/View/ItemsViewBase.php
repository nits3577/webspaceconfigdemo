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
 * @since        Joomla 3.0.0
 */
namespace Visolutions\Component\Visforms\Administrator\View;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\HTMLHelper;
use Visolutions\Component\Visforms\Administrator\Model\VisfieldsModel;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

class ItemsViewBase extends BaseHtmlView
{
    // framework
    public $app;
    public $doc;
    public $input;
    public $user;
    public $userId;
    public $listOrdering;
    public $listDirection;
    // component names
    public $baseName        = 'visforms';
    public $componentName   = 'com_visforms';
    public $authoriseName   = 'com_visforms.visform';
    public $viewName;
    public $editViewName;
    public $baseUrl;
    // payload
    public $fid;
    public $items;
    public $state;
    public $filterForm;
    public $activeFilters;
    public $pagination;
    public $sidebar;
    public $canDo;

    function __construct($config = array()) {
        parent::__construct($config);
        // framework
        $this->app          = Factory::getApplication();
        $this->doc          = $this->app->getDocument();
        $this->input        = $this->app->input;
        $this->user		    = $this->app->getIdentity();
        $this->userId		= $this->user->get('id');
        // component names
        $this->baseUrl      = "index.php?option=$this->componentName";
        // payload
        $this->fid          = $this->getFIdFromInput();
    }

    public function display($tpl = null) {
        $this->setMembers();
        // get data from the model
        $this->items         = $this->get('Items');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->pagination    = $this->get('Pagination');

        $this->listOrdering	 = $this->escape($this->state->get('list.ordering'));
        $this->listDirection = $this->escape($this->state->get('list.direction'));

        // we don't need toolbar in the modal window

        if (($this->getLayout() !== 'modal') && ($this->getLayout() !== 'modal_data')) {
            \VisformsHelper::addSubmenu($this->viewName, $this->fid, $this->getSaveResultState());
            $this->sidebar = HTMLHelper::_('sidebar.render');
            \VisformsHelper::showTitleWithPreFix($this->getFormAddTitle());
            $this->setToolbar();
        }

        $this->addHeaderDeclarations();
        \VisformsHelper::addCommonViewStyleCss();

        parent::display($tpl);
    }

    // overwrites: template methods

    protected function setMembers() { }

    protected function getTitle() { }

    protected function setToolbar() { }

    protected function addHeaderDeclarations() { }

    protected function getSaveResultState() {
    	return false;
    }

    // overwrites: internal

    protected function getFIdUrlQueryName() {
        return 'fid';
    }

    // implementation

    private function getFIdFromInput() {
        $name = $this->getFIdUrlQueryName();
        return $this->input->getInt($name, -1);
    }

    public function getSortHeader($text, $field) {
        return HTMLHelper::_('searchtools.sort', $text, $field, $this->listDirection, $this->listOrdering);
    }

    protected function getFormAddTitle() {
    	if('visfields' == $this->viewName) {
		    $formTitle = $this->get('Formtitle');
	    }
	    else {
    		$fieldsModel = new VisfieldsModel();//JModelLegacy::getInstance('Visfields', 'VisformsModel');
		    $formTitle = $fieldsModel->getFormtitle();
	    }
	    if( !empty($formTitle)) {
		    $formTitle = ' ' . Text::_('COM_VISFORMS_OF_FORM_PLAIN') . ' ' . $formTitle;
	    }
	    return $this->getTitle() . $formTitle;
    }

    // used by visfields and vispdfs views
    protected function setItemsToolbar($deleteMessage = '') {
        // local shortcuts
        $viewName     = $this->viewName;
        $editViewName = $this->editViewName;
        $toolbar = Toolbar::getInstance('toolbar');
        if ($this->canDo->get('core.create')) {
            $toolbar->addNew($editViewName.'.add');
        }
        if ($this->canDo->get('core.edit.state')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();
            $childBar->publish($viewName.'.publish')->listCheck(true);
            $childBar->unpublish($viewName.'.unpublish')->listCheck(true);
            $childBar->checkin($viewName.'.checkin')->listCheck(true);
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
            $childBar->delete($viewName.'.delete')
                ->text('COM_VISFORMS_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }
        // navigation to form is done via fields model functions
        $toolbar->appendButton('Standard', 'file-2', 'COM_VISFORMS_BACK_TO_FORM', 'visfields.form', false);

    }
}