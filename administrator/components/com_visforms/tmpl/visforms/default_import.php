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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>

<div  id="importFormModal" class="joomla-modal modal hide fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo Text::_('COM_VISFORMS_NEW_FILE_HEADER');?></h3>
                <button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
            </div>
            <div class="modal-body">
                <div class="column">
                    <form method="post" action="<?php echo Route::_("index.php?option=com_visforms&view=visform&task=visform.importform"); ?>" class="well" enctype="multipart/form-data">
                        <fieldset>
                            <input type="hidden" class="address" name="address" />
                            <input type="file" name="files" required /><?php
                            echo HTMLHelper::_('form.token'); ?>
                            <input type="submit" value="<?php echo Text::_('COM_VISFORMS_IMPORT');?>" class="btn btn-primary" />
                        </fieldset>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-bs-dismiss="modal"><?php echo Text::_('COM_VISFORMS_CLOSE'); ?></a>
            </div>
        </div>
    </div>
</div>