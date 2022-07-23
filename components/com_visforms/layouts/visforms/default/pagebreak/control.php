<?php
/**
 * Visforms control html for pagebreak button for default layout
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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;

if (!empty($displayData)) : 
    if (isset($displayData['field'])) :
        $field = $displayData['field'];
        $mpDisplayType = $field->mpdisplaytype;
        //0 is multi page, 1 = accordion
        if (!empty($mpDisplayType) && ($mpDisplayType == 1)) :
	        $firstPanelCollapsed = $field->firstpanelcollapsed;
            $html = array();
            $accordionid = (!empty($field->accordionid)) ? $field->accordionid : 'visformaccordion';
            $collapseid = 'collapse'.$field->id;
            $in = ((!empty($field->accordioncounter)) && ($field->accordioncounter  == 1) && empty($firstPanelCollapsed)) ? ' show' : '';
            if ((!empty($field->accordioncounter)) && ($field->accordioncounter  > 1))
            {
                //close previous accordion
                $html[] = '</div>';
                $html[] = '</div>';
                $html[] = '</div>';
            }
            else if ($field->accordioncounter  == 1)
            {
                //open accordion container
                $html[] = '<div class="accordion" id="' . $accordionid . '">';
            }
            $html[] = '<div class="accordion-item">';
            $html[] = '<h2 class="accordion-header">';
            $html[] = '<button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#'.$collapseid.'">'.$field->label.'</button>';
            $html[] = '</h2>';
            $html[] = '<div id="'.$collapseid.'" class="accordion-collapse collapse'.$in.'" data-bs-parent="#'.$accordionid.'">';
            $html[] = '<div class="accordion-body">';
            echo implode('', $html);
        else :
            $html = array();
            $html[] = '<div class="visBtnCon">';
            if (($field->customtext != '') && (isset($field->customtextposition)) && (($field->customtextposition == 0) || ($field->customtextposition == 1)))
            {
                PluginHelper::importPlugin('content');
                $customtext =  HTMLHelper::_('content.prepare', $field->customtext);
                $html[] = '<div class="visCustomText ">' . $customtext. '</div>';
            }

            if ((!empty($field->fieldsetcounter)) && ($field->fieldsetcounter  > 1))
            {
                $backButtonText = (!empty($field->backbtntext)) ? $field->backbtntext : Text::_('COM_VISFORMS_STEP_BACK');
                //add a back button
                $html[] = '<input type="button" class="btn back_btn" value="'.$backButtonText.'"/> ';
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
            $html[] = '</div>';
            echo implode('', $html);
       endif;
    endif;  
endif; ?>