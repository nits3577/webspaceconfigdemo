<?php
/**
 * vistools controller for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Controller;

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

class VisToolsController extends BaseController
{
    protected $protectedFiles;

    public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null) {
		parent::__construct($config, $factory, $app, $input);
		$this->registerTask('apply', 'save');
        $this->protectedFiles = array ('bootstrapform.css', 'visdata.css', 'visdata.min.css', 'visforms.bootstrap4.css', 'visforms.bootstrap4.min.css',
	        'visforms.min.css', 'viforms.css', 'visforms.default.css', 'visforms.default.min.css', 'visforms.full.bootstrap4.css', 'visformssearchtools.css');
	}

    public function cancel() {
		$this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));
	}

	public function close() {
        $this->setRedirect(Route::_('index.php?option=com_visforms&view=vistools', false));
	}
    
    public function getModel($name = 'Vistools', $prefix = 'Administrator', $config = array()) {
		return parent::getModel($name, $prefix, $config);
	}

	protected function allowEdit() {
		return $this->app->getIdentity()->authorise('core.edit.css', 'com_visforms');
	}


	protected function allowSave() {
		return $this->allowEdit();
	}

	function editCSS() {
        $this->setRedirect("index.php?option=com_visforms&view=vistools&layout=default");
	}

	function save() {
		// check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		
        $data         = $this->input->post->get('jform', array(), 'array');
		$task	 	  = $this->getTask();
		$model		  = $this->getModel();
        $fileName     = $this->input->get('file');
		$explodeArray = explode(':', base64_decode($fileName));

		// access check
		if (!$this->allowSave()) {
			$this->app->enqueueMessage(Text::_('JERROR_SAVE_NOT_PERMITTED'), 'error');
			return false;
		}

		// match the stored id's with the submitted
        if (empty($data['filename'])) {
	        $this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_SOURCE_ID_FILENAME_MISMATCH'), 'error');
            return false;
		}
		elseif ($data['filename'] != end($explodeArray)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_SOURCE_ID_FILENAME_MISMATCH'), 'error');
            return false;
		}
        
        // validate the posted data
		$form = $model->getForm();

		if (!$form) {
			$this->app->enqueueMessage($model->getError(), 'error');
			return false;
		}

        // check for validation errors
        $data = $model->validate($form, $data);
		if ($data === false) {
			// get the validation messages
			$errors	= $model->getErrors();
			// push up to three validation messages out to the user
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof \Exception) {
					$this->app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else {
					$this->app->enqueueMessage($errors[$i], 'warning');
				}
			}
			// redirect back to the edit screen
            $url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $fileName;
			$this->setRedirect(Route::_($url, false));
			return false;
		}
		
		// attempt to save the data
		if (!$model->save($data)) {
			// save the data in the session
			$this->app->setUserState('com_visforms.source.data', $data);
			// redirect back to the edit screen
			$this->app->enqueueMessage(Text::sprintf('JERROR_SAVE_FAILED', $model->getError()), 'warning');
            $url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $fileName;
			$this->setRedirect(Route::_($url, false));
		}
		$this->app->enqueueMessage(Text::_('COM_VISFORMS_FILE_SAVED'));
		
		// redirect the user and adjust session state based on the chosen task
		switch ($task) {
			case 'apply':
				// redirect back to the edit screen
				$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $fileName;
                $this->setRedirect(Route::_($url, false));
                break;
			default:
				// redirect to the list screen
				$this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));
				break;
		}
	}

	public function createFile() {
		$model    = $this->getModel();
		$file     = $this->input->get('file');
		$name     = $this->input->get('name');
		$type     = $this->input->get('type');

		if ($type == 'null') {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_INVALID_FILE_TYPE'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		elseif (!preg_match('/^[a-zA-Z0-9-_]+$/', $name)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_INVALID_FILE_NAME'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		elseif ($model->createFile($name, $type)) {
			$this->setMessage(Text::_('COM_VISFORMS_FILE_CREATE_SUCCESS'));
			$file = urlencode(base64_encode($name . '.' . $type));
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		else {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_FILE_CREATE'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
	}

	public function uploadFile() {
		$model    = $this->getModel();
		$file     = $this->input->get('file');
		$upload   = $this->input->files->get('files');

		if ($return = $model->uploadFile($upload)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_FILE_UPLOAD_SUCCESS') . $upload['name']);
			$redirect = base64_encode($return);
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $redirect;
			$this->setRedirect(Route::_($url, false));
		}
		else {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_FILE_UPLOAD'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
	}

	public function delete(){
		$model = $this->getModel();
		$file  = $this->input->get('file');

		if (in_array(base64_decode(urldecode($file)), $this->protectedFiles)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_OWW_FILE_DELETE'), 'warning');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		elseif ($model->deleteFile($file)) {
			$this->setMessage(Text::_('COM_VISFORMS_FILE_DELETE_SUCCESS'));
			$file = base64_encode('home');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		else {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_FILE_DELETE'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
	}

	public function renameFile() {
		$model   = $this->getModel();
		$file    = $this->input->get('file');
		$newName = $this->input->get('new_name');

		if (in_array(base64_decode(urldecode($file)), $this->protectedFiles)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_RENAME_OWN_FILE'), 'warning');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		elseif (!preg_match('/^[a-zA-Z0-9-_]+$/', $newName)) {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_INVALID_FILE_NAME'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
		elseif ($rename = $model->renameFile($file, $newName)) {
			$this->setMessage(Text::_('COM_VISFORMS_FILE_RENAME_SUCCESS'));
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $rename;
			$this->setRedirect(Route::_($url, false));
		}
		else {
			$this->app->enqueueMessage(Text::_('COM_VISFORMS_ERROR_FILE_RENAME'), 'error');
			$url = 'index.php?option=com_visforms&view=vistools&layout=default&file=' . $file;
			$this->setRedirect(Route::_($url, false));
		}
	}
}