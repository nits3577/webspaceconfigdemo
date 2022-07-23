<?php
/**
 * Visform form view for Visforms
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

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('jQuery.framework');

// Check if TinyMCE editor is enable. If not we have to hide the editor buttons
$db = Factory::getDbo();
$query = $db->getQuery(true)
    ->select($db->qn('element'))
    ->from($db->qn('#__extensions'))
    ->where($db->qn('element') .' = ' . $db->quote('tinymce'))
    ->where($db->qn('folder') .' = ' . $db->quote('editors'))
    ->where($db->qn('enabled') . ' = 1');
try {
	$db->setQuery($query, 0, 1);
	$editor = $db->loadResult();
}
catch (RuntimeException $e) {
    $editor = false;
}
$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
// boolean result "false" is echoed as empty string
$hasSubForJs = $hasSub ? 1 : 0;


// if no editor is found stop tinyMCE is disabled
if (!$editor) {
    // hide editor button div
    $css = '#editor-xtd-buttons {display: none;}';
    $doc = Factory::getApplication()->getDocument();
    $doc->addStyleDeclaration($css);
}
$js = <<<JS
Joomla.submitbutton = function(task) {
        if (task == 'visform.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
            jQuery('#permissions-sliders select').attr('disabled', 'disabled');
            if (task !='visform.cancel') {
                // check that dom element exists and that data storage contains an instance of the visformsOptionCreator for this dom element
                if (jQuery('#jform_visformsmailattachments_params_f_attachment_list_hidden').length && typeof jQuery.data(jQuery('#jform_visformsmailattachments_params_f_attachment_list_hidden')[0], 'visformsOptionCreator') !== "undefined") {
                    jQuery('#jform_visformsmailattachments_params_f_attachment_list_hidden').storeVisformsOptionCreatorData();
                }
                // check that dom element exists and that data storage contains an instance of the visformsOptionCreator for this dom element
                if (jQuery('#jform_visformseditmailattachments_params_f_editattachment_list_hidden').length && typeof jQuery.data(jQuery('#jform_visformseditmailattachments_params_f_editattachment_list_hidden')[0], 'visformsOptionCreator' ) !== "undefined") {
                    jQuery('#jform_visformseditmailattachments_params_f_editattachment_list_hidden').storeVisformsOptionCreatorData();
                }
            }
            Joomla.submitform(task, document.getElementById('item-form'));
        }
        else {
            alert('{$this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED'))}');
        }
    }
    var googleReCaptchaListBoxSelection = function () {
        var layout = document.getElementById('jform_layoutsettings_formlayout');
        var layoutVal = layout.options[layout.selectedIndex].value;
        var subLayout = document.getElementById('jform_layoutsettings_displaysublayout');
        var subLayoutVal = subLayout.options[subLayout.selectedIndex].value;
        var captcha = document.getElementById('jform_captcha');
        var captchaval = captcha.options[captcha.selectedIndex].value;
        if (layoutVal == 'btdefault' && subLayoutVal == 'individual') {
            if (captchaval == 2) {
                document.getElementById('jform_captchaoptions_grecaptcha2label_bootstrap_size').parentNode.parentNode.style.display='block';
            }
            else {
                document.getElementById('jform_captchaoptions_grecaptcha2label_bootstrap_size').parentNode.parentNode.style.display='none';
            }
        }
        else {
            document.getElementById('jform_captchaoptions_grecaptcha2label_bootstrap_size').parentNode.parentNode.style.display='none';
        }
    }
    var googleReCaptchaBt4ListBoxSelection = function () {
        var layout = document.getElementById('jform_layoutsettings_formlayout');
        var layoutVal = layout.options[layout.selectedIndex].value;
        var subLayout = document.getElementById('jform_layoutsettings_displaysublayout');
        var subLayoutVal = subLayout.options[subLayout.selectedIndex].value;
        if ((layoutVal == 'bt4mcindividual' || layoutVal == 'bt5')  && subLayoutVal == 'individual'  && $hasSubForJs) {
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidth').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthSm').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthMd').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthLg').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthXl').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_btLabelWidthDesc-lbl').parentNode.parentNode.style.display='block';
            if (layoutVal == 'bt5') {
                document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthXxl').parentNode.parentNode.style.display='block';
            }
            else {
                document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthXxl').parentNode.parentNode.style.display='none';
            }
        } else {
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidth').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthSm').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthMd').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthLg').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthXl').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_btLabelWidthDesc-lbl').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelBootstrapWidthXxl').parentNode.parentNode.style.display='none';
        }
        if (layoutVal == 'uikit3' && subLayoutVal == 'individual') {
            document.getElementById('jform_captchaoptions_captchaLabelUikit3Width').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthSm').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthMd').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthLg').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthXl').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_uikit3LabelWidthDesc-lbl').parentNode.parentNode.style.display='block';
        } else {
            document.getElementById('jform_captchaoptions_captchaLabelUikit3Width').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthSm').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthMd').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthLg').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit3WidthXl').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_uikit3LabelWidthDesc-lbl').parentNode.parentNode.style.display = 'none';
        }
        if (layoutVal == 'uikit2' && subLayoutVal == 'individual') {
            document.getElementById('jform_captchaoptions_captchaLabelUikit2Width').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthSm').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthMd').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthLg').parentNode.parentNode.style.display='block';
            document.getElementById('jform_captchaoptions_uikit2LabelWidthDesc-lbl').parentNode.parentNode.style.display='block';
        } else {
            document.getElementById('jform_captchaoptions_captchaLabelUikit2Width').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthSm').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthMd').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_captchaLabelUikit2WidthLg').parentNode.parentNode.style.display='none';
            document.getElementById('jform_captchaoptions_uikit2LabelWidthDesc-lbl').parentNode.parentNode.style.display = 'none';
        }
    }
JS;
$this->document->addScriptDeclaration($js);
?>

<form id="item-form" class="form-validate" action="<?php echo Route::_("$this->baseUrl&view=$this->editViewName&layout=edit&id=$this->id"); ?>" method="post" name="adminForm">
    <div id="j-main-container">
        <div class="m-t-2 m-b-3"><?php
	    // Todo remove quick start help step or complete it
	    //echo (new JLayoutFile('div.quickstart_help_element'))->render(array('step' => 1, 'description' => 'COM_VISFORMS_CREATOR_QUICKSTART_STEP1'));
		    echo $this->form->renderField('title');
		    echo $this->form->renderField('name'); ?>
    </div>
    <div class="form-horizontal"><?php
    $formFieldSets = $this->form->getFieldsets();
    // we are done with form title
    unset($formFieldSets['form_title']);
    // access rules placed at the very end
    if((isset($formFieldSets[$name = 'access-rules']))) {
        unset($formFieldSets[$name]);
    }

    echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'visform-basic-details', 'recall' => true, 'breakpoint' => 768));

    //custom layout for first tab
    if((isset($formFieldSets[$name = 'visform-basic-details']))) {
        $fieldSet = $formFieldSets[$name];
        echo HTMLHelper::_('uitab.addTab', 'myTab', $name, Text::_($fieldSet->label)); ?>
        <div class="row">
            <div class="col-lg-12 col-xl-6">
                <fieldset class="adminform"><?php
                    foreach ($this->form->getFieldset($name) as $field) {
	                    if ($field->type == 'Editor') {
		                    echo $this->form->getLabel($field->fieldname);
		                    echo $this->form->getInput($field->fieldname);
		                    echo '<div class="clearfix"></div>';
	                    }
	                    else {
		                    if ($field->fieldname != 'checked_out' && $field->fieldname != 'checked_out_time' && $field->fieldname != 'hits') {
			                    echo $field->renderField();
		                    }
	                    }
                    } ?>
                </fieldset>
            </div>
        </div><?php
        unset($formFieldSets[$name]);
        echo HTMLHelper::_('uitab.endTab');
    }

    //layout for all other tabs except the permissions tab
    foreach ($formFieldSets as $name => $fieldSet) {
        if ($hasSub || ($name !== 'visform-edit-email-details')) {
            echo HTMLHelper::_('uitab.addTab', 'myTab', $name, Text::_($fieldSet->label)); ?>
            <div class="row"><?php
            //custom layout for plugin mailattachments tab display attachment selection on top with full width; display pdf and csv attachment options below in two colums
            if ($name === 'visforms-extension-mailattachments') { ?>
                <div class="col-lg-12" style="padding-right: 20px;"> <?php
                    foreach ($this->form->getFieldset($name) as $field) {
                        if (!($field->group == 'visformsmailattachments_params.exportsettings')) {
                            if (($field->fieldname == 'f_attachment_list_hidden') || ($field->fieldname == 'visformsmailattachemtsspacer')) {
                                echo $field->renderField();
                            }
                        }
                    } ?>
                </div>
                <div class="col-lg-12 col-xl-6"><?php
                foreach ($this->form->getFieldset($name) as $field) {
                    if (!($field->group == 'visformsmailattachments_params.exportsettings')) {
                        if ((!($field->fieldname == 'f_attachment_list_hidden')) && (!($field->fieldname == 'visformsmailattachemtsspacer'))) {
                            echo $field->renderField();
                        }
                    }
                } ?>
                </div>
                <div class="col-lg-12 col-xl-6"><?php
                foreach ($this->form->getFieldset($name) as $field) {
                    if ($field->group == 'visformsmailattachments_params.exportsettings') {
                        echo $field->renderField();
                    }
                } ?>
                </div><?php
            }
	        else if ($name === 'visforms-extension-editmailattachments') { ?>
                <div class="col-lg-12" style="padding-right: 20px;"> <?php
			        foreach ($this->form->getFieldset($name) as $field) {
				        if (!($field->group == 'visformseditmailattachments_params.exportsettings')) {
					        if (($field->fieldname == 'f_editattachment_list_hidden') || ($field->fieldname == 'visformseditmailattachemtsspacer')) {
						        echo $field->renderField();
					        }
				        }
			        } ?>
                </div>
                <div class="col-lg-12 col-xl-6"><?php
			        foreach ($this->form->getFieldset($name) as $field) {
				        if (!($field->group == 'visformsmailattachments_params.exportsettings')) {
					        if ((!($field->fieldname == 'f_editattachment_list_hidden')) && (!($field->fieldname == 'visformseditmailattachemtsspacer'))) {
						        echo $field->renderField();
					        }
				        }
			        } ?>
                </div>
                <div class="col-lg-12 col-xl-6"><?php
		        foreach ($this->form->getFieldset($name) as $field) {
			        if ($field->group == 'visformseditmailattachments_params.exportsettings') {
				        echo $field->renderField();
			        }
		        } ?>
                </div><?php
	        }
            else {
                //all the other tabs ?>
                <div class="col-lg-12 col-xl-6"><?php
                foreach ($this->form->getFieldset($name) as $field) {
                    if ($field->type == 'Editor') {
                        echo $this->form->getLabel($field->fieldname,  $field->group);
	                    echo $this->form->getInput($field->fieldname,  $field->group);
	                    echo '<div class="clearfix"></div>';
                    }
                    else {
	                    echo $field->renderField();
                    }
                } ?>
                </div><?php
            } ?>
            </div><?php
            echo HTMLHelper::_('uitab.endTab');
        }
    } ?>
   <?php
    // layout for permissions tab
    if ($this->canDo->get('core.admin')) {
        echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_VISFORMS_FIELDSET_FORM_RULES', true));
        echo $this->form->getInput('rules');
        echo HTMLHelper::_('uitab.endTab');
    }
    echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div><?php
    $layout = new JLayoutFile('div.form_hidden_inputs');
    echo $layout->render(); ?>
    </div>
    </div>
</form>
<?php
$js = <<<JS
document.addEventListener("DOMContentLoaded", googleReCaptchaListBoxSelection);
document.addEventListener("DOMContentLoaded", googleReCaptchaBt4ListBoxSelection);
JS;

$this->document->addScriptDeclaration($js);
