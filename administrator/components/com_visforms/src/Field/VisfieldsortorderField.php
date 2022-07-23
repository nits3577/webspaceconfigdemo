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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Form Field class for Visforms.
 * Supports list Visforms fields.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class VisfieldsortorderField extends ListField
{
	protected $type = 'VisFieldSortOrder';

	protected function getOptions()
	{
		$options = array();
		if (!empty(\VisformsAEF::checkAEF(\VisformsAEF::$subscription))) {
        $options[] = HTMLHelper::_(
                    'select.option', 'a.dataordering ASC',
                    Text::_('COM_VISFORMS_GRID_HEADING_ORDERING_DATA_VIEW_ASC'), 'value', 'text',
                    false
                );
		$options[] = HTMLHelper::_(
                    'select.option', 'a.dataordering DESC',
                    Text::_('COM_VISFORMS_GRID_HEADING_ORDERING_DATA_VIEW_DESC'), 'value', 'text',
                    false
                );
		}
        // Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
