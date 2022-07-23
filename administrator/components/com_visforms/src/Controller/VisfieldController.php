<?php
/**
 * @author       Aicha Vack
 * @package     Joomla.Administrator
 * @subpackage  com_content
 * @link         https://www.vi-solutions.de
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Visolutions\Component\Visforms\Administrator\Controller;

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

require_once JPATH_ROOT . '/administrator/components/com_visforms/lib/visformsSql.php';

class VisfieldController extends ItemControllerBase
{
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null) {
		parent::__construct($config, $factory, $app, $input);
		$this->registerTask('testSqlStatementSingleResult', 'testSqlStatement');
	}

	public function batch($model = null) {
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
		// set the model
		$model = $this->getModel('Visfield', '', array());
		// preset the redirect
		$this->setRedirect(Route::_('index.php?option=com_visforms&view=visfields' . $this->getRedirectToListAppend(), false));
		return parent::batch($model);
	}
	
	protected function postSaveHook(BaseDatabaseModel $model, $validData = array()) {
		$model->assureCreateDataTableFields();
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$fid = $this->input->getInt('fid', 0);
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&fid=' . $fid.'&extension=com_visforms.visform.'.$fid;
		return $append;
	}
	
	protected function getRedirectToListAppend() {
		$fid = $this->input->getInt('fid', 0);
		$append = '';
		// setup redirect info
		if ($fid != 0) {
			$append .= '&fid=' . $fid;
		}
		parent::getRedirectToListAppend();
		return $append;
	}
	
	protected function allowEdit($data = array(), $key = 'id') {
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$fid = $fid = $this->input->getInt('fid');
		$assetId = 'com_visforms.visform.' . $fid . '.visfield.' . $recordId;
		$user = $this->app->getIdentity();
		$userId = $user->get('id');
		// check general edit permission first
		if ($user->authorise('core.edit', $assetId)) {
			return true;
		}

		// fallback on edit.own
		// first test if the permission is available
		if ($user->authorise('core.edit.own', $assetId)) {
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

	public function testSqlStatement() {
		$data = $this->getAjaxRequestData();
		// test sql with a single result or with an array of results?
        $task = $this->input->getCmd('task', 'testSqlStatement');
        $queryFunction = ($task === 'testSqlStatementSingleResult') ? 'loadResult' : 'loadObjectList';
		if (!$this->checkAjaxSessionToken()) {
			$count = null;
			$message = Text::_("COM_VISFORMS_AJAX_INVALID_TOKEN");
			$result = false;
		}
		else if (!$this->app->getIdentity()->authorise('core.create.sql.statement', 'com_visforms')) {
			$count = null;
			$message = Text::_("COM_VISFORMS_CREATE_SQL_ACL_MISSING");
			$result = false;
		}
		else {
			$sql = json_decode(json_encode($data->statement), true);
			$count = 0;
			try {
				$sqlHelper = new \VisformsSql($sql);
				$items = $sqlHelper->getItemsFromSQL($queryFunction);
				$count = count($items);
				$message = 'success: found ' . $count . ' entries';
				$result = true;
			}
			catch (\Exception $e) {
				$message = $e->getMessage();
				$result = false;
			}
		}

		// return data
		// clean php notices and warnings from output buffer
		ob_clean();
		$response = array("success" => true, 'count' => $count, 'message' => $message, 'result' => $result);
		$document = $this->app->getDocument();
		$document->setMimeEncoding('application/json');
		echo json_encode($response);
		$this->app->close();
	}
}