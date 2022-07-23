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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div id="#template-manager-file" class="container-fluid">
	<div class="mt-2 p-2">
		<div class="row">
			<div class="col-12">
				<div class="card card-outline-secondary mb-2">
					<div class="card-body">
						<form method="post" action="<?php echo Route::_("index.php?option=com_visforms&view=vistools&layout=default&task=vistools.createFile&file=$this->file"); ?>">
							<div class="form-group">
								<label><?php echo Text::_('COM_VISFORMS_NEW_FILE_NAME'); ?></label>
								<input type="text" name="name" class="form-control" required>
							</div>
							<div class="form-group">
								<select class="form-select" data-chosen="true" name="type" required >
									<option value="">- <?php echo Text::_('COM_VISFORMS_NEW_FILE_SELECT'); ?> -</option>
									<option value="css">.css</option>
								</select>
							</div>
							<div class="form-group">
								<input type="hidden" class="address" name="address">
								<?php echo HTMLHelper::_('form.token'); ?>
								<button type="submit" class="btn btn-primary"><?php echo Text::_('COM_VISFORMS_BUTTON_CREATE'); ?></button>
							</div>
						</form>
					</div>
				</div>
				<div class="card card-outline-secondary mb-2">
					<div class="card-body">
						<form method="post" action="<?php echo Route::_("index.php?option=com_visforms&view=vistools&layout=default&task=vistools.uploadFile&file=$this->file"); ?>" class="well" enctype="multipart/form-data">
							<input type="hidden" class="address" name="address">
							<div class="input-group">
								<input type="file" name="files" class="form-control" required>
								<?php echo HTMLHelper::_('form.token'); ?>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary"><?php echo Text::_('COM_VISFORMS_BUTTON_UPLOAD'); ?></button>
								</span>
							</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>