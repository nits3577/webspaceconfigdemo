<?php
/**
 * Visforms label html for text field for default layout
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

?>
<?php 
if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        $labelClass = $field->labelClass;
        //label
        $html = array();
        //hide label with css if this option is set, so we can still use it in aria-labelledby
        $style = (isset($field->show_label) && ($field->show_label == 1)) ? ' style="display: none;"' : '';        
        $html[] = '<label class=" ' . $labelClass . ' ' .$field->labelCSSclass . '" id="' . $field->name. 'lbl" for="field' . $field->id .'"' . $style . '>';
        $html[] = $field->label;
        $html[] = '</label>';
        //create an empty span that can take on the required asterix
        if (isset($field->attribute_required) && ($field->attribute_required == 'required') && (isset($field->show_label) && ($field->show_label == 1))) 
        {
            $html[] = '<label class="asterix-ancor ' . $labelClass . '" ></label>';
        }
        echo implode('', $html);
    endif;  
endif; ?>