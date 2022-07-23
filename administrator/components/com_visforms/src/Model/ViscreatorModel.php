<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 * @since        Joomla 3.0.0
 */

namespace Visolutions\Component\Visforms\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

class ViscreatorModel extends ItemModelBase
{
	public function getForm($data = array(), $loadData = false) {
		$form = $this->loadForm('com_visforms.viscreator', 'viscreator', array('control' => 'jform', 'load_data' => $loadData));
		return $form;
	}
}