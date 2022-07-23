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

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
$canEdit =  ($this->canDo->get('core.edit.data') && $hasSub);
$canEditOwn =  ($this->canDo->get('core.edit.own.data') && $hasSub);
$canPublish = ($this->canDo->get('core.edit.data.state') && $hasSub);
$canPublishOwn = ($this->canDo->get('core.edit.own.data.state') && $hasSub);
$canCreatePdf = ($this->canDo->get('core.create.pdf'));
$canCreateOwnPdf = ($this->canDo->get('core.create.own.pdf'));
$canExportCsv = ($this->canDo->get('core.export.data.frontend') && $hasSub && !empty($this->form->display_csv_export_button) && !empty($this->items));
$layout = $this->getLayout();
$userId = Factory::getApplication()->getIdentity()->get('id');
$redirectUri = '&return=' . HTMLHelper::_('visforms.base64_url_encode', Uri::getInstance()->toString());;
$i = 0;


 // Download list pdf
if (($canCreatePdf || $canCreateOwnPdf) && (!empty($this->form->displaypdfexportbutton_list) && !empty($this->form->listPdfTemplate))) {
    HTMLHelper::_('behavior.core');
    echo '<a class="btn" href="javascript:void(0);" onclick="var f = document.getElementById(\''.$this->uniqueContext.'adminForm\'); Joomla.submitform(\'visformsdata.renderPdfList\', f); f.task.value=\'\';">'.Text::_('COM_VISFORMS_DOWNLOAD_PDF').'</a>';
}
if (($layout === 'dataeditlist' || $layout === 'data') && $canExportCsv) {
	HTMLHelper::_('behavior.core');
	echo '<a class="btn" href="javascript:void(0);" onclick="var f = document.getElementById(\''.$this->uniqueContext.'adminForm\'); Joomla.submitform(\'visformsdata.exportCsv\', f); f.task.value=\'\';">'.Text::_('COM_VISFORMS_EXPORT_TO_CSV').'</a>';
}
?>

<table class="visdatatabledatavertical visdata visdatatable jlist-table<?php
if (isset($this->menu_params['show_tableborder']) && $this->menu_params['show_tableborder'] == 1) {
	echo " visdatatableborder";
}
if (isset($this->menu_params['viewclass'])) {
	echo $this->menu_params['viewclass'];
} ?>
    "><?php
	if (isset($this->menu_params['show_columnheader']) && $this->menu_params['show_columnheader'] == 1) { ?>
		<thead>
	<tr><?php
		if (!empty($this->form->displaycounter)) {
			echo '<'.$this->labelHtmlTag.'></'.$this->labelHtmlTag.'>';
		}
		echo LayoutHelper::render('visforms.datas.labels.id', array('form' => $this->form, 'label' => 'COM_VISFORMS_ID', 'name' => 'displayid', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-id', 'context' => $this->uniqueContext), null, array('component' => 'com_visforms'));
		if ($layout === 'dataeditlist') {
			if ($canEdit || $canEditOwn) {
				echo '<'.$this->labelHtmlTag.' width="3%" class="'.$this->labelClass.' data-edit"></'.$this->labelHtmlTag.'>';
			}
			if ($canPublish || $canPublishOwn) {
				echo '<'.$this->labelHtmlTag.' class="'.$this->labelClass.' data-publish"></'.$this->labelHtmlTag.'>';
			}
		}
		if (($canCreatePdf || $canCreateOwnPdf) && (!empty($this->form->displaypdfexportbutton_list) && !empty($this->form->singleRecordPdfTemplate))) {
			echo '<'.$this->labelHtmlTag.' width="3%" class="'.$this->labelClass.' data-pdf"></'.$this->labelHtmlTag.'>';
		}
		foreach ($this->fields as $rowField) {
			if (!empty($rowField->useassearchfieldonly)) {
				continue;
			}
			if (isset($rowField->frontdisplay) && ($rowField->frontdisplay == 1 || $rowField->frontdisplay == 2)) {
				echo LayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => $rowField->label, 'dbName' => 'F'. $rowField->id, 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-f' . $rowField->id, 'context' => $this->uniqueContext, 'unSortable' =>  $rowField->unSortable), null, array('component' => 'com_visforms'));
			}
		}
		echo LayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => Text::_('COM_VISFORMS_IP_ADDRESS'), 'name' => 'displayip', 'dbName' => 'ipaddress', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-ip', 'context' => $this->uniqueContext), null, array('component' => 'com_visforms'));
		echo LayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => Text::_('COM_VISFORMS_MODIFIED'), 'name' => 'displayismfd', 'dbName' => 'ismfd', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-mfd', 'context' => $this->uniqueContext), null, array('component' => 'com_visforms'));
		echo LayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => Text::_('COM_VISFORMS_SUBMISSIONDATE'), 'name' => 'displaycreated', 'dbName' => 'created', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-created', 'context' => $this->uniqueContext), null, array('component' => 'com_visforms'));
		echo LayoutHelper::render('visforms.datas.labels.column', array('form' => $this->form, 'label' => Text::_('COM_VISFORMS_MODIFICATION_DATE'), 'name' => 'displaymodifiedat', 'dbName' => 'modified', 'listDirn' => $listDirn, 'listOrder' => $listOrder, 'extension' => $this->extension, 'htmlTag' => $this->labelHtmlTag, 'class' => $this->labelClass . ' data-modifiedat', 'context' => $this->uniqueContext), null, array('component' => 'com_visforms'));?>
	</tr>
		</thead><?php
	}
	foreach ($this->items as $row) {
		$link = Route::_( 'index.php?option=com_visforms&view=visformsdata&layout='.$this->detailLinkLayout.'&id='.$this->id.'&cid='.$row->id.'&Itemid='.$this->itemid ); ?>
		<tr class="sectiontableentry1"><?php
		if (!empty($this->form->displaycounter)) {
			echo '<'.$this->valueHtmlTag.'>'.++$this->displayCounter.'</'.$this->valueHtmlTag.'>';
		}
		echo LayoutHelper::render('visforms.datas.fields.id', array('form' => $this->form, 'data' => $row, 'link' => $link, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-id', 'viewType' => $this->viewType, 'cbIndex' => $i), null, array('component' => 'com_visforms'));
		if ($layout === 'dataeditlist') {
			if ($canEdit || $canEditOwn) {
				$editUrl = Uri::base() . 'index.php?option=com_visforms&view=edit&layout=edit&task=edit.editdata&id=' . (int) $this->form->id . '&cid=' . (int) $row->id . $redirectUri . '&Itemid='.$this->itemid;
				echo '<'.$this->valueHtmlTag.'>';
				if (($canEdit || (isset($row->created_by) && $row->created_by == $userId))
                    // Only display Edit link for unpublish record set, if a user also has the permission to publish the record set
                    && ((!empty($row->published)) || ((empty($row->published)) && ($canPublish || ($canPublishOwn && isset($row->created_by) && $row->created_by == $userId))))) {
					echo '<a class="visToolTip" href="' . $editUrl . '" title="'.Text::_('COM_VISFORMS_EDIT').'" data-bs-toogle="tooltip"><i class="visicon-edit"></i></a>';
				}
				echo '</'.$this->valueHtmlTag.'>';
			}
			if ($canPublish || $canPublishOwn) {
				echo '<'.$this->valueHtmlTag.'>';
                if ($canPublish || ( isset($row->created_by) && $row->created_by == $userId)) {
                    HTMLHelper::_('visforms.addListTaskScript');
                    if ($row->published) { ?>
                    <a class="btn btn-micro active visToolTip" onclick="return vflistItemTask('cb<?php echo $i; ?>','visformsdata.unpublish', '<?php echo $this->uniqueContext; ?>')" href="javascript:void(0);" title="<?php echo Text::_('JLIB_HTML_UNPUBLISH_ITEM'); ?>"  data-bs-toogle="tooltip">
                            <span class="visicon-publish"></span>
                        </a><?php
                    } else { ?>
                    <a class="btn btn-micro active visToolTip" onclick="return vflistItemTask('cb<?php echo $i; ?>','visformsdata.publish', '<?php echo $this->uniqueContext; ?>')" href="javascript:void(0);" title="<?php echo Text::_('JLIB_HTML_PUBLISH_ITEM'); ?>"  data-bs-toogle="tooltip">
                            <span class="visicon-unpublish"></span>
                        </a><?php
                    }
                }
				echo '</'.$this->valueHtmlTag.'>';
			}

		}
		if (($canCreatePdf || $canCreateOwnPdf) && (!empty($this->form->displaypdfexportbutton_list) && !empty($this->form->singleRecordPdfTemplate))) {
            if ($canCreatePdf || ((isset($row->created_by) && $row->created_by == $userId) && $canCreateOwnPdf)) {
                echo LayoutHelper::render('visforms.datas.fields.pdfbutton', array('form' => $this->form, 'id' => $row->id, 'onclick' => 'return vflistItemTask(\'cb'.$i.'\',\'visformsdata.renderPdf\', \''.$this->uniqueContext.'\')', 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-pdf', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
            }
            else {
	            echo '<'.$this->valueHtmlTag.'></'.$this->valueHtmlTag.'>';
            }
		}
		foreach ($this->fields as $rowField) {
			if (!empty($rowField->useassearchfieldonly)) {
				continue;
			}
			if (isset($rowField->frontdisplay) && ($rowField->frontdisplay == 1 || $rowField->frontdisplay == 2)) {
				$prop="F".$rowField->id;
				$texte = (isset($row->$prop)) ? $row->$prop : '';
				echo $texte = LayoutHelper::render('visforms.datas.fields', array('form' => $this->form, 'field' => $rowField, 'data' => $row, 'text' => $texte, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-f' . $rowField->id, 'extension' => $this->extension, 'view' => 'list', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
			}
		}
		echo LayoutHelper::render('visforms.datas.fields.defaultoverhead', array('form' => $this->form, 'text' => $row->ipaddress, 'name' => 'displayip', 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-ip', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
		echo LayoutHelper::render('visforms.datas.fields.ismfd', array('form' => $this->form, 'text' => $row->ismfd, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-mfd', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));
		echo LayoutHelper::render('visforms.datas.fields.created', array('form' => $this->form, 'data' => $row, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-created', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));

		echo LayoutHelper::render('visforms.datas.fields.modifiedat', array('form' => $this->form, 'data' => $row, 'extension' => $this->extension, 'htmlTag' => $this->valueHtmlTag, 'class' => $this->valueClass . ' data-modifiedat', 'viewType' => $this->viewType), null, array('component' => 'com_visforms'));?>

		</tr><?php
		$i++;
	} ?>
</table>