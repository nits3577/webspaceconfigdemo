<?php 
/**
 * $this->viewName default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
$token = Session::getFormToken();
$fnroute = 'Joomla\CMS\Router\Route::_';
$js = <<<JS
Joomla.submitbutton = function(pressbutton) {
    if (pressbutton == '{$this->viewName}.export') {
    // if data sets are checked we submit id's of check data sets as array cid[] and uncheck the boxes in the form, because the page is not reloaded on export
            var form = document.getElementById('adminForm');
            var stub = 'cb';
            var cid  = '';
            if (form) {
                var j = 0;
                for (var i = 0, n = form.elements.length; i < n; i++) {
                    var e = form.elements[i];
                    if (e.type == 'checkbox') {
                        if (e.id.indexOf(stub) == 0) {
                            if (e.checked == true) {
                                cid += '&cid[' + j + ']=' + e.value;
                                j++;
                                e.checked = false;
                            }
                        }
                    }
                }
            }
            window.location = '{$this->baseUrl}&view={$this->viewName}&fid={$this->fid}&task={$this->viewName}.export' + cid + '&{$token}=1'
        }
		else {
			Joomla.submitform( pressbutton );
		}
	}
JS;
$this->document->addScriptDeclaration($js);
?>

<form action="<?php echo Route::_("$this->baseUrl&view=$this->viewName&fid=$this->fid");?>" method="post" name="adminForm" id="adminForm" ><div class="row"><?php
		if (!empty( $this->sidebar)) { ?>
            <div id="j-sidebar-container" class="col-md-3 col-xl-2">
			<?php echo $this->sidebar; ?>
            </div><?php } ?>
        <div class="<?php if (!empty($this->sidebar)) {echo 'col-md-9 col-xl-10'; } else { echo 'col-12'; } ?>">
            <div id="j-main-container" class="j-main-container"><?php
            // search tools bar
            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
            <div class="clr"></div>
            <table class="table table-striped" id="articleList">
                <caption id="captionTable" class="sr-only">
                    <?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
                </caption>
            <thead><tr>
                <th width="3%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_ID', 'a.id'); ?></th>
                <th width="3%" class="nowrap center"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                <th width="3%"><?php echo $this->getSortHeader('COM_VISFORMS_PUBLISHED', 'a.published'); ?></th>
                <th width="3%"><?php echo $this->getSortHeader('COM_VISFORMS_MODIFIED', 'a.ismfd') ; ?></th>
                <th width="4%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_CREATED_BY', 'a.created_by'); ?></th><?php
                $k = 0;
                $n=count( $this->fields );
                for ($i=0; $i < $n; $i++) {
                    $width = 30;
                    if ($n > 0) {
                        $width = floor(89/$n);
                    }
                    $rowField = $this->fields[$i];
                    if (!($rowField->showFieldInDataView === false)) {
                        if (empty($rowField->unSortable)) { ?>
                            <th width="<?php echo $width ?>%" class="nowrap"><?php
                            echo $this->getSortHeader($rowField->name, "a.F$rowField->id"); ?>
                            </th><?php
                        } else { ?>
                            <th width="<?php echo $width ?>%" class="nowrap"><?php
                            echo $rowField->name; ?>
                            </th><?php
                        }
                    }
                } ?>
                <th width="4%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_IP', 'a.ipaddress'); ?></th>
                <th width="8%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_DATE', 'a.created'); ?></th>
                <th width="8%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_MODIFIED_AT', 'a.modified'); ?></th>
            </tr></thead><?php
            if (is_array($this->items)) {
                foreach ($this->items as $i => $item) {
                    $item->max_ordering = 0; //??
                    $canEditState = $this->canDo->get('core.edit.state');
                    if ($canEditState) {
                        $published	= HTMLHelper::_('jgrid.published', $item->published, $i, "$this->viewName.", true );
                    }
                    else {
                        $published	= HTMLHelper::_('jgrid.published', $item->published, $i, "$this->viewName.", false );
                    }
                    $checked     = HTMLHelper::_('grid.id',   $i, $item->id );
                    $link        = Route::_( "$this->baseUrl&task=visdata.edit&fid=$this->fid&id=$item->id");
                    $authoriseId = "$this->authoriseName.$this->fid";
                    $canCheckin	 = $this->user->authorise('core.manage',        $this->componentName) || $item->checked_out == $this->userId || $item->checked_out == 0;
                    $canEdit	 = $this->user->authorise('core.edit.data',     $authoriseId);
                    $canEditOwn	 = $this->user->authorise('core.edit.own.data', $authoriseId) && $item->created_by == $this->userId;
                    $canEditData  = $this->canDo->get('core.edit.data');
                    $modified    = ($item->ismfd && $canEditData)
                        ? HTMLHelper::_('jgrid.action', $i, "$this->viewName.reset" , $prefix = '', $text = '', $active_title = 'COM_VISFORMS_RESET_DATA', $inactive_title = '',
                                    $tip = true, $active_class = 'undo', $inactive_class = '', $enabled = true, $translate = true, $checkbox = 'cb')
                        : (($item->ismfd) ? Text::_('JYES') : Text::_('JNO')); ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="has-context">
                            <div class="center"><?php
                                if ($canEdit || $canEditOwn) {
                                    echo "<a href=\"" . $link . "\">" . $item->id . "</a>";
                                }
                                else {
                                    echo $item->id;
                                }
                                if ($item->checked_out) {
                                    echo HTMLHelper::_('jgrid.checkedout', $i, $this->user->name, $item->checked_out_time, "$this->viewName.", $canCheckin);
                                } ?>
                            </div>
                        </td>
                        <td class="center"><?php echo $checked; ?></td>
                        <td align="center"><?php echo $published;?></td>
                        <td class="center"><?php echo $modified;?></td>
                        <td class="center"><?php echo $item->created_by;?></td><?php
                        $z = count( $this->fields );
                        for ($j=0; $j < $z; $j++) {
                            $rowField = $this->fields[$j];
                            if (!($rowField->showFieldInDataView === false)) {
                                $prop="F".$rowField->id;
                                if (isset($item->$prop) == false) {
                                    $prop=$rowField->name;
                                }

                                if (isset($item->$prop)) {
                                    $texts = $item->$prop;
                                }
                                else {
                                    $texts = "";
                                }

                                if ($rowField->typefield == 'email') {
                                    $linkField = "mailto:".$texts;
                                    echo "<td><a href=\"".$linkField."\">".$texts."</a></td>";
                                }
                                else if (isset($rowField->defaultvalue['f_url_urlaslink']) && ($rowField->defaultvalue['f_url_urlaslink'] == true) && ($rowField->typefield == 'url') && ($texts != "")) {
                                    echo "<td><a href=\"".$texts."\" target=\"_blank\">".$texts."</a></td>";
                                }
                                else if ($rowField->typefield == 'file') {
                                    if (!empty($texts)) {
                                        //info about uploaded files are stored in a JSON Object. Earlier versions just have a string.
                                        $texts = HTMLHelper::_('visforms.getUploadFileLink', $texts);
                                    }
                                    echo "<td>". $texts . "</td>";
                                }
                                else if ($rowField->typefield == 'signature') {
                                    $layout             = new JLayoutFile('visforms.datas.fields.signature', null);
                                    $layout->setOptions(array('component' => 'com_visforms'));
                                    $texts = $layout->render(array('field' => $rowField, 'data' => $texts, 'maxWidth' => 200));
                                    echo "<td>". $texts . "</td>";
                                }
                                else {
                                    if (StringHelper::strlen($texts) > 255) {
                                        $texts = StringHelper::substr($texts,0,255)."...";
                                    }
                                    echo "<td>" . $texts . "</td>";
                                }
                            }
                        } ?>
                        <td><?php echo $item->ipaddress; ?></td>
                        <td><?php echo VisformsHelper::getFormattedServerDateTime($item->created); ?></td>
                    <td class="center"><?php echo VisformsHelper::getFormattedServerDateTime($item->modified);?></td>
                    </tr><?php
                }
            } ?>
            </table><?php
            echo $this->pagination->getListFooter();
            $layout = new JLayoutFile('div.form_hidden_inputs');
            echo $layout->render(); ?>
            </div>
        </div>
    </div>
</form>