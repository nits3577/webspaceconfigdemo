<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2021 vi-solutions
 * @since        Joomla 1.6
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div id="template-manager-rename" class="container-fluid">
	<div class="mt-2">
		<div class="col-md-12">
			<div class="control-group">
				<div class="control-label">
					<label for="new_name" class="modalTooltip" title="<?php echo HTMLHelper::_('tooltipText', Text::_('COM_VISFORMS_NEW_FILE_NAME')); ?>">
						<?php echo Text::_('COM_VISFORMS_NEW_FILE_NAME')?>
					</label>
				</div>
				<div class="controls">
					<div class="input-group">
						<input class="form-control" type="text" name="new_name" required>
						<div class="input-group-addon">.<?php echo JFile::getExt($this->fileName); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
