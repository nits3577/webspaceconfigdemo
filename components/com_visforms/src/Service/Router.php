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

namespace Visolutions\Component\Visforms\Site\Service;
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
//use Joomla\CMS\Component\Router\Rules\MenuRules;
use Visolutions\Component\Visforms\Site\Service\MenuRules as MenuRules;
use Visolutions\Component\Visforms\Site\Service\VisformsRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Component\Router\RouterBase;

class Router extends RouterView
{
	protected $noIDs = false;

	public function __construct($app = null, $menu = null) {
		$visforms = new RouterViewConfiguration('visforms');
		$visforms->setKey('id');
		$visforms->addLayout('message');
		$this->registerView($visforms);
		$visformsdata = new RouterViewConfiguration('visformsdata');
		$visformsdata->setKey('id');
		$visformsdata->removeLayout('default');
		$visformsdata->addLayout('data');
		$visformsdata->addLayout('detail');
		$visformsdata->addLayout('dataeditlist');
		$visformsdata->addLayout('detailedit');
		$visformsdata->addLayout('detailitem');
		$this->registerView($visformsdata);
		/*$data = new RouterViewConfiguration('data');
		$data->setKey('layout')->setParent($visformsdata, 'id');
		$this->registerView($data);*/
		$mysubmissions = new RouterViewConfiguration('mysubmissions');
		$mysubmissions->setKey('id');
		$this->registerView($mysubmissions);
		$edit = new RouterViewConfiguration('edit');
		$edit->setKey('id');
		$this->registerView($edit);
		parent::__construct($app, $menu);
		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new VisformsRules($this));
		// do not move nomenrules above visformsrules!
        $this->attachRule(new NomenuRules($this));
		$test = true;
	}

	public function getVisformsSegment($id, $query) {
		if (!strpos($id, ':')) {
			$db = Factory::getDbo();
			$dbQuery = $db->getQuery(true)
				->select($db->qn('name'))
				->from($db->qn('#__visforms'))
				->where($db->qn('id') . ' = ' . (int) $query['id']);
			$db->setQuery($dbQuery);
			$id .= ':' . $db->loadResult();
		}
		return array((int) $id => $id);
	}

	public function getVisformsdataSegment($id, $query) {
		// view visformsdata layout detailitem has its own menu type
		// this is specific for one form (query parameter id) and one record set (query parameter cid)
		// MenuRules router does only accept one url parameter, in order to determine whether a menu item exists
		// this leads to wrong urls
		// if we have a non menu item url with query parameter detailitem, do not return the Segment
		// parse route of the no menu router will remove the cid from the query
		// so the no menu router will resolve to a full sef url
		if (isset($query['cid'])) {
			return array();
		}
		return $this->getVisformsSegment($id, $query);
	}

	public function getMysubmissionsSegment($id, $query) {
		return $this->getVisformsSegment($id, $query);
	}

	public function getEditSegment($id, $query) {
		return $this->getVisformsSegment($id, $query);
	}

	public function getVisformsId($segment, $query) {
		return (int) $segment;
	}

	public function getVisformsdataId($segment, $query) {
		return (int) $segment;
	}

	public function getMysubmissionsId($segment, $query) {
		return (int) $segment;
	}

	public function getEditId($segment, $query) {
		return (int) $segment;
	}
}