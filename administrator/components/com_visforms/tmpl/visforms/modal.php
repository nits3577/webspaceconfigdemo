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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

if (Factory::getApplication()->isClient('site')) {
	Session::checkToken() or die(Text::_('JINVALID_TOKEN'));
}

$function	= Factory::getApplication()->input->getCmd('function', 'jSelectVisforms');
?>
<div class="container-popup">
    <form action="<?php echo Route::_('index.php?option=com_visforms&view=visforms&layout=modal&tmpl=component&function='.$function.'&'.Session::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	    <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>


        <table class="table table-striped table-condensed" id="articleList">
            <caption id="captionTable" class="sr-only">
		        <?php echo Text::_('COM_CONTENT_ARTICLES_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
            </caption>
            <thead>
                <tr>
                    <th class="center nowrap">
                    <?php echo $this->getSortHeader('COM_VISFORMS_TITLE', 'a.title'); ?>
                    </th>
                    <th width="20%" class="nowrap center">
	                    <?php echo $this->getSortHeader('JGRID_HEADING_ACCESS', 'access_level'); ?>
                    </th>
                    <th width="10%" class="nowrap center">
	                    <?php echo $this->getSortHeader('JGRID_HEADING_LANGUAGE', 'language'); ?>
                    </th>
                    <th width="10%" class="center nowrap">
	                    <?php echo $this->getSortHeader('COM_VISFORMS_DATE', 'a.created'); ?>
                    </th>
                    <th width="5%"class="center nowrap">
	                    <?php echo $this->getSortHeader('COM_VISFORMS_ID', 'a.id'); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="15">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">
                            <?php echo $this->escape($item->title); ?></a>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->access_level); ?>
                    </td>
                    <td class="center">
                        <?php if ($item->language=='*'):?>
                            <?php echo Text::alt('JALL', 'language'); ?>
                        <?php else:?>
                            <?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
                        <?php endif;?>
                    </td>
                    <td class="center nowrap">
                        <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                    </td>
                    <td class="center">
                        <?php echo (int) $item->id; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div>
            <?php
            $layout = new JLayoutFile('div.form_hidden_inputs');
            echo $layout->render(); ?>
        </div>
    </form>
</div>
