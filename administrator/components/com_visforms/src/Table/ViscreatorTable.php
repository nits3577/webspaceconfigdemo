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
 * @since        Joomla 3.6.2
 */

namespace Visolutions\Component\Visforms\Administrator\Table;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
//use Visolutions\Component\Visforms\Administrator\Table\TableBase;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Access\Rules;


class ViscreatorTable extends TablebaseTable
{
    public function __construct(DatabaseDriver $db) {
        parent::__construct('#__viscreator', 'id', $db);
    }

    protected function _getAssetName() {
        return 'com_visforms.visform.'. $this->fid . '.viscreator.'.$this->id;
    }

    protected function _getAssetTitle() {
        return $this->label;
    }

    protected function _getAssetParentId(Table $table = null, $id = null) {
        return $this->_getAssetFormId($table, $id);
    }

    public function bind($array, $ignore = '') {
        // bind the rules
        if (isset($array['rules'])) {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }
        return parent::bind($array, $ignore);
    }

    public function store($updateNulls = false) {
        $this->addCreatedByFields();
        return parent::store($updateNulls);
    }
}