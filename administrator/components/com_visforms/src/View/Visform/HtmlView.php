<?php
/**
 * Visform view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Visform;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Visolutions\Component\Visforms\Administrator\View\ItemViewBase;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;

class HtmlView extends ItemViewBase
{
    function __construct($config = array()) {
        parent::__construct($config);
        $this->editViewName = "visform";
        $this->controllerName = 'visform';
        $this->canSaveToCopy = true;
    }

    protected function setMembers() { }

    protected function getTitle() {
        $text = $this->isNew ? Text::_( 'COM_VISFORMS_FORM_NEW' ) : Text::_( 'COM_VISFORMS_FORM_EDIT' );
        return Text::_('COM_VISFORMS_FORM') . \VisformsHelper::appendTitleAppendixFormat($text);
    }

    protected function setToolbar() {
        $toolbar = Toolbar::getInstance('toolbar');
    	// Todo remove quick start help step or complete it
	    //$layout = new JLayoutFile('div.quickstart_help_element');
	    //$text =  Text::_('COM_VISFORMS_FIELDS') .  ' ' . $layout->render(array('step' => 3, 'tag' => 'span'));

        if (!$this->checkedOut) {
	        ToolbarHelper::custom("$this->controllerName.fields",'list','list','COM_VISFORMS_FIELDS',false) ;
        	//JToolbarHelper::custom("$this->controllerName.fields",'forms','forms',$text,false) ;
        }

        if ($this->form->getValue('saveresult') == '1') {
            ToolbarHelper::custom("$this->controllerName.datas",'database','database','COM_VISFORMS_DATAS',false) ;
        }

	    if (\VisformsAEF::checkAEF(\VisformsAEF::$subscription)) {
		    ToolbarHelper::custom("$this->controllerName.pdfs",'file-2','file-2','COM_VISFORMS_PDFS',false) ;
	    }
    }

    protected function getFIdUrlQueryName() { return 'id'; }
}
