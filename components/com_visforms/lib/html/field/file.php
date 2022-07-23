<?php
/**
 * Visforms HTML class for file fields
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

use Joomla\CMS\HTML\HTMLHelper;

require_once(__DIR__ . '/text.php');

/**
 * Create HTML of a file field according to it's type
 *
 * @package        Joomla.Site
 * @subpackage     com_visforms
 * @since          1.6
 */
class VisformsHtmlFile extends VisformsHtmlText
{

	public function __construct($field, $decorable, $attribute_type) {
		$attribute_type = "file";
		parent::__construct($field, $decorable, $attribute_type);
	}

	// bootstrap 2 and 3
	public function removeNoBootstrapClasses($field) {
		if ((isset($field->attribute_class))) {
			$field->attribute_class = '';
		}
		return $field;
	}

    // bootstrap 4
    public function setControlBt5HtmlClasses($field) {
        return $field;
    }

	// bootstrap 4
	public function setControlHtmlClasses($field) {
		return $field;
	}

	// uikit 3
	public function setUikit3ControlHtmlClasses($field) {
		return $field;
	}

	// uikit 2
	public function setUikit2ControlHtmlClasses($field) {
		return $field;
	}

	public function removeUnsupportedShowLabel($field) {
		unset($field->show_label);
		return $field;
	}
}