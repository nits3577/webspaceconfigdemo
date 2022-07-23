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
<div class="container">
    <div class="row">
        <div class="">
            <div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6"><?php
            // Create the copy/move options.
            $options = array(HTMLHelper::_('select.option', 'c', Text::_('JYes')),
                HTMLHelper::_('select.option', 'n', Text::_('JNo')));

            // Create the batch selector to select whether to copy fields or not.
            $lines = array('<label id="batch-choose-action-lbl" for="batch-choose-action">', Text::_('COM_VISFORMS_COPY_FIELDS'), '</label>',
                '<fieldset id="batch-choose-action" class="combo">',
                //show the radiolist with default 0
                HTMLHelper::_('select.radiolist', $options, 'batch[copy_fields]', '', 'value', 'text', 'c'), '</fieldset>');

            echo implode("\n", $lines);
            if (VisformsAEF::checkAEF(VisformsAEF::$subscription)) {
                // Create the batch selector to select whether to copy pdf-templates or not.
                $lines = array('<label id="batch-choosepdf-action-lbl" for="batch-choosepdf-action">', Text::_('COM_VISFORMS_COPY_PDF_TEMPLATES'), '</label>',
                    '<fieldset id="batch-choose-pdf-action" class="combo">',
                    //show the radiolist with default 0
                    HTMLHelper::_('select.radiolist', $options, 'batch[copy_pdf_templates]', '', 'value', 'text', 'c'), '</fieldset>');
                echo implode("\n", $lines);
            } ?>
        </div>
    </div>
</div>