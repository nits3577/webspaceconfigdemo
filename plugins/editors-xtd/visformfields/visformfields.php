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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;

/**
 * Editor Visfields buton
 *
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.visfields
 * @since       1.5
 */
class PlgButtonVisformfields extends CMSPlugin
{
    // load plugin language files
	protected $autoloadLanguage = true;

	public function onDisplay($name) {
		$app = Factory::getApplication();
		$o = $app->input->get('option');
		$v = $app->input->get('view');
		if ($o == 'com_visforms' && ($v == 'visform' || $v == 'vispdf')) {
			$fid = $app->input->getCmd('fid', 0);
			$id = $fid > 0 ? $fid : $app->input->getCmd('id', 0);
			/*
			 * Javascript to insert the link
			 * View element calls jSelectVisformsfield when an field is clicked
			 * jSelectVisformsfield creates the Placeholder for the field, sends it to the editor,
			 * and closes the select frame.
			 */
			$linkeditorname = '&amp;editor=' . $name;
			/*
			 * Use the built-in element view to select the field.
			 * Currently uses blank class.
			 */
			$link = 'index.php?option=com_visforms&amp;view=visplaceholders&amp;fid=' . $id . '&amp;layout=modal&amp;tmpl=component&amp;'
				. Session::getFormToken() . '=1' . $linkeditorname;
			$button = new JObject;
			$button->modal = true;
			$button->class = 'btn';
			$button->link = $link;
			$button->text = Text::_('PLG_VISFORMFIELDS_BUTTON_VISFORMFIELDS');
			$button->name = 'file-add';
			$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";
			return $button;
		}
	}
}
