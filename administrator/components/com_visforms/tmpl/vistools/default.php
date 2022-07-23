<?php
/**
 * Vistools editcss view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<div class="row">
	<div class="col-md-12"><?php
        if($this->type == 'file') { ?>
			<p class="lead"><?php echo Text::sprintf('COM_VISFORMS_CSS_FILENAME', $this->source->filename); ?></p><?php
        } ?>
	</div>
</div>
<div class="row">
	<div id="treeholder" class="col-md-3 tree-holder"><?php echo $this->loadTemplate('tree');?></div>
	<div class="col-lg-9"><?php
        if($this->type == 'home') { ?>
			<form action="<?php echo Route::_('index.php?option=com_visforms&view=vistools&layout=default&file=' . $this->file); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal"><?php
                $layout = new JLayoutFile('div.form_hidden_inputs');
                echo $layout->render(); ?>
			</form><?php
        }
        if($this->type == 'file'){ ?>
			<form action="<?php echo Route::_('index.php?option=com_visforms&view=vistools&layout=default&file=' . $this->file); ?>" method="post" name="adminForm" id="adminForm">
				<div class="editor-border"><?php echo $this->form->getInput('source'); ?></div><?php
                $layout = new JLayoutFile('div.form_hidden_inputs');
                echo $layout->render();
                echo $this->form->getInput('filename'); ?>
			</form><?php
        } ?>
	</div>
</div><?php
if ($this->type != 'home') {
    // Delete Modal
    HTMLHelper::_('bootstrap.modal', '#deleteModal');
	$deleteModalData = array(
		'selector' => 'deleteModal',
		'params'   => array(
			'title'  => Text::_('COM_VISFORMS_ARE_YOU_SURE'),
			'footer' => $this->loadTemplate('modal_delete_footer')
		),
		'body' => $this->loadTemplate('modal_delete_body')
	);
	echo LayoutHelper::render('libraries.html.bootstrap.modal.main', $deleteModalData); ?><?php
}
// File Modal
HTMLHelper::_('bootstrap.modal', '#fileModal');
$fileModalData = array(
	'selector' => 'fileModal',
	'params'   => array(
		'title'      => Text::_('COM_VISFORMS_NEW_FILE_HEADER'),
		'footer'     => $this->loadTemplate('modal_file_footer'),
		'height'     => '400px',
		'width'      => '800px',
		'bodyHeight' => 50,
		'modalWidth' => 60,
	),
	'body' => $this->loadTemplate('modal_file_body')
);
echo LayoutHelper::render('libraries.html.bootstrap.modal.main', $fileModalData);
if ($this->type != 'home') {
    // Rename Modal
    HTMLHelper::_('bootstrap.modal', '#renameModal');
	$renameModalData = array(
		'selector' => 'renameModal',
		'params'   => array(
			'title'  => Text::sprintf('COM_VISFORMS_RENAME_FILE', $this->fileName),
			'footer' => $this->loadTemplate('modal_rename_footer')
		),
		'body' => $this->loadTemplate('modal_rename_body')
	); ?>
    <form action="<?php echo Route::_('index.php?option=com_visforms&view=vistools&layout=default&task=vistools.renameFile&file=' . $this->file); ?>" method="post">
		<?php echo LayoutHelper::render('libraries.html.bootstrap.modal.main', $renameModalData); ?>
		<?php echo HTMLHelper::_('form.token'); ?>
    </form><?php
} ?>