<?php

/**
 * visdata conroller for visforms
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

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

class VisdataController extends FormController
{
	function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null, FormFactoryInterface $formFactory = null) {
		parent::__construct($config, $factory, $app, $input);
        $this->text_prefix = 'COM_VISFORMS_DATA';
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$fid = Factory::getApplication()->input->getInt('fid', 0);
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&fid=' . $fid;
		return $append;
	}
	
	protected function getRedirectToListAppend() {
		$fid = Factory::getApplication()->input->getIntr('fid', 0);
		$append = '';
		// Setup redirect info.
		if ($fid != 0) {
			$append .= '&fid=' . $fid;
		}
		parent::getRedirectToListAppend();
		return $append;
	}
    
    protected function allowEdit($data = array(), $key = 'id') {
		// initialise variables
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $fid = Factory::getApplication()->input->getInt('fid', 0);
		$assetId = 'com_visforms.visform.' . $fid;
		$user = $this->app->getIdentity();
		$userId = $user->get('id');
		// check general edit permission first
		if ($user->authorise('core.edit.data', $assetId)) {
			return true;
		}

		// fallback on edit.own
		// first test if the permission is available
		if ($user->authorise('core.edit.own.data', $assetId)) {
			// now test the owner is the user
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// need to do a lookup from the model
				$record = $this->getModel()->getItem($recordId);
				if (empty($record)) {
					return false;
				}
				$ownerId = $record->created_by;
			}

			// if the owner matches 'me' then do the test
			if ($ownerId == $userId) {
				return true;
			}
		}
		
		return false;
	}
    
    public function save($key = null, $urlVar = null) {
		// check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$model   = $this->getModel();
		$table   = $model->getTable();
		$data    = $this->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$cansavemodifiedinfo = property_exists($table, 'modified');
		$context = "$this->option.edit.$this->context";
		$task    = $this->getTask();

		// determine the name of the primary key for the data
		if (empty($key)) {
			$key = $table->getKeyName();
		}

		// Tto avoid data collisions the urlVar may be different from the primary key
		if (empty($urlVar)) {
			$urlVar = $key;
		}

		$recordId = $this->input->getInt($urlVar);
		// populate the row id from the session
		$data[$key] = $recordId;
		// access check
		if (!$this->allowSave($data, $key)) {
			$this->setMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
			return false;
		}
        
        $data = $model->uploadFiles($data);
        // pre process Database values
        $data = $model->processDbValues($data);
        // create a replica of the originals data in visforms_n_save table
        $isMfd = $model->copyOrgData($data);
        if ($isMfd) {
            $data['ismfd'] = true;
        }
        $data = $model->deleteFiles($data);

	    if ($cansavemodifiedinfo) {

		    $date = Factory::getDate();
		    $data['modified'] = $date->toSql();

		    $user = $this->app->getIdentity();
		    $data['modified_by'] = $user->get('id');
	    }
	    unset($data['created']);

		// attempt to save the data
		if (!$model->save($data)) {
			// save the data in the session
			$this->app->setUserState($context . '.data', $data);
			// redirect back to the edit screen
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar), false));
			return false;
		}

		// save succeeded, so check-in the record
		if ($checkin && $model->checkin($data[$key]) === false) {
			// save the data in the session
			$this->app->setUserState($context . '.data', $data);
			// check-in failed, so go back to the record and display a notice
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'error');
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar), false));
			return false;
		}
		$this->setMessage(Text::_('JLIB_APPLICATION_SAVE_SUCCESS'));

		// redirect the user and adjust session state based on the chosen task
		switch ($task) {
			case 'apply':
				// set the record data in the session
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$this->app->setUserState($context . '.data', null);
				$model->checkout($recordId);
				// redirect back to the edit screen
				$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId, $urlVar), false));
				break;

			default:
				// clear the record id and data from the session
				$this->releaseEditId($context, $recordId);
				$this->app->setUserState($context . '.data', null);
				// redirect to the list screen
				$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}

		// invoke the postSave method to allow for the child class to access the model
		$this->postSaveHook($model, $data);

		return true;
	}
    
    public function reset() {
        $model = $this->getModel();
        $recordId = $this->input->get('id', 0, 'int');
        $model->restoreToUserInputs($recordId);
        $model->deleteOrgData($recordId);
        $this->setMessage(Text::_('COM_VISFORMS_DATA_USER_INPUT_RESTORED'));
        
        // redirect back to the list view
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
    }
}