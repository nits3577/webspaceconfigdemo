<?php
/**
 * Visforms control html for text field for default layout
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
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
?>
<?php
if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];     
        //input
        $html = array();
        $html[] = '<input ';
        if (!empty($field->attributeArray)) 
        {
             //add all attributes
             $html[] = ArrayHelper::toString($field->attributeArray, '=',' ', true);
        } 

        $html[] =  '/>';
        if (!empty($field->showclearbutton))
        {
            $html[] = '<a class="clear-selection" data-clear-target="field' . $field->id . '" href="">' . Text::_('COM_VISFORMS_CLEAR_SELECTION') . '</a>';
        }
        echo implode('', $html);
    endif;  
endif; ?>


