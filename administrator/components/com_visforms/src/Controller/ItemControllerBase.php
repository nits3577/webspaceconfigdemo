<?php
/**
 * Itemlist controller for Visforms
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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;

class ItemControllerBase extends FormController
{
	protected $app;
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null, FormFactoryInterface $formFactory = null) {
		parent::__construct($config, $factory, $app, $input);
		$this->app = Factory::getApplication();
		//$this->registerTask('showStepBadges', 'hideStepBadges');
	}

	protected function getAjaxRequestData() {
		// get the data
		$input = $this->app->input;
		$json = $input->get('data', '', 'raw');
		$data = json_decode($json);
		return $data;
	}

	protected function checkAjaxSessionToken() {
		$data = $this->getAjaxRequestData();
		$token = Session::getFormToken();
		if ((!isset($data->$token)) || !((int) $data->$token === 1)) {
			return false;
		}
		return true;
	}
	// stored for later use
	/*public function hideStepBadges() {
		if ('hideStepBadges' === $this->getTask()) {
			$this->storeParam('hideStepBadges', 1);
		}
		else {
			$this->storeParam('hideStepBadges', 0);
		}
		$this->setRedirect(Route::_('index.php?option=com_visforms&view=visfields' . $this->getRedirectToListAppend(), false));
	}*/

	/*protected function storeParam($name, $value) {
		$component = ComponentHelper::getComponent('com_visforms');
		$component->params->set($name, $value);
		$componentId = $component->id;
		$table = Table::getInstance('extension');
		$table->load($componentId);
		$table->bind(array('params' => $component->params->toString()));
		if (!$table->check()) {
			Factory::getApplication()->enqueueMessage('Invalid params', 'error');
			return false;
		}
		if (!$table->store()) {
			Factory::getApplication()->enqueueMessage('Problems saving params', 'error');
			return false;
		}
		return true;
	}*/
}