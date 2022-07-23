<?php
/**
 * visfields model for Visforms
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

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

class VisplaceholdersModel extends VisfieldsModel
{
    protected $fid;

	public function __construct($config = array(), MVCFactoryInterface $factory = null) {
		parent::__construct($config, $factory);
        $app = Factory::getApplication();
		$this->fid = $app->input->getInt('fid',  0);
	}
}