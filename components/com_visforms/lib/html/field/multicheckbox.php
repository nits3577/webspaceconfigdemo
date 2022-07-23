<?php
/**
 * Visforms HTML class for multicheckbox
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

require_once(__DIR__ . '/radio.php');

/**
 * Create HTML of a multicheckbox according to it's type
 *
 * @package		Joomla.Site
 * @subpackage	com_visforms
 * @since		1.6
 */
class VisformsHtmlMulticheckbox extends VisformsHtmlRadio
{     
    /**
     * 
     * Constructor
     * 
     * @param object $field field object as extracted from database
     */
    public function __construct($field, $decorable, $attribute_type)
    {
        $attribute_type = "checkbox";
        parent::__construct($field, $decorable, $attribute_type);
    }

    // uikit 3
	public function setUikit3ControlHtmlClasses($field) {
		$field->attribute_class = " uk-checkbox";
		return $field;
	}

	// uikit 2
	public function setUikit2ControlHtmlClasses($field) {
		$field->attribute_class = " uk-checkbox uk-width-1-1";
		return $field;
	}
}