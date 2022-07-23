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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$input = Factory::getApplication()->input;
?>
<form method="post" action="">
	<input type="hidden" name="option" value="com_visforms">
	<input type="hidden" name="task" value="vistools.delete">
	<input type="hidden" name="file" value="<?php echo $this->file; ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('COM_VISFORMS_CLOSE'); ?></button>
	<button type="submit" class="btn btn-danger"><?php echo Text::_('COM_VISFORMS_DELETE'); ?></button>
</form>
