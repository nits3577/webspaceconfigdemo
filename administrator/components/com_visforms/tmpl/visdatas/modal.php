<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

if (Factory::getApplication()->isClient('site')) {
	Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
}

$function	= Factory::getApplication()->input->getCmd('function', 'jSelectVisdatadetail');

?>
<div class="container-popup">
<form action="<?php echo Route::_('index.php?option=com_visforms&view=visdatas&layout=modal&fid='.$this->fid.'&tmpl=component&function='.$function.'&'.Session::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

	<table class="table table-striped table-condensed" id="articleList">
        <caption id="captionTable" class="sr-only">
			<?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
        </caption>
		<thead>
			<tr>
				<th class="center nowrap">
					<?php echo $this->getSortHeader('COM_VISFORMS_ID', 'a.id'); ?>
				</th>
                <?php
                $k = 0;
                $n=count( $this->fields );
                for ($i=0; $i < $n; $i++) {
	                $width = 30;
	                if ($n > 0) {
		                $width = floor(89/$n);
	                }
	                $rowField = $this->fields[$i];
	                if (!($rowField->showFieldInDataView === false)) {
		                if (empty($rowField->unSortable)) { ?>
                            <th width="<?php echo $width ?>%" class="nowrap"><?php
			                echo $this->getSortHeader($rowField->name, "a.F$rowField->id"); ?>
                            </th><?php
		                }
		                else { ?>
                            <th width="<?php echo $width ?>%" class="nowrap"><?php
			                echo $rowField->name; ?>
                            </th><?php
		                }
	                }
                }
                ?>
				<th width="10%" class="center nowrap">
					<?php echo $this->getSortHeader('JDATE', 'a.created'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->id)); ?>');">
						<?php echo $this->escape($item->id); ?></a>
				</td>
				<?php $z = count( $this->fields );
				for ($j=0; $j < $z; $j++) {
					$rowField = $this->fields[$j];
					if (!($rowField->showFieldInDataView === false)) {
						$prop="F".$rowField->id;
						if (isset($item->$prop) == false) {
							$prop=$rowField->name;
						}

						if (isset($item->$prop)) {
							$texts = $item->$prop;
						}
						else {
							$texts = "";
						}
						if ($rowField->typefield == 'file') {
							//info about uploaded files are stored in a JSON Object. Earlier versions just have a string.
							$texts = HTMLHelper::_('visforms.getUploadFileName', $texts);
							echo "<td>". $texts . "</td>";
						}
						else if ($rowField->typefield == 'signature') {
							$layout             = new JLayoutFile('visforms.datas.fields.signature', null);
							$layout->setOptions(array('component' => 'com_visforms'));
							$texts = $layout->render(array('field' => $rowField, 'data' => $texts, 'maxWidth' => 200));
							echo "<td>". $texts . "</td>";
						}
						else {
							if (StringHelper::strlen($texts) > 255) {
								$texts = StringHelper::substr($texts,0,255)."...";
							}
							echo "<td>" . $texts . "</td>";
						}
					}
				} ?>
                <td class="center nowrap">
					<?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                </td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div><?php
        $layout = new JLayoutFile('div.form_hidden_inputs');
        echo $layout->render(); ?>
	</div>
</form>
