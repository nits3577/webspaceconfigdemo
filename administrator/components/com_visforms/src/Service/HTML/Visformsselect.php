<?php
/**
 * HTMLHelper for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Service\HTML;

defined('_JEXEC') or die( 'Direct Access to this location is not allowed.' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\CMS\Language\Text;

require_once JPATH_ROOT . '/administrator/components/com_visforms/lib/visformsSql.php';

/**
 * Utility class for creating HTML Grids
 *
 * @static
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @since   1.5.5
 */
class Visformsselect
{
	protected static $loaded = array();
    public static $nullbyte = "\0";
    public static $msdbseparator = "\0, ";
    
    /**
     * Explode database value of stored user input of fields with type select or multicheckbox
     * @param string $dbvalue: multiple values of multiselect or multichechbox are separated by "\0, "
     * @return array
     * @since  Visform 3.7.0
     */
    public static function explodeMsDbValue ($dbvalue)
    {
        $values = explode(static::$msdbseparator , $dbvalue);
        foreach ( $values as $index => $word) {
             $values[$index] = (string) trim($word);
        }
        return $values;
    }
    
    //remove Nullbit from string
    public static function removeNullbyte($value)
    {
        if ((!empty($value)) && is_string($value)) {
            $value = str_replace(static::$nullbyte, "", $value);
        }
        return $value;
    }

    public static function extractHiddenList ($optionString = '')
    {
        $options = array();
        $returnopts = array();
        if ($optionString != "") {
            $options = json_decode($optionString);
            foreach ($options as $option) {
                if (!empty($option->listitemvalue)) {
                    $option->listitemvalue = (string) trim($option->listitemvalue);
                }
                if (isset($option->listitemischecked) && ($option->listitemischecked == "1")) {
                    $selected = true;
                }
                else {
                    $selected = false;
                }
                $option->listitemredirecturl = (isset($option->listitemredirecturl)) ? StringHelper::trim($option->listitemredirecturl) : '';
                $option->listitemmail = (isset($option->listitemmail)) ? StringHelper::trim($option->listitemmail) : '';
                $option->listitemmailcc = (isset($option->listitemmailcc)) ? StringHelper::trim($option->listitemmailcc) : '';
                $option->listitemmailbcc = (isset($option->listitemmailbcc)) ? StringHelper::trim($option->listitemmailbcc) : '';
	            $option->listiteminputclass = (isset($option->listiteminputclass)) ? $option->listiteminputclass : '';
	            $option->listitemlabelclass = (isset($option->listitemlabelclass)) ? $option->listitemlabelclass : '';

                $returnopts[] = array( 'id' => $option->listitemid, 'value' => $option->listitemvalue, 'label' => $option->listitemlabel, 'selected' => $selected, 'redirecturl' => $option->listitemredirecturl, 'mail' => $option->listitemmail, 'mailcc' => $option->listitemmailcc, 'mailbcc' => $option->listitemmailbcc, 'inputclass' =>  $option->listiteminputclass, 'labelclass' =>  $option->listitemlabelclass);
            }
        }       
        return $returnopts;
    }
    
    public static function mapDbValueToOptionLabel ($dbValue, $fieldHiddenList)
    {
        $fieldOptions = static::extractHiddenList($fieldHiddenList);
        if (empty($fieldOptions)) {
            return false;
        }
        $extractedItemValues = static::explodeMsDbValue($dbValue);
        $newExtractedItemFieldValues = array();
        foreach ($fieldOptions as $fieldOption) {
            foreach ($extractedItemValues as $extractedItemValue) {
                if ($extractedItemValue == $fieldOption['value']) {
                    $newExtractedItemFieldValues[] = $fieldOption['label'];
                }                      
            }
        }
        return $newExtractedItemFieldValues;
    }

    public static function getOptionsFromSQL($sql, $inputContext = '') {
	    $returnopts = array();
	    $i = 1;
	    try {
	    	$sqlHelper = new \VisformsSql($sql, $inputContext);
		    $items = $sqlHelper->getItemsFromSQL();
	    }
	    catch (\Exception $e) {
		    return $returnopts;
	    }
	    if (!empty($items)) {
		    foreach ($items as $item) {
			    if (isset($item->label) && isset($item->value)) {
			        // database query in Joomla4 may return an integer; Option Values in selects are always strings; convert all value values to string
				    $returnopts[] = array('id' => $i, 'value' => (string) $item->value, 'label' => $item->label, 'selected' => false, 'redirecturl' => (isset($item->redirecturl) ? $item->redirecturl : ''), 'mail' => (isset($item->mail) ? $item->mail : ''), 'mailcc' => (isset($item->mailcc) ? $item->mailcc : ''), 'mailbcc' => (isset($item->mailbcc) ? $item->mailbcc : ''), 'labelclass' => (isset($item->labelclass) ? $item->labelclass : ''));
			    }
		    }
	    }
	    return $returnopts;
    }

    public static function mapDbValueToSqlOptionLabel ($dbValue, $sql) {
	    $fieldOptions = static::getOptionsFromSQL($sql);
	    $extractedItemValues = static::explodeMsDbValue($dbValue);
	    $newExtractedItemFieldValues = array();
	    foreach ($extractedItemValues as $extractedItemValue) {
	        foreach ($fieldOptions as $fieldOption) {
	            // clean up from previous loop
	        	if (isset($optionLabel)) {
			        unset($optionLabel);
		        }
	        	// use label from sql if it is not empty
			    if ($extractedItemValue == $fieldOption['value'] && !empty($fieldOption['label'])) {
				    $optionLabel = $fieldOption['label'];
				    break;
			    }
		    }
		    if (isset($optionLabel)) {
			    $newExtractedItemFieldValues[] = $optionLabel;
		    }
		    else {
			    $newExtractedItemFieldValues[] = $extractedItemValue;
		    }
	    }
	    return $newExtractedItemFieldValues;
    }

	public static function getStoredUserInputs($fieldId, $formId, $recordId = 0, $pulishedOnly = false) {
		$db = Factory::getDbO();
		$query = $db->getQuery(true);
		$query->select($db->qn('F' . $fieldId))
			->from($db->qn('#__visforms_' . $formId));
		if (!empty($pulishedOnly)) {
			$query->where($db->qn('published') . ' = ' . 1);
		}
		// exclude current record on edit submit
		if (!empty($recordId)) {
			$query->where($db->qn('id') . ' != ' . $recordId);
		}
		$query->where($db->qn('F' . $fieldId) . ' IS NOT NULL');
		$query->where($db->qn('F' . $fieldId) . " != ''");
		$query->group($db->qn('F' . $fieldId));
		$db->setQuery($query);
		try {
			return $db->loadColumn();
		}
		catch (\Exception $exc) {
			return array();
		}
	}

	public static function loadSearchableApi () {
		if (!empty(static::$loaded[__METHOD__])) {
			return true;
		}
		$doc = Factory::getDocument();
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('script', 'media/com_visforms/js/select2.js', array('version' => 'auto', 'relative' => false, 'detectBrowser' => false, 'detectDebug' => false));
		$doc->addStyleSheet(Uri::root(true) . '/media/com_visforms/css/select2.min.css', array('version' => 'auto', 'relative' => false, 'detectBrowser' => false, 'detectDebug' => false));
		static::$loaded[__METHOD__] = true;
		return false;
	}

    public static function getOptionListForSQLFilterFields ($fieldId, $formId, $canPublish) {
        $dbValues = static::getStoredUserInputs($fieldId, $formId, 0, !$canPublish);
        $uniqueDbValues = array();
        $returnOpts = array();
        foreach ($dbValues as $dbValue) {
            // extract single option values from multiselect
            $singleValues = static::explodeMsDbValue($dbValue);
            // create an array with one element for each unique single dbValue;
            foreach ($singleValues as $singleValue) {
                $singleValue = (string) $singleValue;
                $uniqueDbValues[$singleValue] = $singleValue;
            }
        }
        // return SQL Filter Field Options in Visforms standard format (array of objects with value and label property keys)
        foreach ($uniqueDbValues as $uniqueDbValue) {
            $returnOpts[] = array( 'value' => $uniqueDbValue, 'label' => $uniqueDbValue);
        }
        return $returnOpts;
    }

    public static function createEmptyOption($field, $options) {
        $hasSelectedItem = false;
        $k = count($field->opts);
        // Has select no default value or is required? Then we need a supplementary 'default' option for selects that are not "multiple" or have a height < 1. Otherwise the first option can not be selected properly.
        for ($j = 0; $j < $k; $j++) {
            if ($field->opts[$j]['selected'] != false) {
                $hasSelectedItem = true;
                break;
            }
        }
        if ((empty($field->attribute_multiple))
            && (empty($field->attribute_size))
            && ((empty($hasSelectedItem)) || (isset($field->attribute_required)))) {
            $options[] = HTMLHelper::_('select.option', '', ((empty($field->customselectvaluetext)) ? Text::_('CHOOSE_A_VALUE') : $field->customselectvaluetext));
        }
        return $options;
    }
}
?>