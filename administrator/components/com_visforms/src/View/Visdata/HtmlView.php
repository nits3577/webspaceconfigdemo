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

namespace Visolutions\Component\Visforms\Administrator\View\Visdata;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Visolutions\Component\Visforms\Administrator\Model\VisfieldsModel;
use Visolutions\Component\Visforms\Administrator\View\ItemViewBase;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * visforms View
 *
 * @package    Joomla.Administratoar
 * @subpackage com_visforms
 * @since      Joomla 1.6
 */
class HtmlView extends ItemViewBase
{
	public $fields;

    function __construct($config = array()) {
        parent::__construct($config);
        $this->editViewName = "visdata";
        $this->controllerName = 'visdata';
    }

    protected function setMembers() {
        $this->fields = $this->get('Datafields');
        $this->canDo = \VisformsHelper::getActions($this->fid);
        $this->canDoPostFix = '.data';
    }

    protected function getTitle() {
        $model = new VisfieldsModel(); //JModelLegacy::getInstance('Visfields', 'VisformsModel');
        $title = $model->getFormtitle();
        if( !empty($title)) {
            $title = ' ' . Text::_('COM_VISFORMS_OF_FORM_PLAIN') . ' ' . $title;
        }
        $text = Text::_('COM_VISFORMS_DATA_EDIT');
        return Text::_('COM_VISFORMS_VISFORM_DATA_RECORD_SET' ) . $title . \VisformsHelper::appendTitleAppendixFormat($text);
    }

    protected function setToolbarNotCheckedOut($childBar) {
        if ($this->item->ismfd) {
            ToolbarHelper::custom("$this->controllerName.reset",'undo','undo','COM_VISFORMS_RESET_DATA', false) ;
        }
    }
}
