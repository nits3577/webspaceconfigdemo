<?php
/**
 * viscpanel model for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

namespace Visolutions\Component\Visforms\Administrator\Model;

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Component\ComponentHelper;

class ViscpanelModel extends BaseDatabaseModel
{
    public function __construct($config = array(), MVCFactoryInterface $factory = null) {
        parent::__construct($config, $factory);
    }

    public function storeDlid() {
        $extensions = "('files_vfmultipageforms', 'files_vfbt3layouts', 'files_vffrontedit', 'Plugin Visforms - Mail Attachments', 'Plugin Content Visforms Form View', 'Plugin Visforms - Maxsubmissions', 'Plugin Visforms - Delay Double Registration', 'plg_search_visformsdata', 'Plugin Content Visforms Data View', 'Visforms - Custom Mail Address', 'files_vfcustomfieldtypes', 'vfsubscription')";
        $dlId = $this->getState('dlid');
        $extra_query = (!empty($dlId)) ? "dlid=$dlId" : "";
        $return = true;
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update('#__update_sites')
            ->set('extra_query = ' . $db->quote($extra_query))
            //->where('name = "vfsubscription"');
            ->where('name in ' . $extensions);
        try {
	        $db->setQuery($query);
            $db->execute();
        }
        catch (RuntimeException $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage() . ' Problems saving download id', 'error');
            $return = false;
        }
	    if (!$this->storeParam('downloadid', $dlId)) {
		    return false;
	    }
        return $return;
    }

	public function storeDemoFormInstalled() {
		if (!$this->storeParam('demoFormInstalled', true)) {
			return false;
		}
		return true;
	}

    protected function storeParam($name, $value) {
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
    }
}