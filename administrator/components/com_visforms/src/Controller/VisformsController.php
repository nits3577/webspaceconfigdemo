<?php
/**
 * visforms controller for Visforms
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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

class VisformsController extends AdminController
{
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null) {
		parent::__construct($config, $factory, $app, $input);
	}
	
	public function getModel($name = 'Visform', $prefix = 'Administrator', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}