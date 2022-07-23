<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 * @since        Joomla 3.0.0
 */

namespace Visolutions\Component\Visforms\Administrator\Table;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

class TablebaseTable extends Table
{
    protected function addCreatedByFields()
    {
        // initialize if not yet set

        if (!intval($this->created)) {
            $date = Factory::getDate();
            $this->created = $date->toSql();
        }

        if (empty($this->created_by)) {
	        $user = Factory::getApplication()->getIdentity();
	        $this->created_by = $user->get('id');
        }
    }

    protected function _getAssetFormId(Table $table = null, $id = null) {
        // we will retrieve the parent-asset from the visforms table
        $assetId = null;
        $fid = $this->fid;
        if ($fid > 0) {
            // build the query to get the asset id for the parent category
            $query = $this->_db->getQuery(true);
            $query->select($this->_db->quoteName('asset_id'));
            $query->from($this->_db->quoteName('#__visforms'));
            $query->where($this->_db->quoteName('id') . ' = ' . (int) $fid);
            // get the asset id from the database
            $this->_db->setQuery($query);
            if ($result = $this->_db->loadResult()) {
                $assetId = (int) $result;
            }
        }
        else {
            // use component as default
            $assetParent = Table::getInstance('Asset');
            // default: if no asset-parent can be found we take the global asset
            $assetId = $assetParent->getRootId();
            // the item has the component as asset-parent
            $assetParent->loadByName('com_visforms');
            // return the found asset-parent-id
            if ($assetParent->id) {
                $assetId = $assetParent->id;
            }
        }

        // return the asset id
        if ($assetId) {
            return $assetId;
        }

        return parent::_getAssetParentId($table, $id);
    }
}