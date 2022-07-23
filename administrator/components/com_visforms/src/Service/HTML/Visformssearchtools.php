<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 */

namespace Visolutions\Component\Visforms\Administrator\Service\HTML;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

class Visformssearchtools
{
	protected static $loaded = array();

	public static function form($selector = '.js-stools-form', $options = array()) {
		$sig = md5(serialize(array($selector, $options)));
		// Only load once
		if (!isset(static::$loaded[__METHOD__][$sig])) {
			// Include Bootstrap framework
			HTMLHelper::_('jquery.framework');
			// Load the jQuery plugin && CSS
			HTMLHelper::_('script', 'com_visforms/jquery.searchtools.min.js', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('stylesheet', 'com_visforms/jquery.searchtools.css', array('version' => 'auto', 'relative' => true));
			// Add the form selector to the search tools options
			$options['formSelector'] = $selector;
			// Generate options with default values
			$options = static::optionsToRegistry($options);
			$doc = Factory::getApplication()->getDocument();
			$script = "
				(function($){
					$(document).ready(function() {
						$('" . $selector . "').searchtools(
							" . $options->toString() . "
						);
					});
				})(jQuery);
			";
			$doc->addScriptDeclaration($script);
			static::$loaded[__METHOD__][$sig] = true;
		}
		return;
	}

	private static function optionsToRegistry($options) {
		// Support options array
		if (is_array($options)) {
			$options = new Registry($options);
		}
		if (!($options instanceof Registry)) {
			$options = new Registry;
		}
		return $options;
	}
}
