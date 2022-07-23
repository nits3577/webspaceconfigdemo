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
use Joomla\CMS\HTML\HTMLHelper;

$options = array(HTMLHelper::_('select.option', 'c', Text::_('JYes')),
	HTMLHelper::_('select.option', 'n', Text::_('JNo')));

?>
<div class="joomla-modal modal hide fade" id="exportFormModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo Text::_('COM_VISFORMS_FORM_EXPORT_OPTIONS'); ?></h3>
                <button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="form-group col-md-6"><?php
                            // Create the export selector to select whether to copy fields or not.
                            $lines = array('<label id="export-field-choose-action-lbl" for="export-field-choose-action">', Text::_('COM_VISFORMS_FORM_EXPORT_COPY_FIELDS'), '</label>',
                                '<fieldset id="export-field-choose-action">',
                                //show the radiolist with default 0
                                HTMLHelper::_('select.radiolist', $options, 'export[copy-fields]', '', 'value', 'text', 'c'),
                                '</fieldset>');

                            echo implode("\n", $lines);
                            $lines = array('<label id="export-data-choose-action-lbl" for="export-data-choose-action">', Text::_('COM_VISFORMS_FORM_EXPORT_COPY_DATA'), '</label>',
                                '<fieldset id="export-data-choose-action">',
                                //show the radiolist with default 0
                                HTMLHelper::_('select.radiolist', $options, 'export[copy-data]', '', 'value', 'text', 'n'),
                                '</fieldset>');

                            echo implode("\n", $lines);
                            if (VisformsAEF::checkAEF(VisformsAEF::$subscription)) {
                                // Create the batch selector to select whether to copy pdf-templates or not.
                                $lines = array('<label id="export-pdf-choose-action-lbl" for="export-pdf-choose-action">', Text::_('COM_VISFORMS_FORM_EXPORT_COPY_PDF_TEMPLATES'), '</label>',
                                    '<fieldset id="export-pdf-choose-action">',
                                    //show the radiolist with default 0
                                    HTMLHelper::_('select.radiolist', $options, 'export[copy-pdf-templates]', '', 'value', 'text', 'c'),
                                '</fieldset>');
                                echo implode("\n", $lines);
                            }
                            echo '<hr/><p class="alert alert-danger">'.Text::_('COM_VISFORMS_EXPORT_OPTIONS_WARNING_USERID_ACL').'</p>';
                            $lines = array('<label id="export-userid-choose-action-lbl" for="export-userid-choose-action">', Text::_('COM_VISFORMS_FORM_EXPORT_COPY_USERID'), '</label>',
                                '<fieldset id="export-userid-choose-action">',
                                //show the radiolist with default 0
                                HTMLHelper::_('select.radiolist', $options, 'export[copy-userid]', '', 'value', 'text', 'n'),
                                '</fieldset>');

                            echo implode("\n", $lines);
                            $lines = array('<label id="export-acl-choose-action-lbl" for="export-acl-choose-action">', Text::_('COM_VISFORMS_FORM_EXPORT_COPY_ACL'), '</label>',
                                '<fieldset id="export-acl-choose-action">',
                                //show the radiolist with default 0
                                HTMLHelper::_('select.radiolist', $options, 'export[copy-acl]', '', 'value', 'text', 'n'),
                                '</fieldset>');
                            echo implode("\n", $lines);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button  class="btn" type="button" onclick="document.getElementById('export[copy-fields]c').checked=true;document.getElementById('export[copy-fields]n').checked=false; document.getElementById('export[copy-data]c').checked=false;document.getElementById('export[copy-data]n').checked=true;document.getElementById('export[copy-pdf-templates]c').checked=true;document.getElementById('export[copy-pdf-templates]n').checked=false;document.getElementById('export[copy-userid]c').checked=false;document.getElementById('export[copy-userid]n').checked=true;document.getElementById('export[copy-acl]c').checked=false;;document.getElementById('export[copy-acl]n').checked=true;" data-bs-dismiss="modal">
                    <?php echo Text::_('JCANCEL'); ?>
                </button>
            <button  class="btn btn-primary" type="button" onclick="exportForm()" data-bs-dismiss="modal">
                <?php echo Text::_('COM_VISFORMS_EXPORT_FORM_DEFINITION'); ?>
            </button>
            </div>
        </div>
    </div>
</div>
