<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

abstract class VisformsPlaceholderEntry {

	protected $param;
	protected $rawData;
	protected $field;
	protected static $customParams = array();
	protected static $customSubscriptionParams = array();

	// used to create placeholder list in modal editor-xtd-button view
	// we do not provide checked_out and checked_out_time in this list
	public static $nonFieldPlaceholder = array(
		'formtitle' => 'COM_VISFORMS_PLACEHOLDER_FORM_TITLE',
		'id' => 'COM_VISFORMS_ID',
		'created' => 'COM_VISFORMS_FIELD_CREATED_LABEL',
		'created_by' => 'COM_VISFORMS_FIELD_CREATED_BY_LABEL',
		'modified' => 'COM_VISFORMS_MODIFIED_AT',
		'modified_by' => 'COM_VISFORMS_MODIFIED_BY',
		'ismdf' => 'COM_VISFORMS_MODIFIED',
		'ipaddress' => 'COM_VISFORMS_IP',
		'currentdate' => 'COM_VISFORMS_CURRENT_CURRENT_DATE'
	);

	public static $subscriptionNonFieldPlaceholder = array(
		
	);

	public function __construct($param, $rawData, $field) {
		$this->param = $param;
		$this->rawData = $rawData;
		$this->field = $field;
	}

	public static function getInstance ($pParam, $rawData, $pType = 'Default', $field = null) {
		if (empty($pParam)) {
		    $pParam = '';
        }
	    $className = 'VisformsPlaceholderEntry' . ucfirst($pType);
		if (!class_exists($className)) {
			// Try to load specific placeholder class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholderentry/' . strtolower($pType . '.php'));
		}
		if (!class_exists($className)) {
			$className = 'VisformsPlaceholderEntryDefault';
			// Fall back to default class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholderentry/default.php');
		}
		if (class_exists($className)) {
			return new $className($pParam, $rawData, $field);
		}
		return false;
	}

	// returns an array of strings that can be added as params to the placeholder
	public static function getParamStringsArrayForType($pType) {
		$var = 'customParams';
		$subscriptionVar = 'customSubscriptionParams';
		$className = 'VisformsPlaceholderEntry' . ucfirst($pType);
		if (!class_exists($className)) {
			// Try to load specific placeholder class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholderentry/' . strtolower($pType . '.php'));
		}
		if (!class_exists($className)) {
			$className = 'VisformsPlaceholderEntryDefault';
			// Fall back to default class
			JLoader::register($className, JPATH_ADMINISTRATOR . '/components/com_visforms/lib/placeholderentry/default.php');
		}
		if (class_exists($className)) {
			$vars = get_class_vars($className);
			if (!empty($vars) && is_array($vars)) {
				if (VisformsAEF::checkAEF(VisformsAEF::$subscription)) {
					$customParams = array_merge($className::$$var, $className::$$subscriptionVar);
				}
				else {
					$customParams = $className::$$var;
				}
				foreach ($customParams as $key => $description) {
					$customParams[$key] = Text::_($description);
				}
				return $customParams;
			}
		}
		// no special params for this type
		return self::$$var;
	}
	
	public static function getStaticPlaceholderList() {
		return (VisformsAEF::checkAEF(VisformsAEF::$subscription)) ? array_merge(self::$nonFieldPlaceholder, self::$subscriptionNonFieldPlaceholder) : self::$nonFieldPlaceholder;
	}

	abstract public function getReplaceValue();
}