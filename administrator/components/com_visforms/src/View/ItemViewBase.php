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

namespace Visolutions\Component\Visforms\Administrator\View;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;

class ItemViewBase extends BaseHtmlView
{
	// framework
	public $app;
	public $doc;
	public $input;
	public $user;
	public $userId;
	// component names
	public $baseName = 'visforms';
	public $componentName = 'com_visforms';
	public $editViewName;
	public $controllerName;
	public $baseUrl;
	// payload
	public $form;
	public $item;
	public $id;
	public $fid;
	public $canDo;
	public $canDoPostFix;
	public $isNew;
	public $checkedOut;
	public $cssName;
	public $hideButtons;
	public $badgesHidden;
	public $canSaveToCopy;

	function __construct($config = array()) {
		parent::__construct($config);
		// framework
		$this->app = Factory::getApplication();
		//$this->doc = $this->app->getDocument();
		$this->input = $this->app->input;
		$this->user = $this->app->getIdentity();
		$this->userId = $this->user->get('id');
		// component names
		$this->baseUrl = "index.php?option=$this->componentName";
		// defaults
		$this->id = 0;
		$this->canDo = \VisformsHelper::getActions();
		$this->isNew = true;
		$this->checkedOut = false;
		// completely individual toolbar (viscreator)
		$this->hideButtons = false;
		$component = ComponentHelper::getComponent('com_visforms');
		$this->badgesHidden = $component->params->get('hideHelpBadges', '');
		// Does View support a SaveAndCopyTask? (not in edit pdf and data)
		$this->canSaveToCopy = false;
        HTMLHelper::_('jquery.framework');
	}

	protected function initialize() {
		// payload
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->fid = $this->getFIdFromInput();
		$this->canDoPostFix = '';
		// item may not be available
		if (isset($this->item)) {
			if (isset($this->item->id)) {
				$this->id = (int) $this->item->id;
				$this->canDo = \VisformsHelper::getActions($this->item->id);
				$this->isNew = ($this->item->id == 0);
			}
			if (isset($this->item->checkedOut)) {
				$this->checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $this->userId);
			}
		}
		// derived class specific member initialization
		$this->setMembers();
	}

	public function display($tpl = null) {
		$this->initialize();
		\VisformsHelper::showTitleWithPreFix($this->getTitle());
        $toolbar = Toolbar::getInstance('toolbar');
		if ($this->hideButtons) {
			$this->setToolbar();
		}
		else if ($this->isNew) {
			if ($this->canDo->get('core.create')) {
				// ToDo show quick start help step in apply button, remove or complete
				//$layout = new JLayoutFile('div.quickstart_help_element');
				//$text =  $layout->render(array('step' => 2, 'tag' => 'span'));
				//ToolbarHelper::apply("$this->controllerName.apply", Text::_('JTOOLBAR_APPLY') . ' ' . $text);
                $toolbar->apply("$this->controllerName.apply");
                $saveGroup = $toolbar->dropdownButton('save-group');
                $saveGroup->configure(
                    function (Toolbar $childBar)
                    {

                        $childBar->save("$this->controllerName.save");
                        $childBar->save2new("$this->controllerName.save2new");
                    }
                );
			}
            $toolbar->cancel("$this->controllerName.cancel", 'JTOOLBAR_CLOSE');
		}
		else {
            if (!$this->checkedOut) {
                $toolbar->apply("$this->controllerName.apply");
            }
            $saveGroup = $toolbar->dropdownButton('save-group');
			// Can't save the record if it's checked out.
			if (!$this->checkedOut) {
				if ($this->canDo->get("core.edit$this->canDoPostFix") || ($this->canDo->get("core.edit.own$this->canDoPostFix") && $this->item->created_by == $this->userId)) {

                    $saveGroup->configure(
                        function (Toolbar $childBar)
                        {
                            $childBar->save("$this->controllerName.save");
                            $this->setToolbarNotCheckedOut($childBar);
                        }
                    );
				}
			}
			if ($this->canSaveToCopy) {
                $saveGroup->configure(
                    function (Toolbar $childBar) {
                        $childBar->save2copy("$this->controllerName.save2copy");
                    });
            }
			$this->setToolbar();
			ToolbarHelper::cancel("$this->controllerName.cancel", 'COM_VISFORMS_CLOSE');
		}
		$this->addHeaderDeclarations();
		\VisformsHelper::addCommonViewStyleCss();
		$this->setHideMainMenu();
		parent::display($tpl);
	}

	// overwrites: template methods
	protected function setMembers() {
	}

	protected function getTitle() {
	}

	protected function setToolbar() {
	}

	protected function addHeaderDeclarations() {
	}

	// overwrites: internal
	protected function getFIdUrlQueryName() {
		return 'fid';
	}

	protected function setToolbarNotCheckedOut($childBar) {
		if ($this->canDo->get('core.create')) {
			//ToolbarHelper::save2new("$this->controllerName.save2new");
            $childBar->save2new("$this->controllerName.save2new");
		}
	}

	protected function setHideMainMenu() {
		Factory::getApplication()->input->set('hidemainmenu', 1);
	}

	// implementation
	private function getFIdFromInput() {
		$name = $this->getFIdUrlQueryName();
		return $this->input->getInt($name, -1);
	}

	// stored for later use
	protected function addHideStepBadgesButtons() {
		if ($this->badgesHidden) {
			ToolbarHelper::custom("$this->controllerName.showStepBadges", 'help', 'help', 'COM_SHOW_STEP_BADGES', false);
		}
		else {
			ToolbarHelper::custom("$this->controllerName.hideStepBadges", 'help', 'help', 'COM_HIDE_STEP_BADGES', false);
		}
	}
}