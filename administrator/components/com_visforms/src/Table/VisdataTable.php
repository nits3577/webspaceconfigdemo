<?php

/**
 * Visform table class
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Table;

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Visolutions\Component\Visforms\Administrator\Table\TablebaseTable as VisTableBase;

class VisdataTable extends VisTableBase
{   
    public function __construct(\JDatabaseDriver $db) {
        $id = Factory::getApplication()->input->getInt('fid', -1);
        parent::__construct('#__visforms_' . $id, 'id', $db);
    }
}