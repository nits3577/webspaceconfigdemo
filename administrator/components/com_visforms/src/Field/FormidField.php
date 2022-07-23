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
namespace Visolutions\Component\Visforms\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\TextField;

class FormidField extends TextField
{

	public $type = 'FormId';

	protected function getInput() {
		$fid = Factory::getApplication()->input->getInt('fid', 0);
		$this->value = $fid;
		$this->readonly = true;
		return parent::getInput();
	}
}
