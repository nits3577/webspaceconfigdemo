<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData) && isset($displayData['form']) && isset($displayData['id']) && isset($displayData['onclick'])) {
	$form = $displayData['form'];
	$id = $displayData['id'];
	$onclick = $displayData['onclick'];

	$extension = (!empty($displayData['extension'])) ? $displayData['extension'] : 'component';
	$htmlTag = (!empty($displayData['htmlTag'])) ? $displayData['htmlTag'] : 'td';
	$class = (!empty($displayData['class'])) ? ' class="' . $displayData['class'] . '"' : '';
	$pparams = (!empty($displayData['pparams'])) ? $displayData['pparams'] : array();
	$viewType = (!empty($displayData['viewType'])) ? $displayData['viewType'] : 'column';
	$btnclass = (!empty($displayData['btnclass'])) ? $displayData['btnclass'] . ' visTooltip': 'visTooltip';
	$displayPdfButton = false;
	$displayCheckbox = false;
	$name='displaypdfexportbutton';

	switch ($extension) {
		case 'vfdataview' :
			$name .= '_plg';
			break;
		default:
			if ($viewType == 'column') {
				$name .= '_list';
			}
			if ($viewType == 'row') {
				$name .= '_detail';
			}
			break;
	}
	if (!empty($form->$name) && !empty($form->singleRecordPdfTemplate)) {
		$displayPdfButton = true;
	}
	else if (!empty($form->$name) && !empty($form->listPdfTemplate)) {
		$displayCheckbox = true;
	}

	if (!empty($displayPdfButton)) {
		HTMLHelper::_('visforms.addListTaskScript');
		echo '<' . $htmlTag . $class . '>';

		echo '<a class="' . $btnclass . '" title="" onclick="' . $onclick . '" href="javascript:void(0);" title="' . Text::_('COM_VISFORMS_DOWNLOAD_PDF') . '" data-bs-toggle="tooltip">';
		echo '<span class="icon-file"></span>';
		echo '</a>';
		echo '</' . $htmlTag . '>';
	}
}