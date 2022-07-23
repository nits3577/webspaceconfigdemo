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
namespace Visolutions\Component\Visforms\Site\Controller;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\HTML\HTMLHelper;

class DisplayController extends BaseController
{

	public function __construct(array $config, MVCFactoryInterface $factory = null, $app = null, $input = null) {
		parent::__construct($config, $factory, $app, $input);
	}

	public function display($cachable = false, $urlparams = false) {
		$vName = $this->input->get('view', 'visforms');
		$this->input->set('view', $vName);
		if ($vName == 'visforms') {
			$app = Factory::getApplication();
			$layout = $this->input->get('layout', 'default');
			$task = $this->input->getCmd('task');

			if ($layout == 'default' && !(isset($task))) {
				$model = $this->getModel('visforms');
				$model->addHits();
			}
			if ($layout == 'message') {
				//something to do?
			}
		}
		if ($vName == 'visformsdata') {
			$app = Factory::getApplication();
			$cid = $this->input->getInt('cid');
			$this->input->set('view', 'visformsdata');
			$dataViewMenuItemExists = HTMLHelper::_('visforms.checkDataViewMenuItemExists', $app->input->getInt('id', -1));
			//only display data list view with edit link if a menu item exists
			if (empty($dataViewMenuItemExists)) {
				$layout = $this->input->get('layout', 'data', 'string');
				if ($layout == 'dataeditlist' || $layout == 'detailedit') {
					$app = Factory::getApplication();
					$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
					return false;
				}
			}
		}
		if ($vName == 'mysubmissions') {
			$menuexists = HTMLHelper::_('visforms.checkMySubmissionsMenuItemExists') ;
			if (empty($menuexists)) {
				$app = Factory::getApplication();
				$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				return false;
			}
		}
		parent::display($cachable, $urlparams);
	}
}