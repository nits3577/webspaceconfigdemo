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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div class="modal-footer">
    <button class="btn" type="button" onclick="document.getElementById('batch-form-id').value='<?php echo $fid; ?>'" data-bs-dismiss="modal">
        <?php echo Text::_('JCANCEL'); ?>
    </button>
    <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('<?php echo $displayData['controller']; ?>.batch');">
        <?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
    </button>
</div>