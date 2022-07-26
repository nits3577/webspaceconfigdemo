<?php 
/**
 * Visfield field view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
    
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
$fieldsetsWithOptionlist = array('visf_select_options', 'visf_radio_options', 'visf_multicheckbox_options');
$sqlOptionListfieldNames = array('f_selectsql_sql', 'f_radiosql_sql', 'f_multicheckboxsql_sql', 'f_text_defaultvalue_sql', 'f_email_defaultvalue_sql', 'f_hidden_defaultvalue_sql', 'f_number_defaultvalue_sql', 'f_url_defaultvalue_sql', 'f_date_defaultvalue_sql');
$token = Session::getFormToken();
$fntext = 'Joomla\CMS\Language\Text::_';
$fnroute = 'Joomla\CMS\Router\Route::_';

// using <<< is a bit tricky. Static functions like Text::_ cannot be called directly. Use either $this->escape(Text::_('TEXT)) or $fntext('TEXT)
// $this->escape does not return the desired result, if we have a string with quotes (See COM_VISFORMS_CHECKBOX_VALUE_REQUIRED)
$js = <<<JS
    jQuery(document).ready(function() {
        visHelperAsync.stopWaitDonut();
    });
    var visField = {
        testSqlStatement: function (event, button) {
            event.preventDefault();
            var element = jQuery(button).parents('.sql-edit-controls');
            // textarea with sql-statement must have id attribute ending on _sql
            var sqlElement = jQuery(element).find('[id$=_sql]');
            var sql = sqlElement.val();
            // add class single_result to textarea with sql-statement in order to test using php loadResult
            var singleResult = sqlElement.hasClass('single_result') ? 'SingleResult' : '';
            // escape quotes
            sql = sql.replace(/"/g, '\\\"');
            // escape special characters
            sql = encodeURIComponent(sql);
            var messageDiv = jQuery(button).siblings('.sql-message-field');
            if(sql) {
                var waitDonut = jQuery(button).siblings('.div_ajax-call-wait').find('.icon_ajax-call-wait');
                var data = 'data=' + JSON.stringify({ statement: sql, "{$token}" : 1 });
                // show the waiting icon during the request
                visHelperAsync.startWaitDonut(waitDonut);
                jQuery.ajax({
                    type: 'POST',
                    // use differnt tasks, depending on whether we want to test for a single Result or an array of results
                    url: 'index.php?option=com_visforms&task=visfield.testSqlStatement'+singleResult,
                    data: data,
                    success: function(data, textStatus, jqXHR) {
                        visHelperAsync.stopWaitDonut();
                        jQuery(messageDiv).text(data.message);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        visHelperAsync.stopWaitDonut();
                        // give error feedback
                        jQuery(messageDiv).text('error');
                    },
                    dataType: 'json',
                    async: true
                });
            }
            else {
                jQuery(messageDiv).text('{$fntext('COM_VISFORMS_EMPTY_SQL_STATEMENT')}');
            }
        },
    }
    var visHelperAsync = {
        startWaitDonut: function (waitDonut) {
            jQuery(waitDonut).show();
        },
        stopWaitDonut: function (waitDonut) {
            if(null == waitDonut) {
                jQuery('.icon_ajax-call-wait').hide();
            }
            else {
                jQuery(waitDonut).hide();
            }
        }
    };
    function removeUnused(selected) {
        var fieldType = ['text', 'email', 'date', 'url', 'number', 'password', 'hidden', 'textarea', 'checkbox', 'multicheckbox', 'radio', 'select', 'file', 'image', 'reset', 'submit', 'fieldsep', 'pagebreak', 'calculation', 'location', 'signature', 'multicheckboxsql', 'radiosql', 'selectsql'];
        var fieldTypesWithOptionlist = ['multicheckbox', 'radio', 'select'];
        for (var i in fieldType) {
            if (selected != fieldType[i]) {
                try {
                    var elname = 'visf_' + fieldType[i];
                    var el = document.getElementById(elname);
                    el.parentNode.removeChild(el);
                }
                catch (e) { }
            }
            for (var j in fieldTypesWithOptionlist) {
                if (selected != fieldTypesWithOptionlist[j]) {
                    try {
                        var elname = 'visf_' + fieldTypesWithOptionlist[j] + '_options';
                        var el = document.getElementById(elname);
                        el.parentNode.removeChild(el);
                    }
                    catch (e) { }
                }
            }
        }
    }
    Joomla.submitbutton = function(task) {
		if (task == 'visfield.cancel') {
            jQuery('#permissions-sliders select').attr('disabled', 'disabled');
            Joomla.submitform(task, document.getElementById('item-form'));
		}
		else if (document.formvalidator.isValid(document.getElementById('item-form'))) {
            Joomla.removeMessages();
            jQuery('#permissions-sliders select').attr('disabled', 'disabled');
            //make sure the typefield has a selected value
            var ft = document.getElementById('jform_typefield');
            var idx = ft.selectedIndex;
            var sel = ft[idx].value;
            switch (sel) {
                case '0' :
                    alert('{$this->escape(Text::_('COM_VISFORMS_TYPE_FIELD_REQUIRED'))}');
                    break;
                case 'checkbox' :
                    var cbval = document.getElementById('jform_defaultvalue_f_checkbox_attribute_value');
                    if (cbval.value == "") {
                        alert('{$fntext('COM_VISFORMS_CHECKBOX_VALUE_REQUIRED')}');
                    }
                    else {
                        removeUnused(sel);
                        Joomla.submitform(task, document.getElementById('item-form'));
                    }
                    break;
                case 'multicheckbox' :
                case 'radio' :
                    jQuery('#jform_defaultvalue_f_' + sel + '_list_hidden').storeVisformsOptionCreatorData();
                    var grpel = document.getElementById('jform_defaultvalue_f_' + sel + '_list_hidden');
                    var countDefOpts = document.getElementById('jform_defaultvalue_f_' + sel + '_countDefaultOpts').value;
                    if (grpel.value == "" || grpel.value == "{}") {
                        alert('{$this->escape(Text::_('COM_VISFORMS_OPTIONS_REQUIRED'))}');
                    }
                    else if (countDefOpts > 1) {
                        alert('{$this->escape(Text::_('COM_VISFORMS_ONLY_ONE_DEFAULT_OPTION_POSSIBLE'))}');
                    }
                    else {
                        removeUnused(sel);
                        Joomla.submitform(task, document.getElementById('item-form'));
                    }
                    break;
                case 'select' :
                    jQuery('#jform_defaultvalue_f_' + sel + '_list_hidden').storeVisformsOptionCreatorData();
                    var grpel = document.getElementById('jform_defaultvalue_f_' + sel + '_list_hidden');
                    var countDefOpts = document.getElementById('jform_defaultvalue_f_' + sel + '_countDefaultOpts').value;
                    var isMultiple = document.getElementById('jform_defaultvalue_f_' + sel + '_attribute_multiple').checked;
                    if (grpel.value == "" || grpel.value == "{}") {
                        alert('{$this->escape(Text::_('COM_VISFORMS_OPTIONS_REQUIRED'))}');
                    }
                    else if ((countDefOpts > 1) && (isMultiple == false)) {
                        alert('{$this->escape(Text::_('COM_VISFORMS_ONLY_ONE_DEFAULT_OPTION_POSSIBLE'))}');
                    }
                    else {
                        removeUnused(sel);
                        Joomla.submitform(task, document.getElementById('item-form'));
                    }
                    break;
                case 'radiosql':
                case 'multicheckboxsql':
                case 'selectsql':
                    var sql = document.getElementById('jform_defaultvalue_f_' + sel + '_sql').value;
                    if (sql == "") {
                        alert('{$this->escape(Text::_('COM_VISFORMS_SQL_REQUIRED'))}');
                    }
                    else {
                        removeUnused(sel);
                        Joomla.submitform(task, document.getElementById('item-form'));
                    }
                    break;
                case 'image':
                        var altt = document.getElementById('jform_defaultvalue_f_image_attribute_alt');
                        var image = document.getElementById('jform_defaultvalue_f_image_attribute_src');
                        if ((altt.value == "") || (image.value == "")) {
                            if (altt.value == "") {
                                alert('{$fntext('COM_VISFORMS_ALT_TEXT_REQUIRED')}');
                            }
                            else {
                                alert('{$this->escape(Text::_('COM_VISFORMS_FIELD_IMAGE_IMAGE_REQUIRED'))}');
                            }
                        }
                        else {
                            removeUnused(sel);
                            Joomla.submitform(task, document.getElementById('item-form'));
                        }
                    break;
                case 'location' :
                    var reLat = /^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/;
                    var reLng = /^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/;
                    var latCenter = document.getElementById('jform_defaultvalue_f_location_defaultMapCenter_lat').value;
                    var lngCenter = document.getElementById('jform_defaultvalue_f_location_defaultMapCenter_lng').value;
                    var validLatCenter = reLat.test(latCenter) && latCenter !== "";
                    var validLngCenter = reLng.test(lngCenter) && lngCenter !== "";
                    var latPos = document.getElementById('jform_defaultvalue_f_location_attribute_value_lat').value;
                    var lngPos = document.getElementById('jform_defaultvalue_f_location_attribute_value_lng').value;
                    var validLatPos = (reLat.test(latPos) || (latPos === "" && lngPos === ""));
                    var validLngPos = (reLng.test(lngPos) || (latPos === "" && lngPos === ""));
                    if (!validLatCenter || !validLngCenter) {
                        alert('{$this->escape(Text::_('COM_VISFORMS_LOCATION_DEFAULT_CENTER_VALUES_REQUIRED'))}');
                    } else if (!validLatPos || !validLngPos) {
                        alert('{$this->escape(Text::_('COM_VISFORMS_LOCATION_DEFAULT_POSITION_VALUES_INVALID_FORMAT'))}');
                    }
                    else {
                        removeUnused(sel);
                        Joomla.submitform(task, document.getElementById('item-form'));
                    }
                    break;
                default :
                    removeUnused(sel);
                    Joomla.submitform(task, document.getElementById('item-form'));
                    break;
            }
		}
		else {
			alert('{$this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED'))}');
		}
	}
JS;
$this->document->addScriptDeclaration($js);
?>

<form action="<?php echo Route::_("$this->baseUrl&view=$this->editViewName&layout=edit&id=$this->id&fid=$this->fid"); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <input type="hidden" name="option" value="com_visforms" />
    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="fid" value="<?php echo $this->fid; ?>" />
    <input type="hidden" name="ordering" value="<?php echo $this->item->ordering; ?>" />
    <input type="hidden" name="controller" value="visfields" /><?php
    $layout = new JLayoutFile('div.form_hidden_inputs');
    echo $layout->render(); ?>
	<div class="j-main-container">
        <div class="m-t-2 m-b-3"><?php
            echo $this->form->renderField('label');
            echo $this->form->renderField('name'); ?>
        </div>
        <div class="form-horizontal"><?php
            echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'basicfieldinfo', 'recall' => true, 'breakpoint' => 768));
            echo HTMLHelper::_('uitab.addTab', 'myTab', 'basicfieldinfo', Text::_('COM_VISFORMS_FIELD_BASIC_INFO')); ?>
            <div class="row">
                <div class="col-lg-12 col-xl-6"><?php
                    foreach ($this->form->getFieldset('basicfieldinfo') as $field) {
                        if ($field->fieldname != 'ordering' && $field->fieldname != 'checked_out' && $field->fieldname != 'checked_out_time') {
                            echo $field->renderField();
                        }
                    } ?>
                </div>
                <div class="col-lg-12 col-xl-6"><?php
                    $groupFieldSets = $this->form->getFieldsets('defaultvalue');
                    foreach ($groupFieldSets as $name => $fieldSet) {
                        if (in_array($name, $fieldsetsWithOptionlist)) {continue;}?>
                        <div id="<?php echo $name; ?>"><?php
                            foreach ($this->form->getFieldset($name) as $field) {
                                if (in_array($field->fieldname, $sqlOptionListfieldNames)) {
                                    $statement = $this->form->getField($field->fieldname, 'defaultvalue');
                                    $renderData  = array("input" => $statement->__get('input'), "view" => $this, "task" => 'visField.testSqlStatement');
                                    $label = $statement->__get('label');
                                    $input = (new JLayoutFile('renderpdf.fields.sql_statement_selection'))->render($renderData);
                                    echo $statement->render('joomla.form.renderfield', array("label" => $label, "input" => $input));
                                    continue;
                                }
                               // if we have a date field we have to set default dateformat for the calendar
                               if ($field->fieldname === "f_date_attribute_value") {
                                   $dateFormatField = $this->form->getField('f_date_format', 'defaultvalue');
                                   if ($dateFormatField->value != "") {
                                       // get date format for javascript
                                       $dFormat = explode(";", $dateFormatField->value);
                                       if (isset($dFormat[1])) {
                                           $this->form->setFieldAttribute("f_date_attribute_value", "format", $dFormat[1], 'defaultvalue');
                                       }
                                   }
                               }
                               echo $field->renderField();
                            } ?>
                        </div> <?php
                    } ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="col-lg-12 col-xl-6"><?php
                    foreach ($fieldsetsWithOptionlist as $name) {
                        $groupFieldSet = $this->form->getFieldset($name, 'defaultvalue'); ?>
                        <div id="<?php echo $name; ?>"><?php
                        foreach ($groupFieldSet as $field) {
                            echo $field->renderField();
                        }
                        ?>
                        </div><?php
                    } ?>
                </div>
            </div><?php
            echo HTMLHelper::_('uitab.endTab');
            echo HTMLHelper::_('uitab.addTab', 'myTab', 'visfield-advanced-detailso', Text::_('COM_VISFORMS_TAB_ADVANCED')); ?>
            <div class="row">
                <div class="col-lg-12 col-xl-6">
                    <h3><?php echo Text::_('COM_VISFORMS_TAB_LAYOUT'); ?></h3><?php
                    $btgridLayout = $this->form->getFieldset('visfield-bootstrap-grid');
                    if (!empty($btgridLayout)) {
                        echo '<div id="bootstrapGridSizes">';
                        foreach ($btgridLayout as $field) {
                            echo $field->renderField();
                        }
                        echo '</div>';
                    }
                    $fsLayout = $this->form->getFieldset('visfield-layout-details');
                    foreach ($fsLayout as $field) {
                        echo $field->renderField();
                    } ?>
                </div>
                <div class="col-lg-12 col-xl-6">
                    <h3><?php echo Text::_('COM_VISFORMS_HEADER_USAGE'); ?></h3><?php
                    $fsAdvanced = $this->form->getFieldset('visfield-advanced-details');
                    foreach ($fsAdvanced as $field) {
                        echo $field->renderField();
                    }
                    $fslayoutcustomtext= $this->form->getFieldset('layout-custom-text');
                    foreach ($fslayoutcustomtext as $field) {
                        // display editor without control-group html as fix for poor responsive editor field layout breaks showon functionality
                        // use custom class and custom css to fix editor layout, add class to control-group element
	                    echo $field->renderField(array("class" => $field->class));
                    } ?>
                </div>
            </div><?php
            echo HTMLHelper::_('uitab.endTab');

            if ($this->canDo->get('core.admin')) {
                echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_VISFORMS_FIELDSET_FIELD_RULES', true));
                echo $this->form->getInput('rules');
                HTMLHelper::_('uitab.endTab');
            }

            echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
    </div>
</form>