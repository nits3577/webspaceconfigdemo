<?php
/**
 * viscpanel default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

//no direct access
 defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.framework');
$issub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
$component = JComponentHelper::getComponent('com_visforms');
$dlid = $component->params->get('downloadid', '');
$demoFormInstalled = $component->params->get('demoFormInstalled', '');
$extensiontypetag = ($issub) ? 'COM_VISFORMS_SUBSCRIPTION' : 'COM_VISFORMS_PAYED_EXTENSION';
?>

<div class="row"><?php
    if (!empty( $this->sidebar)) { ?>
    <div id="j-sidebar-container" class="col-md-3 col-xl-2">
    <?php echo $this->sidebar; ?>
    </div><?php } ?>
    <div class="<?php if (!empty($this->sidebar)) {echo 'col-md-9 col-xl-10'; } else { echo 'col-12'; } ?>">
        <div id="j-main-container" class="j-main-container">
            <div id="vfcpanel">
    <?php  if (isset($this->update_message)) {echo $this->update_message;} ?>
    <div class="row">
        <div class="col-lg-6 mt-3">
            <h1><?php echo Text::_('COM_VISFORMS_SUBMENU_CPANEL_LABEL'); ?></h1>
        </div>
    </div>
            <div class="row">
                <div class="col-lg-6 mt-3">
            <h3><?php echo Text::_('COM_VISFORMS_CPANEL_OPERATIIONS_HEADER'); ?></h3>
            <div class="clearfix">
                <div class="cpanel">
                    <a href="index.php?option=com_visforms&amp;view=visforms"><i class="icon-stack"></i><span><?php echo Text::_('COM_VISFORMS_SUBMENU_FORMS'); ?></span></a>
                </div>
                <?php if ($this->canDo->get('core.create')) : ?>
                <div class="cpanel">
                    <a href="index.php?option=com_visforms&amp;task=visform.add" ><i class="icon-file-plus"></i><span><?php echo Text::_('COM_VISFORMS_FORM_NEW'); //echo (new JLayoutFile('div.quickstart_help_element'))->render(array('step' => 1, 'tag' => 'span'));?></span></a>
                </div>
                <?php endif; ?>
                <?php if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_visforms')) : ?>
                <div class="cpanel">
                    <a href="<?php echo $this->preferencesLink; ?>" ><i class="icon-options"></i><span><?php echo Text::_('JTOOLBAR_OPTIONS'); ?></span></a>
                </div>
                <?php endif; ?>
                <?php if ($this->canDo->get('core.edit.css')) : ?>
                <div class="cpanel">
                            <a href="index.php?option=com_visforms&amp;task=viscpanel.edit_css" ><i class="icon-pencil"></i><span><?php echo Text::_('COM_VISFORMS_EDIT_CSS'); ?></span></a>
                </div>
                <?php endif; ?>
            </div>

        </div>
                <div class="col-lg-6 mt-3">
            <h3><?php echo Text::_('COM_VISFORMS_CPANEL_INFO_SUPPORT_HEADER'); ?></h3>
            <div class="clearfix">
                <div class="cpanel">
                    <a href="<?php echo $this->documentationLink; ?>" target="_blank"><i class="icon-info-circle"></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_DOCUMENTATION_BUTTON_LABEL');?></span></a>
                </div>
                <div class="cpanel">
                    <a href="<?php echo $this->forumLink; ?>" target="_blank"><i class="icon-question-circle"></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_FORUM_BUTTON_LABEL');?></span></a>
                </div>
            </div>

        </div>
    </div>
            <div class="row">
                <div class="col-lg-6 mt-3">
            <?php if ((empty($issub))) : ?>
            <h3><?php echo Text::_('COM_VISFORMS_CPANEL_ADDITIONAL_FEATURE_HEADER'); ?></h3>
            <div id="subscribe" class="alert alert-block alert-info">
                <p class="text-center"><?php echo Text::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_TEXT'); ?></p>
                <p class="text-center visible-desktop"><?php echo Text::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_LIST'); ?></p>
                        <p class="text-center" style="margin-top: 20px"><a href="<?php echo $this->versionCompareLink; ?>" target="_blank" class="btn btn-secondary btn-sm"><?php echo Text::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_COMPARE_VERSIONS'); ?></a>
                        <a href="<?php echo $this->buySubsLink; ?>" target="_blank" class="btn btn-secondary btn-sm"><?php echo Text::_('COM_VISFORMS_CPANAL_ADDITIONAL_FEATURE_BUY_SUBSCRIPTION'); ?></a></p>
            </div>
            <?php endif; ?>
            <?php if ((!empty($issub))) : ?>
                <h3><?php echo Text::sprintf('COM_VISFORMS_CPANEL_MANAGE_SUBSCRIPTION_HEADER', Text::_($extensiontypetag)); ?></h3>
                <div class="clearfix">
                    <div class="cpanel">
                        <a href="#downloadid" data-bs-toggle="modal"><i class="icon-unlock "></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_UPDATE_BUTTON_LABEL'); ?></span></a>
                    </div>
                    <div class="cpanel">
                        <a href=<?php echo $this->dlidInfoLink; ?>" target="_blank"><i class="icon-eye-open "></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_MANAGE_BUTTON_LABEL'); ?></span></a>
                    </div><?php
                    // todo enable if
                    if (empty($demoFormInstalled) && Factory::getApplication()->getIdentity()->authorise('core.create', 'com_visforms')) { ?>
                        <div class="cpanel">
                        <a href="<?php echo $this->installPdfDemoFormLink; ?>"><i class="icon-drawer"></i><span
                                    style="margin-top:0;"><?php echo Text::_('COM_VISFORMS_CPANEL_INSTALL_PDF_DEMO_LABEL'); ?></span></a>
                        </div><?php
                    } ?>
                </div>
            <?php endif; ?>
        </div>
                <div class="col-lg-6 mt-3">

            <h3><?php echo Text::_('COM_VISFORMS_CPANEL_CONTRIBUTE_HEADER'); ?></h3>
            <div class="clearfix">
                <div class="cpanel">
                    <a href="http://extensions.joomla.org/extensions/contacts-and-feedback/forms/23899" target="_blank"><i class="icon-star"></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_REVIEW_BUTTON_LABEL');?></span></a>
                </div>
                <?php if (empty($issub)) : ?>
                <div class="cpanel">
                    <a href="<?php echo $this->donateLink; ?>" target="_blank"><i class="icon-credit"></i><span><?php echo Text::_('COM_VISFORMS_CPANEL_DONATE_BUTTON_LABEL');?></span></a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
            <div class="row">
                <div class="col-md-12 mt-3">
                <h3><?php echo Text::_('COM_VISFORMS_HELP_GETTING_STARTED_HEADER'); ?></h3>
                <div class="accordion" id="first-steps">
                        <div class="card">
                            <div class="card-header">
                                <a data-bs-toggle="collapse" data-bs-parent="#first-steps" href="#createform">
                                <?php echo Text::_('COM_VISFORMS_CREATE_FORM'); ?>
                            </a>
                        </div>
                            <div id="createform" class="collapse">
                                <div class="card-block">
                                <ul>
                                    <li><?php echo Text::_('COM_VISFORMS_CREATE_FORM_STEP1'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_CREATE_FORM_STEP2'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_CREATE_FORM_STEP3'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="card">
                            <div class="card-header">
                                <a data-bs-toggle="collapse" data-bs-parent="#first-steps" href="#addfields">
                                <?php echo Text::_('COM_VISFORMS_ADD_FIELDS'); ?>
                            </a>
                        </div>
                            <div id="addfields" class=" collapse">
                                <div class="card-block">
                                <ul>
                                    <li><?php echo Text::_('COM_VISFORMS_ADD_FIELDS_STEP1'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_ADD_FIELDS_STEP2'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_ADD_FIELDS_STEP3'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_ADD_FIELDS_STEP4'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="card">
                            <div class="card-header">
                                <a data-bs-toggle="collapse" data-bs-parent="#first-steps" href="#addsubmit">
                                <?php echo Text::_('COM_VISFORMS_ADD_SUBMIT_BUTTON'); ?>
                            </a>
                        </div>
                            <div id="addsubmit" class="collapse">
                                <div class="card-block">
                                <ul>
                                    <li><?php echo Text::_('COM_VISFORMS_ADD_SUBMIT_BUTTON_STEP1'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="card">
                            <div class="card-header">
                                <a data-bs-toggle="collapse" data-bs-parent="#first-steps" href="#createmenu">
                                <?php echo Text::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM'); ?>
                            </a>
                        </div>
                            <div id="createmenu" class=" collapse">
                                <div class="card-block">
                                <ul>
                                    <li><?php echo Text::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM_STEP1'); ?></li>
                                    <li><?php echo Text::_('COM_VISFORMS_FIRST_STEPS_ADD_MENU_ITEM_STEP2'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
            <div class="row">
                <div class="col-md-12 mt-3">
            <h3><?php echo Text::_('COM_VISFORMS_CPANEL_TRANSLATIONS'); ?></h3>
            <p>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="cs-cz"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/cs_cz.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="da-dk"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/da_dk.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="de-at"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/de_at.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="de-ch"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/de_ch.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="de-de"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/de_de.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="de-li"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/de_li.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="de-lu"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/de_lu.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="el-gr"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/el_gr.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="es-es"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/es_es.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="fr-fr"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/fr_fr.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="he-il"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/he_il.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="hu-hu"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/hu_hu.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="ja-jp"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/ja_jp.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="lt-lt"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/lt_lt.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="nl-nl"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/nl_nl.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="pl-pl"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/pl_pl.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="pt-br"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/pt_br.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="ru-ru"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/ru_ru.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="sk-sk"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/sk_sk.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="tr-tr"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/tr_tr.gif"/></a>
                <a href="<?php echo $this->translationsLink; ?>" target="_blank" title="sr-yu"><img class="img-bordered" src="<?php echo Uri::root(); ?>/media/com_visforms/img/sr_yu.gif"/></a>
            </p>
        </div>
    </div>
    <?php echo HTMLHelper::_('visforms.creditsBackend'); ?>




    <div id="downloadid" class="joomla-modal modal fade" tabindex="-1" role="dialog" aria-labelledby="downloadid" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form class="form-horizontal" action="<?php echo Route::_($this->dlidFormLink); ?>" method="post" style="padding-bottom: 0; margin-bottom:0">
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo Text::sprintf('COM_VISFORMS_CPANEL_MODAL_UPDATE_HEADER', Text::_($extensiontypetag));?></h3>
                    <button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="<?php echo Text::_('COM_VISFORMS_CLOSE'); ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">

                            <label class="form-label col-4" style="width: 160px; text-align: right;"><?php echo Text::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LABEL');?></label>

                        <div class="col-8">
                            <input class="form-control" name="downloadid" type="text" value="<?php echo $dlid; ?>" /><small class="text-muted"><?php echo Text::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_DESC'); ?></small>
                        </div>
                    </div>
                    <div class="accordion" id="dlid">
                        <div class="card">
                            <div class="card-header">
                                <a data-bs-toggle="collapse" data-bs-parent="#dlid" href="#dlid-info">
                                    <?php echo Text::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_HEADER'); ?>
                                </a>
                            </div>
                            <div id="dlid-info" class="collapse">
                                <div class="card-block">
                                    <p><?php echo Text::sprintf('COM_VISFORMS_DOWNLOAD_ID_DESC', Text::_($extensiontypetag), Text::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LINK_TEXT'), Text::_($extensiontypetag));?></p>
                                    <p><a href="<?php echo $this->dlidInfoLink; ?>" target="_blank"><?php echo Text::_('COM_VISFORMS_FIELD_DOWNLOAD_ID_LINK_TEXT'); ?></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: left;">
                    <input type="submit" class="btn btn-success" value="Submit" />
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('COM_VISFORMS_CLOSE'); ?></button>
                </div>
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

