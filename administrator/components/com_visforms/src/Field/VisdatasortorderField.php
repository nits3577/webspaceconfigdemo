<?php
/**
 * Visform field Visdatasortorder
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */
namespace Visolutions\Component\Visforms\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/include.php';

class VisdatasortorderField extends ListField
{
	protected $type = 'VisDataSortOrder';

	protected function getOptions() {
		$id = 0;
		//extract form id
		$form = $this->form;
		$link = $form->getValue('link');
		if (isset($link) && $link != "") {
			$parts = array();
			parse_str($link, $parts);
			if (isset($parts['id']) && is_numeric($parts['id'])) {
				$id = $parts['id'];
			}
		}
		$optionHelper = new \visFormsSortOrderHelper($id);
		$options = $optionHelper->getOptions();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
