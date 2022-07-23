<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Visolutions\Component\Visforms\Administrator\Field\Modal;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

class VisdatadetailField extends FormField
{
	protected $type = 'Modal_Visdatadetail';

	protected function getInput() {
	    HTMLHelper::_('jquery.framework');
		$allowClear     = ((string) $this->element['clear'] != 'false');
		$allowSelect    = ((string) $this->element['select'] != 'false');
		// Load the modal behavior script.

		// Build the script.
		$script = array();
		$script[] = '	function jSelectVisdatadetail_'.$this->id.'(id, title, object) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = title;';
		$script[] = '		jQuery("#modalVisform' . $this->id . '").modal("hide");';
		$script[] = '	}';
        // Add the script to the document head.
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineScript(implode("\n", $script));
        // Add the modal field script to the document head.
        $wa->useScript('field.modal-fields');

		// Add the script to the document head.
		//Factory::getApplication()->getDocument()->addScriptDeclaration(implode("\n", $script));

		// modal select for data detail id
		// custom implementation because form id must be set in link according to form selection
		// hide option, as long as on form is selected
		$script = "
		
		jQuery(document).ready(function($) {
		    
			var fid = $('#jform_request_id_id').val();
			var formselected = (fid) ? true : false;
			if (!formselected) {
				$('#jform_request_cid_id').parents('.control-group').hide();
			}
			var myModalEl = document.getElementById('modalVisformjform_request_cid');
                myModalEl.addEventListener('show.bs.modal', function (event) {
                    var fid = $('#jform_request_id_id').val();
                    $('body').addClass('modal-open');
                   var modalBody = $(this).find('.modal-body');
                   var link = $('#modalVisformjform_request_cid').attr('data-url');
                   var fid = $('#jform_request_id_id').val();
                   link += '&fid=' + fid;
                   modalBody.find('iframe').remove();
                   modalBody.prepend('<iframe class=\"iframe\" src=\"'+link+'\" name=\"".Text::_('COM_VISFORMS_CHOOSE_RECORD_SET')."\" height=\"300px\" width=\"800px\"></iframe>');
            });
		});
		";

        $wa->addInlineScript($script);

		// Setup variables for display.
		//$html	= array();
		$link	= 'index.php?option=com_visforms&amp;view=visdatas&amp;layout=modal&amp;tmpl=component&amp;function=jSelectVisdatadetail_'.$this->id;
		$modalTitle = $this->value;
		if (empty($modalTitle)) {
			$modalTitle = Text::_('COM_VISFORMS_CHOOSE_RECORD_SET');
		}
		$modalTitle = htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8');
		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		}
		else {
			$value = (int)$this->value;
		}
		$modalId = 'modalVisform'. $this->id;
		$html  = '';

		if ($allowSelect || $allowClear)
		{
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $modalTitle . '" readonly size="35">';

		// Select article button
		if ($allowSelect)
		{
			$html .= '<button'
				. ' class="btn btn-primary"'
				. ' id="' . $this->id . '_select"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#' . $modalId . '">'
				. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
				. '</button>';
		}

		// Clear article button
		if ($allowClear)
		{
			$html .= '<button'
				. ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
				. ' id="' . $this->id . '_clear"'
				. ' type="button"'
				. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
				. '<span class="icon-remove" aria-hidden="true"></span> ' . Text::_('JCLEAR')
				. '</button>';
		}

		if ($allowSelect || $allowClear)
		{
			$html .= '</span>';
		}

		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			$modalId,
			array(
				'title'       => Text::_('COM_VISFORMS_CHOOSE_RECORD_SET'),
				'url'         => $link,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 70,
				'modalWidth'  => 80,
				'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
			)
		);

		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_VISFORMS_CHOOSE_RECORD_SET', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '">';

		return $html;
	}
}
