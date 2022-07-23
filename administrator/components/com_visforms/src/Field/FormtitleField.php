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

use Joomla\CMS\Form\Field\TextField;
use Visolutions\Component\Visforms\Administrator\Model\VisfieldsModel;

class FormtitleField extends TextField
{
	public $type = 'Formtitle';

	protected function getInput() {
        $model = new VisfieldsModel(); //JModelLegacy::getInstance('Visfields', 'VisformsModel');
        $formtitle = $model->getFormtitle();
        $this->value = $formtitle;
        $this->readonly = true;
        return parent::getInput();
	}
}