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

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

if (Factory::getApplication()->isClient('site')) {
	Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
}

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.polyfill', array('event'), 'lt IE 9');
HTMLHelper::_('script', 'com_visforms/admin-visformfields-modal.js', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));


// Special case for the search field tooltip.
$searchFilterDesc = $this->filterForm->getFieldAttribute('search', 'description', null, 'filter');
HTMLHelper::_('bootstrap.tooltip', '#filter_search', array('title' => Text::_($searchFilterDesc), 'placement' => 'bottom'));

$linkeditorname = '';
$function	= Factory::getApplication()->input->getCmd('function', 'jSelectVisformfield');
$editor = Factory::getApplication()->input->getCmd('editor', '');

$linkeditorname = '&amp;editor=' . $editor;
if (!empty($editor)) {
    // This view is used also in com_menus. Load the xtd script only if the editor is set!
    Factory::getApplication()->getDocument()->addScriptOptions('xtd-visformfields', array('editor' => $editor));
    $onclick = "jSelectVisformfield";
}

// Add field types that should not be available as placeholder to this list
$hiddenFieldTypes = array('submit', 'reset', 'image', 'fieldsep', 'pagebreak');
$nonFieldPlaceholder = VisformsPlaceholderEntry::getStaticPlaceholderList(); ?>

<form action="<?php echo Route::_('index.php?option=com_visforms&view=visplaceholders&fid=' . Factory::getApplication()->input->getInt('fid', -1) . '&layout=modal&tmpl=component&function='.$function.'&'.Session::getFormToken().'=1'. $linkeditorname);?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<table class="table table-striped table-condensed" id="articleList">
		<caption id="captionTable" class="sr-only">
			<?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
		</caption>
		<thead>
			<tr>
				<th width="1%"class="center nowrap"><?php echo $this->getSortHeader('JGRID_HEADING_ID', 'a.id'); ?></th>
				<th class="center nowrap"><?php echo $this->getSortHeader('JGLOBAL_TITLE', 'a.label'); ?></th>
                <th width="10%" class="nowrap center"><?php echo $this->getSortHeader('COM_VISFORMS_TYPE', 'a.typefield'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody> <?php
		foreach ($nonFieldPlaceholder as $placeholder => $title) { ?>
            <tr>
                <td class="center"></td>
                <td class="center">
                    <a href="javascript:void(0)" onclick="if (window.parent) window.<?php echo $this->escape($function);?>('<?php echo $this->escape(addslashes($placeholder)); ?>','<?php echo $this->escape(addslashes($editor)); ?>');">
						<?php echo $this->escape(Text::_($title)); ?></a>
                </td>
                <td class="center nowrap"><?php echo Text::_('COM_VISFORMS_OVERHEAD_PLACEHOLER'); ?></td>
            </tr> <?php
		}
		foreach ($this->items as $i => $item) {
			if (!(in_array($item->typefield, $hiddenFieldTypes)) && $item->published) {
				$placeholder = new stdClass();
				$placeholder->counter = $i;
				$placeholder->id = $item->id;
				$placeholder->name = $item->name;
				$placeholder->label = $item->label;
				$placeholder->function = $function;
				$placeholder->typefield = $item->typefield;
				$placeholder->editor = $editor;
				echo LayoutHelper::render('modal.visplaceholders.placeholder', array('view' => $this, 'placeholder' => $placeholder));
				$params = VisformsPlaceholderEntry::getParamStringsArrayForType($item->typefield);
				if (!empty($params)) {
					foreach ($params as $pParamValue => $pParamLabel) {
						$placeholder->name = $item->name . '|' . $pParamValue;
						$placeholder->label = $item->label . ' (' . $pParamLabel . ')';
						echo LayoutHelper::render('modal.visplaceholders.placeholder', array('view' => $this, 'placeholder' => $placeholder));
					}
				}
				unset($placeholder);
				unset($params);
			}
		} ?>
		</tbody>
	</table>

	<div><?php
        $layout = new JLayoutFile('div.form_hidden_inputs');
        echo $layout->render(); ?>
	</div>
</form>
