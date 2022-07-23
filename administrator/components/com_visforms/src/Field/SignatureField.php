<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */
namespace Visolutions\Component\Visforms\Administrator\Field;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Form\FormField;

class SignatureField extends FormField
{
	protected $type = 'Signature';

	protected function getInput() {
		$base30 = $this->value;
		$field = new \stdClass();
		$field->canvasWidth = (isset($this->element['canvasWidth'])) ? (string) $this->element['canvasWidth'] : '280';
		$field->canvasHeight = (isset($this->element['canvasHeight'])) ? (string) $this->element['canvasHeight'] : '120';
		return LayoutHelper::render('visforms.datas.fields.signature', array('field' => $field, 'data' => $base30, 'maxWidth' => 200), null, array('component' => 'com_visforms', 'client' => 'site'));
	}
}