<?php
/**
 * Visforms control html of textarea field for default layout
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\HTML\HTMLHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        //input
        $html = array();
        //We inclose textareas with HTML-Editor that are not readonly in a div
        if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor)
        {
            // textarea element to which the tooltip is attached will be disabled
            // attach tooltip to new enclosing div
            $tooltip = (!empty($field->custominfo)) ? ' title="'.htmlspecialchars($field->custominfo, ENT_COMPAT, 'UTF-8').'" data-bs-toggle="tooltip"' : '';
            $tooltipClass = (!empty($field->custominfo)) ? ' visToolTip' : '';
            // we do not need to include HTMLHelper::_('visforms.visformsTooltip'), because that was already done on the textarea
            $html[] = '<div class="editor'.$tooltipClass.'"'.$tooltip.'>';
        }
        $html[] = '<textarea ';
        if (!empty($field->attributeArray)) 
        {
             //add all attributes
             $html[] = ArrayHelper::toString($field->attributeArray, '=',' ', true);
        } 

        $html[] =  '>';
        $html[] = $field->initvalue;
        $html[] ='</textarea>';
        //field is a textarea with html Editor we have to close the div
        if (isset($field->hasHTMLEditor) && $field->hasHTMLEditor) 
        {
          $html[] = '</div>';
        }
        echo implode('', $html);
    endif;  
endif;

if (!empty($this->field->custominfo)) {
    $this->field->attribute_title = htmlspecialchars($this->field->custominfo, ENT_COMPAT, 'UTF-8');
    $this->field->attribute_class .= ' visToolTip';
    $this->field->{'attribute_data-bs-toggle'} = 'tooltip';
    HTMLHelper::_('visforms.visformsTooltip');
}
?>
   
