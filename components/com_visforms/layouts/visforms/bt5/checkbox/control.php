<?php
/**
 * Visforms control html for checkbox for multi colum layout
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
use Joomla\Utilities\ArrayHelper;

if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        $clabel = $field->clabel;
        $ccustomtext = $field->ccustomtext;
	    $inverseBtLabelClasses = $field->inverseBtLabelClasses;
	    $bt_size = $field->fieldGroupBootstrapClasses;
	    $labelClass = 'asterix-ancor form-check-label';
	    $labelClass .= (!empty($field->labelCSSclass)) ? ' ' .$field->labelCSSclass: '';
        $html = array();
        //we wrap the control in a div if the field isCondtional, so that we can easily hide the whole control
	    $html[] = '<div class="' . $bt_size.' ';
	    $html[] = (isset($field->isConditional) && ($field->isConditional == true)) ? 'conditional field' . $field->id : 'field' . $field->id;
	    $html[] = (isset($field->attribute_required)) ? ' required' : '';
	    $html[] = (isset($field->isForbidden) && ($field->isForbidden == true)) ? ' isForbidden' : '';
	    //closing quote for class attribute
	    $html[] = '"';
	    $html[] = (isset($field->isDisabled) && ($field->isDisabled == true)) ? ' style="display:none;" ' : "";
	    $html[] = '>';

        if (($ccustomtext != '') && (isset($field->customtextposition)) && (($field->customtextposition == 0) || ($field->customtextposition == 1)))
        {
	        $html[] = '<div class="row">';
            $html[] = $ccustomtext;
	        $html[] = '</div>';
        }
	    $html[] = '<div class="form-group row">';
	    $html[] = '<div class="'.$inverseBtLabelClasses.'">';
        $html[] = '<div class="form-check">';
		$html[] = '<input ';
		if (!empty($field->attributeArray))
		{
			//add all attributes
			$html[] = ArrayHelper::toString($field->attributeArray, '=',' ', true);
		}
		$html[] = '/>';
	    $html[] = '<label class="' .$labelClass . '" id="' . $field->name. 'lbl" for="field'. $field->id . '">';
		$html[] = $field->label;
		$html[] = "</label>";
	    $html[] = '</div>';
        $html[] = '</div>';
	    $html[] = '</div>';
	    $html[] = '<div class="row">';
	    $html[] = '<div class="'.$inverseBtLabelClasses.'">';
	    $html[] = LayoutHelper::render('visforms.custom.default_error_div', $displayData, null, array('component' => 'com_visforms'));
	    $html[] = '</div>';
	    $html[] = '</div>';
	    if (($ccustomtext != '') && (((isset($field->customtextposition)) && ($field->customtextposition == 2)) || !(isset($field->customtextposition))))
	    {
		    $html[] = '<div class="row">';
		    $html[] = $ccustomtext;
		    $html[] = '</div>';
	    }
	    $html[] = '</div>';

        echo implode('', $html);
    endif;  
endif; ?>
