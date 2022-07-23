<?php
/**
 * @author      Aicha Vack
 * @package     Joomla.Site
 * @subpackage  com_visforms
 * @link        https://www.vi-solutions.de
 * @copyright   2014 Copyright (C) vi-solutions, Inc. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Visolutions\Component\Visforms\Administrator\Field;

// No direct access to this file
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\HiddenField;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.framework');
// used in option to provide drag & drop functionality if the browser supports "sortable". If not, native js is used to.
HTMLHelper::_('script', 'media/com_visforms/js/jquery.ui.core.js', array('version' => 'auto', 'relative' => false, 'detectBrowser' => false, 'detectDebug' => false));
HTMLHelper::_('script', 'media/com_visforms/js/jquery.ui.sortable.js', array('version' => 'auto', 'relative' => false, 'detectBrowser' => false, 'detectDebug' => false));

class ItemlistcreatorField extends HiddenField
{
	protected $type='itemlistcreator';
	protected $hasSub;
    
	protected function getInput() {
		$this->hasSub =  (!empty(\VisformsAEF::checkAEF(\VisformsAEF::$subscription))) ? true : false;
        $doc = Factory::getApplication()->getDocument();
        $doc->addScript(Uri::root(true).'/administrator/components/com_visforms/js/itemlistcreator.js');
		$texts =  "{texts : {txtMoveUp: '" . addslashes(Text::_( 'COM_VISFORMS_ITEMLISTCREATOR_MOVE_UP' )). "',".
				"txtMoveDown: '" . addslashes(Text::_( 'COM_VISFORMS_ITEMLISTCREATOR_MOVE_DOWN' )). "',".
                "txtMoveDragDrop: '" . addslashes(Text::_( 'COM_VISFORMS_ITEMLISTCREATOR_MOVE_DRAG_AND_DROP' )). "',".
				"txtDelete: '" . addslashes(Text::_( 'COM_VISFORMS_DEL' )). "',".
                "txtCreateItem: '" . addslashes(Text::_( 'COM_VISFORMS_ITEMLISTCREATOR_CREATE_NEW_ITEM' )). "',".
                "txtAlertRequired: '" . addslashes(Text::_( 'COM_VISFORMS_ITEMLISTCREATOR_REQUIRED_LABEL_VALUE' )). "',".
                "txtItemsImported : '". addslashes(Text::_( 'COM_VISFORMS_IMPORT_OPTION_SUCCESS' )). "',".
                "txtReaderError : '" . addslashes(Text::_( 'COM_VISFORMS_INVALID_IMPORT_OPTIONS_FORMAT' )). "',".
                "txtNoDataToImport: '" . addslashes(Text::_( 'COM_VISFORMS_NO_DATA_TO_IMPORT' )). "',".
				"txtDescr: '" . addslashes(Text::_( 'COM_VISFORMS_SELECT_VALUE_DESC' )). "'".
			"},".
            " params: {fieldName : '" . $this->fieldname . "',".
                "idPrefix : 'jform_defaultvalue_',".
                "dbFieldExt : '_list_hidden',".
                "importField : '_importOptions', ".
                "importSeparator : '_importSeparator', ".
                "hdnMFlds : {".
					"listitemid:{'fname' : 'listitemid', 'ftype': 'hidden', 'frequired': false, 'fvalue' : ''},".
                    "listitemvalue:{'fname' : 'listitemvalue', 'ftype': 'text', 'frequired': true, 'fvalue' : ''},".
                    "listitemlabel:{'fname' : 'listitemlabel', 'ftype': 'text', 'frequired': true, 'fvalue' : ''},".
                    "listitemischecked:{'fname' : 'listitemischecked', 'ftype': 'checkbox', 'frequired': false, 'fvalue' : '1'},".
					"listitemredirecturl:{'fname' : 'listitemredirecturl', 'ftype': 'text', 'frequired': false, 'fvalue' : ''},".
					"listitemmail:{'fname' : 'listitemmail', 'ftype': 'text', 'frequired': false, 'fvalue' : ''},".
					"listitemmailcc:{'fname' : 'listitemmailcc', 'ftype': 'text', 'frequired': false, 'fvalue' : ''},".
					"listitemmailbcc:{'fname' : 'listitemmailbcc', 'ftype': 'text', 'frequired': false, 'fvalue' : ''},".
					"listitemlabelclass:{'fname' : 'listitemlabelclass', 'ftype': 'text', 'frequired': false, 'fvalue' : ''},".
                "},".
            //add ctype for custom use, where ctype is not field name based
			"header: '". $this->createListHeader()."',".
			"items: '". $this->createExistingListItems()."',".
			"rowTemplate: '". $this->createEmptyRowTemplate()."',".
            "}".
            "}";
		$script = 'jQuery(document).ready(function() {jQuery("#jform_defaultvalue_'.$this->fieldname.'").createVisformsOptionCreator(' . $texts . ')});';
		$doc->addScriptDeclaration($script);
		
        $hiddenInput = parent::getInput();
		$html = $hiddenInput;
		
		return $html;
	}

	protected function createEmptyRowTemplate() {
		return '<tr class="liItem">' .
			'<td class="hiddenNotSortable"><span class="itemMove"><i class="icon-menu" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_DRAG_AND_DROP" ).'"></i></span></td>' .
			'<td class="hiddenSortable"><a class="itemUp"><i class="icon-arrow-up-3" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_UP" ).'"></i></a></td>' .
			'<td class="hiddenSortable"><a class="itemDown"><i class="icon-arrow-down-3" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_DOWN" ).'"></i></a></td>' .
			'<td><input type="hidden" class="itemlist listitemid" value="" /></td>' .
			'<td><input type="text" class="itemlist listitemvalue form-control-sm focus" value="" required="required" /></td>' .
			'<td><input type="text" class="itemlist listitemlabel form-control-sm" value="" required="required" /></td>' .
			'<td><input type="checkbox" class="itemlist listitemischecked" value="1"/></td>' .
			'<td><input type="text" class="itemlist listitemredirecturl form-control-sm" value=""'.((!$this->hasSub) ? " disabled=\"disabled\"" : "").' /></td>' .
			'<td><input type="text" class="itemlist listitemmail form-control-sm" value=""'.((!$this->hasSub) ? " disabled=\"disabled\"" : "").' /></td>' .
			'<td><input type="text" class="itemlist listitemmailcc form-control-sm" value=""'.((!$this->hasSub) ? " disabled=\"disabled\"" : "").' /></td>' .
			'<td><input type="text" class="itemlist listitemmailbcc form-control-sm" value=""'.((!$this->hasSub) ? " disabled=\"disabled\"" : "").' /></td>' .
			'<td><input type="text" class="itemlist listitemlabelclass form-control-sm" value=""'.((!$this->hasSub) ? " disabled=\"disabled\"" : "").' /></td>'.
			'<td><a class="itemRemove" href="#">'. Text::_( "COM_VISFORMS_DEL" ).'</a></td>' .
			'</tr>';
	}

	protected function createExistingListItems() {
		$data = $this->form->getValue($this->fieldname, 'defaultvalue');
		$html = array();
		if (!empty($data)) {
			$options = HTMLHelper::_('visformsselect.extractHiddenList', $data);
			if (is_array($options)) {
				foreach ($options as $option) {
					$checked = $option['selected'] ? 'checked="checked"' : '';
					$html[] = '<tr class="liItem">' .
						'<td class="hiddenNotSortable"><span class="itemMove"><i class="icon-menu" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_DRAG_AND_DROP" ).'"></i></span></td>' .
						'<td class="hiddenSortable"><a class="itemUp"><i class="icon-arrow-up-3" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_UP" ).'"></i></a></td>' .
						'<td class="hiddenSortable"><a class="itemDown"><i class="icon-arrow-down-3" title="'. Text::_( "COM_VISFORMS_ITEMLISTCREATOR_MOVE_DOWN" ).'"></i></a></td>' .
						'<td><input type="hidden" class="itemlist listitemid" value="'. $option['id'] .'" /></td>' .
						'<td><input type="text" class="itemlist listitemvalue form-control-sm" value="'. $option['value'] .'" required="required" /></td>' .
						'<td><input type="text" class="itemlist listitemlabel form-control-sm" value="'. $option['label'].'" required="required" /></td>' .
						'<td><input type="checkbox" class="itemlist listitemischecked" value="1"'.$checked.'/></td>' .
						'<td><input type="text" class="itemlist listitemredirecturl form-control-sm" value="'. (($this->hasSub) ? $option['redirecturl'] : "").'"'.((!$this->hasSub) ? " disabled=\"disabled\"":"").' /></td>' .
						'<td><input type="text" class="itemlist listitemmail form-control-sm" value="'. (($this->hasSub) ? $option['mail'] : "").'"'.((!$this->hasSub) ? " disabled=\"disabled\"":"").' /></td>' .
						'<td><input type="text" class="itemlist listitemmailcc form-control-sm" value="'. (($this->hasSub) ? $option['mailcc'] : "").'"'.((!$this->hasSub) ? " disabled=\"disabled\"":"").' /></td>' .
						'<td><input type="text" class="itemlist listitemmailbcc form-control-sm" value="'. (($this->hasSub) ? $option['mailbcc'] : "").'"'.((!$this->hasSub) ? " disabled=\"disabled\"":"").' /></td>' .
						'<td><input type="text" class="itemlist listitemlabelclass form-control-sm" value="'. (($this->hasSub) ? $option['labelclass'] : "").'"'.((!$this->hasSub) ? " disabled=\"disabled\"":"").' /></td>'.
						'<td><a class="itemRemove" href="#">'. Text::_( "COM_VISFORMS_DEL" ).'</a></td>' .
						'</tr>';
				}
			}
		}
		return addslashes(implode('', $html));
	}

	protected function createListHeader() {
		return addslashes('<tr class="liItemHeader">' .
            '<th class="itemMoveHeader hiddenNotSortable"></th>' .
            '<th class="itemUpHeader hiddenSortable"></th>' .
            '<th class="itemDownHeader hiddenSortable"></th>' .
			'<th class="itemIdHeader"></th>' .
            '<th class="itemlistheader">'. Text::_( "COM_VISFORMS_VALUE" ).' *</th>' .
            '<th class="itemlistheader">'. Text::_( "COM_VISFORMS_LABEL" ).' *</th>' .
            '<th class="itemlistheader">'. Text::_( "COM_VISFORMS_DEFAULT" ).'</th>' .
			'<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_REDIRECTURL")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_REDRECTS_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_REDIRECTURL" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>' .
			'<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAIL")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAIL_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_CUSTOM_MAIL" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>' .
			'<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAILCC")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAIL_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_CUSTOM_MAILCC" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>' .
			'<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAILBCC")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_CUSTOM_MAIL_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_CUSTOM_MAILBCC" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>' .
			(($this->fieldname != 'f_select_list_hidden') ? '<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_LABEL_CSS_CLASS")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_LABEL_CSS_CLASS_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_LABEL_CSS_CLASS" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>' : '<th class="itemlistheader hasPopover" title="'.htmlspecialchars(Text::_("COM_VISFORMS_OPTION_CSS_CLASS")).'" data-content="'.htmlspecialchars(Text::_("COM_VISFORMS_OPTION_CSS_CLASS_DESC")).'" data-placement="top">'. Text::_( "COM_VISFORMS_OPTION_CSS_CLASS" ). ((!$this->hasSub) ? Text::_( "COM_VISFORMS_SUBSCRIPTION_ONLY" ) : "") .'</th>') .
            '<th class="itemRemoveHeader">'. Text::_( "COM_VISFORMS_DEL" ).'</th>' .
			'</tr>');
	}
	
}