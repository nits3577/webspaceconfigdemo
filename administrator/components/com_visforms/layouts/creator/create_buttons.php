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

?>
<button data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_BUTTON_ADD_FIELD_DESC'); ?>" onclick="visTable.tableAddRow(event, '<?php echo $displayData['tag']; ?>', '<?php echo $displayData['char']; ?>');" class="btn btn-small btn-info btn-add-field hasPopover"><?php echo Text::_('COM_VISFORMS_CREATOR_BUTTON_ADD_FIELD_LABEL'); ?></button>
<?php
    echo (new JLayoutFile('div.quickstart_help_element'))->render(array('step' => 3, 'description' => $displayData['text'], 'tag' => 'span', 'hideTextStep' => true)); ?>

<div style="float: right" class="btn-create-form-float"><?php
	echo (new JLayoutFile('div.quickstart_help_element'))->render(array('step' => 4, 'tag' => 'span', 'hideTextStep' => true)); ?>
    <button style="margin-left: 0px; display: inline-block;" data-bs-content="<?php echo Text::_('COM_VISFORMS_CREATOR_BUTTON_CREATE_FORM_AND_FIELDS_DESC'); ?>" onclick="visHelper.createForm(event, '<?php echo $displayData['tag']; ?>', '<?php echo $displayData['char']; ?>');" class="btn btn-small btn-info btn-create-form hasPopover" data-bs-trigger="hover focus"><?php echo Text::_('COM_VISFORMS_CREATOR_BUTTON_CREATE_FORM_AND_FIELDS_LABEL'); ?></button>
</div>