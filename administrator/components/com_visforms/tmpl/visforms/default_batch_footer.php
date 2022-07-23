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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

?>
<div class="modal-footer">
    <button  class="btn" type="button" onclick="document.getElementById('batch-access').value='';document.getElementById('batch-language-id').value=''; document.getElementById('batch[copy_fields]c').checked=true;document.getElementById('batch[copy_fields]n').checked=false; document.getElementById('batch[copy_pdf_templates]c').checked=true;document.getElementById('batch[copy_pdf_templates]n').checked=false" data-bs-dismiss="modal">
        <?php echo Text::_('JCANCEL'); ?>
    </button>
    <button  class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('visform.batch');">
        <?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
    </button>
</div>
