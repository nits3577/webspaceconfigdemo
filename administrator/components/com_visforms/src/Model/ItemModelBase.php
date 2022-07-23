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

namespace Visolutions\Component\Visforms\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel;

class ItemModelBase extends AdminModel
{
    protected $item;

    public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null) {
        parent::__construct($config, $factory, $formFactory);
	    $this->user = Factory::getApplication()->getIdentity();
    }

    public function getForm($data = array(), $loadData = true) { }

    public function getItem($pk = null)
    {
        if(isset($this->item)) {
            // item already loaded and processed
            return $this->item;
        }
        if($this->item = parent::getItem($pk)) {
            // sub class: format the fields parameters
            $this->loadFormFieldsParameters();
        }
        return $this->item;
    }

    protected function loadFormFieldsParameters() { }
}