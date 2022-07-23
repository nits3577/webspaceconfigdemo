<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 * @since        Joomla 3.0.0
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<table id="table-creator-<?php echo $displayData['tag']; ?>" class="table table-striped table-hover table-bordered table-condensed" style="position:relative">
	<thead>
	<tr>
        <th style="text-align: center" class="hasPopover hiddenNotSortable" data-bs-content="<?php echo Text::_('COM_VISFORMS_ITEMLISTCREATOR_MOVE_DRAG_AND_DROP'); ?>"></th>
        <th style="text-align: center" class="hasPopover hiddenSortable" data-bs-content="<?php echo Text::_('COM_VISFORMS_MOVE_DESC'); ?>"><?php echo Text::_('COM_VISFORMS_MOVE'); ?></th>
		<th width="3%" class="nowrap center">
			<div class="checkbox" style="float: left"><label class="hasPopover" style="font-weight: bold" data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_CB_CREATE_DESC'); ?>"><?php
				echo str_replace('/>','checked/>', str_replace('>', ' />', HTMLHelper::_('grid.checkall', 'checkall-toggle', "Joomla.checkAll(this, '".$displayData['char'].'cb'."'); visHelper.hidePopover(this);"))); ?>
					<?php echo Text::_('COM_VISFORMS_CREATOR_CB_CREATE_LABEL'); ?></label></div>
		</th>
		<th title=""><?php echo Text::_('COM_VISFORMS_FIELD_TYPE'); ?></th>
		<th data-bs-content="<?php echo Text::_('COM_VISFORMS_NAME_DESC'); ?>" class="hasPopover"><?php echo Text::_('COM_VISFORMS_NAME'); ?></th>
		<th data-bs-content="<?php echo Text::_('COM_VISFORMS_LABEL_DESCR'); ?>" class="hasPopover"><?php echo Text::_('COM_VISFORMS_LABEL'); ?></th>
		<th width="3%" class="nowrap center">
			<div class="checkbox" style="float: left"><label class="hasPopover" style="font-weight: bold" data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_FIELD_FRONTEND_DISPLAY_DESCR'); ?>"><?php
				echo HTMLHelper::_('grid.checkall', 'checkall-toggle', "Joomla.checkAll(this, '".$displayData['char'].'fb'."'); visHelper.hidePopover(this);");  echo ' ' . Text::_('COM_VISFORMS_FRONTEND_DISPLAY_SIGN'); ?></label></div>
		</th>
        <th width="3%" class="nowrap center" title="">
            <div class="checkbox" style="float: left"><label class="hasPopover" style="font-weight: bold" data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_REQUIRED_DESC'); ?>"><?php
					echo HTMLHelper::_('grid.checkall', 'checkall-toggle', "Joomla.checkAll(this, '".$displayData['char'].'rb'."'); visHelper.hidePopover(this);"); ?>
                    <?php echo Text::_('COM_VISFORMS_REQUIRED_SIGN'); ?> </label></div>
        </th>
		<th style="text-align: center" class="hasPopover" data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_DEL_DESC'); ?>"></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($displayData['data'] as $i => $row) {
        $disabled = false;
		// field selection
		if('submit' == $row[0]) {
			// create selected and unreachable for header function 'select/deselect all'
            $checked = substr(HTMLHelper::_('grid.id', $i, $i, false, 'xid', 'xb'), 0, -1) . ' checked disabled';
			// no delete for the submit button
			$delete = '<td style="text-align: center; vertical-align: middle;"></td>';
			$disabled = true;
		}
		else {
		    $checked = substr(HTMLHelper::_('grid.id', $i, $i, false, $displayData['char'].'cid', $displayData['char'].'cb'), 0 , -1) . ' checked>';
			$delete = $displayData['tdDelete'];
		}
		// field front end display
		if('submit' == $row[0] || 'reset' == $row[0]) {
			// no checkbox for these buttons
			$frontEndDisplay = '';
			$required = '';
		}
		else {
			$frontEndDisplay = str_replace('/>', '/>', HTMLHelper::_('grid.id', $i, $i, false, $displayData['char'].'fid', $displayData['char'].'fb'));
			$required        = str_replace('/>', '/>', HTMLHelper::_('grid.id', $i, $i, false, $displayData['char'].'rid', $displayData['char'].'rb'));
		} ?>
		<tr><?php
            echo $displayData['tdMove']; ?>
			<td class="center "><?php echo $checked; ?></td>
			<td><?php echo $displayData['form']->typefield->getCreatorInput($row[0], $disabled); ?></td>
			<td><input name="" id="" value="<?php echo $row[1]?>" class="form-control inputbox required" size="50" placeholder="" maxlength="50" required="" aria-required="true" type="text"></td>
			<td><input name="" id="" value="<?php echo $row[2]?>" class="form-control inputbox required" size="50" placeholder="" maxlength="50" required="" aria-required="true" type="text"></td>
			<td class="center"><?php echo $frontEndDisplay; ?></td>
			<td class="center"><?php echo $required; ?></td>
			<?php echo $delete; ?>
		</tr>
	<?php }; ?>
	</tbody>
</table>
