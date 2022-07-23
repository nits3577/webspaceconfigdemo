<?php
/**
 * Vistools view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Vistools;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Vistools view
 *
 * @package    Visforms
 * @subpackage Components
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * For loading extension state
	 */
	protected $state;

	/**
	 * For loading extension details
	 */
	protected $extension;

	/**
	 * For loading the source form
	 */
	protected $form;

	/**
	 * For loading source file contents
	 */
	protected $source;

	/**
	 * Encrypted file path
	 */
	protected $file;

	/**
	 * Name of the present file
	 */
	protected $fileName;

	/**
	 * Type of the file - image, source, font
	 */
	protected $type;

	/**
	 * A nested array containing lst of files and folders
	 */
	protected $files;

	/**
	 * Execute and display a edit css viwe.
	 *
	 * @param   string  $tpl  The name of the extension ; automatically searches through the css folder.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
        // add apply and save button
		$app            = Factory::getApplication();
		$this->file     = $app->input->get('file');
		$this->fileName = base64_decode($this->file);
		$explodeArray   = explode('.', $this->fileName);
		$ext            = end($explodeArray);
		$this->files    = $this->get('Files');
		$this->state    = $this->get('State');

		$canDo    = \VisformsHelper::getActions();
        if (!($canDo->get('core.edit.css'))) {
            Factory::getApplication()->redirect('index.php?option=com_visforms&view=visforms', Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
        }

		$sourceTypes  = array('css');
		if (in_array($ext, $sourceTypes)) {
			$this->form   = $this->get('Form');
			$this->form->setFieldAttribute('source', 'syntax', $ext);
			$this->source = $this->get('Source');
			$this->type   = 'file';
		}
		else {
			$this->type = 'home';
		}

		// check for errors
		if (count($errors = $this->get('Errors'))) {
			$app->enqueueMessage(implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
        \VisformsHelper::addCommonViewStyleCss();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$app = Factory::getApplication();
		$app->input->set('hidemainmenu', true);
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_VISFORMS_EDIT_CSS_BUTTON_TEXT'), 'visform');

		// add a new file button
        $toolbar->popupButton('file')
            ->text('COM_VISFORMS_BUTTON_FILE')
            ->icon('icon-file')
            ->selector('fileModal');
        if (!($this->type == 'home')) {
            // add rename file button
            $toolbar->popupButton('rename')
                ->text('COM_VISFORMS_BUTTON_RENAME_FILE')
                ->icon('icon-refresh')
                ->selector('renameModal');

            // add delete file button
            $toolbar->popupButton('delete')
                ->text('COM_VISFORMS_BUTTON_DELETE_FILE')
                ->icon('icon-remove')
                ->selector('deleteModal');
        }
        
		// add apply and save button
		if ($this->type == 'file') {
            ToolbarHelper::apply('vistools.apply');
            ToolbarHelper::save('vistools.save');
		}

		if ($this->type == 'home') {
            $toolbar->cancel('vistools.cancel', 'JTOOLBAR_CLOSE');
		}
		else {
            $toolbar->cancel('vistools.close', 'COM_VISFORMS_BUTTON_CLOSE_FILE');
		}
	}

	/**
	 * Method for creating the collapsible tree.
	 *
	 * @param   array  $array  The value of the present node for recursion
	 *
	 * @return  string
	 *
	 * @note    Uses recursion
	 * @since   3.2
	 */
	protected function directoryTree($array)
	{
		$temp        = $this->files;
		$this->files = $array;
		$txt         = $this->loadExtension('tree');
		$this->files = $temp;

		return $txt;
	}
}
