<?php
/**
 * $this->editViewName default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

$subVer = VisformsAEF::getVersion(VisformsAEF::$subscription);
$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);

$saveOrder	= $this->listOrdering == 'a.ordering';
$saveOrderDataDetail = $this->listOrdering == 'a.dataordering' && $hasSub;

if ($saveOrder) {
	$saveOrderingUrl = "$this->baseUrl&task=$this->viewName.saveOrderAjax&tmpl=component&fid=$this->fid";
	HTMLHelper::_('draggablelist.draggable');
}
if ($saveOrderDataDetail) {
	$saveOrderingUrl = "$this->baseUrl&task=$this->viewName.saveOrderAjaxData&tmpl=component&fid=$this->fid";
	HTMLHelper::_('draggablelist.draggable');
}
?>
<form action="<?php echo Route::_("$this->baseUrl&view=$this->viewName&fid=$this->fid"); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row"><?php
		if (!empty( $this->sidebar)) { ?>
            <div id="j-sidebar-container" class="col-md-3 col-xl-2">
			<?php echo $this->sidebar; ?>
            </div><?php } ?>
        <div class="<?php if (!empty($this->sidebar)) {echo 'col-md-9 col-xl-10'; } else { echo 'col-12'; } ?>">
            <div id="j-main-container" class="j-main-container"><?php
            // search tools bar
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
            <div class="clr"></div>
            <table class="table table-striped" id="articleList">
                <caption id="captionTable" class="sr-only">
                    <?php echo Text::_('COM_VISFORMS_CONTENT_FIELDS_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
                </caption>
                <thead><tr>
                    <th width="1%"  class="nowrap center"><?php
                        echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', strtolower($this->listDirection), $this->listOrdering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="3%"  class="nowrap center hidden-phone"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                    <th width="25%" class="nowrap"><?php echo $this->getSortHeader('COM_VISFORMS_LABEL', 'a.label'); ?></th>
                    <th width="5%"  class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_PUBLISHED', 'a.published'); ?></th>
                    <th width="10%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_TYPE', 'a.typefield'); ?></th>
                    <th width="10%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_FRONTEND_DISPLAY', 'a.frontdisplay'); ?></th>
                    <th width="10%" class="nowrap center"><?php echo JText::_('COM_VISFORMS_REQUIRED'); ?></th><?php
	                if ($hasSub) { ?>
                        <th width="10%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_IS_EDIT_ONLY_FIELD_LABEL', 'a.editonlyfield'); ?></th><?php
	                }
	                ?>
                    <th width="3%"  class="nowrap center hidden-phone"><?php echo $this->getSortHeader('COM_VISFORMS_ID', 'a.id'); ?></th> <?php if ($hasSub) {?>
                    <th width="5%"  class="nowrap"><?php
                        echo HTMLHelper::_('searchtools.sort', '', 'a.dataordering', strtolower($this->listDirection), $this->listOrdering, null, 'asc', 'COM_VISFORMS_GRID_HEADING_ORDERING_DATA_VIEW', 'icon-menu-2'); ?>
                    </th> <?php } ?>
                </tr>
                </thead>
                <tbody <?php if ($saveOrder || $saveOrderDataDetail) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->listDirection); ?>" data-nested="false" <?php endif; ?>><?php
                foreach ($this->items as $i => $item) {
                    $item->max_ordering = 0; // without the change between ascending and descending ordering doesn't work properly
                    $checked     = HTMLHelper::_('grid.id',   $i, $item->id );
                    $link        = Route::_( "$this->baseUrl&task=visfield.edit&id=". $item->id.'&fid='.$this->fid);
                    $authoriseId = "$this->authoriseName.$this->fid.$this->editViewName.$item->id";
                    $canCheckIn  = $this->user->authorise('core.manage',	 $this->componentName) || $item->checked_out == $this->userId || $item->checked_out == 0;
                    $canEdit     = $this->user->authorise('core.edit',		 $authoriseId);
                    $canChange   = $this->user->authorise('core.edit.state', $authoriseId) && $canCheckIn;
                    $canEditOwn  = $this->user->authorise('core.edit.own',	 $authoriseId) && $item->created_by == $this->userId;
                    $published   = HTMLHelper::_('jgrid.published', $item->published, $i, "$this->viewName.", $canChange  );
	                switch ($item->frontdisplay) {
		                case 1 :
			                $frontdisplay = JText::_('COM_VISFORMS_FIELD_FRONTDISPLAY_BOTH');
			                break;
		                case 2 :
			                $frontdisplay = JText::_('COM_VISFORMS_FIELD_FRONTDISPLAY_LIST_ONLY');
			                break;
		                case 3 :
			                $frontdisplay = JText::_('COM_VISFORMS_FIELD_FRONTDISPLAY_DETAIL_ONLY');
			                break;
		                default :
			                $frontdisplay = JText::_('COM_VISFORMS_FIELD_FRONTDISPLAY_NONE');
			                break;
	                }?>
                    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $this->fid; ?>">
                        <td class="order nowrap center"><?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                            } ?>
                            <span class="sortable-handler<?php echo $iconClass ?>"><i class="icon-menu"></i></span><?php
                            if ($canChange && $saveOrder) {?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " /><?php
                            } ?>
                        </td>
                        <td class="center hidden-phone"><?php echo $checked; ?></td>
                        <td class="has-context">
                            <div class="pull-left"><?php
                                if ($item->checked_out) {
                                    echo HTMLHelper::_('jgrid.checkedout', $i, $this->user->name, $item->checked_out_time, "$this->viewName.", $canCheckIn);
                                }
                                if ($canEdit || $canEditOwn) { ?>
                                    <a href="<?php echo $link; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>"><?php echo $item->label; ?></a>
                                    <p class="small"><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->name));?></p><?php
                                } else {
                                    echo $this->escape($item->label); ?>
                                    <p class="small"><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->name));?></p><?php
                                } ?>
                            </div>
                        </td>
                        <td class="center"><?php echo $published;?></td>
                        <td class="center nowrap"><?php echo $this->escape($item->typefield); ?></td>
                        <td class="center"><?php echo $frontdisplay;?></td>
                        <td class="center"><?php echo $item->required;?></td><?php
                        if ($hasSub) { ?>
                            <td class="center"><?php echo ($item->editonlyfield) ? JText::_('JYES') : JText::_('JNO');?></td><?php
                        } ?>
                        <td class="center hidden-phone"><?php echo $item->id; ?></td><?php if ($hasSub) { ?>
                        <td class="order nowrap"><?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrderDataDetail) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                            } ?>
                            <span class="sortable-handler<?php echo $iconClass ?>"><i class="icon-menu"></i></span><?php
                            if ($canChange && $saveOrderDataDetail) {?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->dataordering; ?>" class="width-20 text-area-order " /><?php
                            } ?>
                        </td> <?php } ?>
                    </tr><?php
                } ?>
                </tbody>
            </table><?php
            echo $this->pagination->getListFooter();
            // load the batch processing form
            if ($this->canDo->get('core.create')) {
                $options = array('label' => 'COM_VISFORMS_COPY_TO_FORM', 'description' => 'COM_VISFORMS_COPY_TO_FORM_DESC', 'controller' => 'visfield');
                echo HTMLHelper::_(
                    'bootstrap.renderModal',
                    'collapseModal',
                    array(
                        'title' => Text::_('COM_VISFORMS_FIELDS_BATCH_OPTIONS'),
                        'footer' => LayoutHelper::render('form.items_batch_copy_footer', $options),
                    ),
                    LayoutHelper::render('form.items_batch_copy', array('title' => 'COM_VISFORMS_FIELDS_BATCH_OPTIONS',
                        'label' => 'COM_VISFORMS_COPY_TO_FORM',
                        'description' => 'COM_VISFORMS_COPY_TO_FORM_DESC',
                        'controller' => 'visfield'))
                );
            }
            $layout = new JLayoutFile('div.form_hidden_inputs');
            echo $layout->render(); ?>
            </div>
        </div>
    </div>
</form>