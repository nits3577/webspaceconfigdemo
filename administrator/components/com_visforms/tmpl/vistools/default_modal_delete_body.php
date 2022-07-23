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
?>
<div id="template-manager-delete" class="container-fluid">
	<div class="mt-2">
        <div class="col-md-12">
            <p><?php echo Text::sprintf('COM_VISFORMS_MODAL_FILE_DELETE', $this->fileName); ?></p>
        </div>
	</div>
</div>