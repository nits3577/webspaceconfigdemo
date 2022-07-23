<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Visolutions\Component\Visforms\Administrator\Field\Modal;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

class VisdataField extends FormField
{
	protected $type = 'Modal_Visdata';

	protected function getInput() {
        HTMLHelper::_('jquery.framework');
		$allowClear     = ((string) $this->element['clear'] != 'false');
		$allowSelect    = ((string) $this->element['select'] != 'false');
		// set id of selected record set (from modal window) in parent view
		// options in some selects depend on which form was selected for the data view
		// reload options depending on form selection
		// show/hide data detail selection depending on wether a form is selected or not
		$script = array();
		$script[] = '	function jSelectVisdata_'.$this->id.'(id, title, object) {';
		$script[] = '		document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '		document.getElementById("'.$this->id.'_name").value = title;';
		$script[] = '		jQuery("#modalVisform' . $this->id . '").modal("hide");';
		$script[] = '       setFormId(id)';
		$script[] = '	};
		function setFormId(fid) {
			jQuery.ajax({
				url: "index.php?option=com_visforms&task=visform.getSortOrderFieldOptions&fid=" + fid + "&'.Session::getFormToken().'=1",
				dataType: "html"
			}).done(function(data) {
				jQuery("#jform_params_sortorder option").each(function() {jQuery(this).remove()});
				jQuery("#jform_params_sortorder").append(data);
			});
			jQuery("#jform_request_cid_id").val("");
			jQuery("#jform_request_cid_name").val("Datensatz wählen");
			var id = document.getElementById("'.$this->id.'_id").value; //jQuery("#'.$this->id.'_id").val(); 
			if (id) {
				jQuery("#jform_request_cid_id").parents(".control-group").show();
			}
			else {
				jQuery("#jform_request_cid_id").parents(".control-group").hide();
			}
		};';

        // Add the script to the document head.
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineScript(implode("\n", $script));
        // Add the modal field script to the document head.
        $wa->useScript('field.modal-fields');

		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_visforms&amp;view=visforms&amp;layout=modal_data&amp;tmpl=component&amp;function=jSelectVisdata_'.$this->id;

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('title'))
			->from($db->quoteName('#__visforms'))
			->where($db->qn('id') . ' = ' . (int) $this->value);
		try {
			$db->setQuery($query);
			$modalTitle = $db->loadResult();
		}
		catch (\RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (empty($modalTitle)) {
			$modalTitle = Text::_('COM_VISFORMS_CHOOSE_FORM');
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
		if ($allowSelect || $allowClear)
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
				'title'       => Text::_('COM_VISFORMS_CHOOSE_FORM'),
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
			. '" data-text="' . htmlspecialchars(Text::_('COM_VISFORMS_CHOOSE_FORM', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '">';

		return $html;

	}
}
