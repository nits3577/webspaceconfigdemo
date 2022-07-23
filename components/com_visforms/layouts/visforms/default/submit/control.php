<?php
/**
 * Visforms control html for submit button for default layout
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

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        $html = array();
        if (($field->customtext != '') && (isset($field->customtextposition)) && (($field->customtextposition == 0) || ($field->customtextposition == 1)))  
        {
			PluginHelper::importPlugin('content');
			$customtext =  HTMLHelper::_('content.prepare', $field->customtext);
            $html[] = '<div class="visCustomText ">' . $customtext. '</div>';
        }
        $html[] =  '<input ';
        if (!empty($field->attributeArray)) 
        {
            //add all attributes
            $html[] = ArrayHelper::toString($field->attributeArray, '=',' ', true);
        } 
        $html[] = '/>&nbsp;';
        if (($field->customtext != '') && (((isset($field->customtextposition)) && ($field->customtextposition == 2)) || !(isset($field->customtextposition))))  
        {
			PluginHelper::importPlugin('content');
			$customtext =  HTMLHelper::_('content.prepare', $field->customtext);
            $html[] = '<div class="visCustomText ">' . $customtext. '</div>';
        }

        echo implode('', $html);
    endif;  
endif; ?>