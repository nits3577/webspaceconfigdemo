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
<div class="container">
    <div class="row">
        <div class="form-group col-md-6">
            <div class="controls"> <?php
                $fid = Factory::getApplication()->input->getInt( 'fid', -1 );
                // create copy select options
                $db = Factory::getDbo();
                $query = $db->getQuery(true);
                $query->select('a.id, a.title');
                $query->from('#__visforms AS a');
                try {
                    $db->setQuery($query);
                    $forms = $db->loadObjectList();
                }
                catch (RuntimeException $e) {
                    $forms = false;
                }
                $options = array();
                foreach ($forms as &$form){
                    $options[] = HTMLHelper::_('select.option', $form->id, $form->title);
                }
                // Create the batch forms listbox, default selected value the form, the fields belong to.
                ?>
                <label id="batch-choose-action-lbl" for="batch-choose-action"><?php echo Text::_($displayData['label']); ?></label>
                <fieldset id="batch-choose-action" class="combo">
                    <select name="batch[form_id]" class="custom-select" title="<?php echo Text::_($displayData['label']) . '::' . Text::_($displayData['description']); ?>" id="batch-form-id">
                        <?php echo HTMLHelper::_('select.options', $options, 'value', 'text', $fid); ?>
                    </select>
                </fieldset>
            </div>
        </div>
    </div>
</div>