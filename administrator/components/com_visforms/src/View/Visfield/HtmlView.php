<?php
/**
 * Visfield view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Visfield;

defined('_JEXEC') or die( 'Restricted access' );

use Visolutions\Component\Visforms\Administrator\Model\VisfieldsModel;
use Visolutions\Component\Visforms\Administrator\View\ItemViewBase;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class HtmlView extends ItemViewBase
{
    function __construct($config = array()) {
        parent::__construct($config);
        $this->editViewName = "visfield";
        $this->controllerName = 'visfield';
        $this->canSaveToCopy = true;
    }

	protected function setMembers() {
        $this->canDo = \VisformsHelper::getActions($this->item->fid, $this->item->id);
        $data = $this->form->getData();
        $data->set('restrictions', $this->item->restrictions);
    }

    protected function getTitle() {
        $model = new VisfieldsModel(); //JModelLegacy::getInstance('Visfields', 'VisformsModel');
        $title = Text::_( 'COM_VISFORMS_FIELD' ) . ' ' . Text::_('COM_VISFORMS_OF_FORM_PLAIN') . ' ' . $model->getFormtitle();
        $text = $this->isNew ? Text::_( 'COM_VISFORMS_FIELD_NEW' ) : Text::_( 'COM_VISFORMS_FIELD_EDIT' );
        return $title . \VisformsHelper::appendTitleAppendixFormat($text);
    }

    protected function addHeaderDeclarations() {
        $this->document->addScript(Uri::root(true).'/administrator/components/com_visforms/js/visforms.js');
        $this->document->addScript(Uri::root(true).'/administrator/components/com_visforms/js/jquery.csv.min.js');
    }
}
