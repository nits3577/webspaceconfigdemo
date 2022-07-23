<?php
/**
 * Visforms control html for date field for bootstrap horizontal layout
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

if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        //input
        $html = HTMLHelper::_('visformscalendar.calendar', $field->attribute_value, $field->name, 'field' . $field->id, $field->dateFormatJs, $field->attributeArray);
        echo $html;
    endif;  
endif; ?>