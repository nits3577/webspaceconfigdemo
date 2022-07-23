<?php
/**
 * Visforms create control HTML class
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;

class VisformsHtmlControlBt5Multicheckbox extends VisformsHtmlControlBt5Radio
{
	public function getControlHtml() {
		return LayoutHelper::render('visforms.bt5.multicheckbox.control', array('field' => $this->field->getField()), null, array('component' => 'com_visforms'));
	}
}