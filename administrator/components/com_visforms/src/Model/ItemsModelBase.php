<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

namespace Visolutions\Component\Visforms\Administrator\Model;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;

class ItemsModelBase extends ListModel {

	protected $fid;

	public function __construct($config = array(), MVCFactoryInterface $factory = null) {
		parent::__construct($config, $factory);
		$app = Factory::getApplication();
		$this->fid = $app->input->getInt('fid',  0);
	}

	public function getForm() {
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__visforms'))
			->where('id='.$this->fid);
		$db->setQuery($query);
		$form = $db->loadObject();
		return $form;
	}
}