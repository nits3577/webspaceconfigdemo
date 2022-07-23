<?php
/**
 * Visforms error html  for bootstrap default layout
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

use Joomla\CMS\Factory;

if (!empty($displayData)) : 
    if (isset($displayData['errormessages'])) :
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useScript('messages');
        // Using custom selector may not work if template implements css using #system-message-container, therefore we need to deliver the css, too
        // Using #system-message-container may break message queue
        $selector = (!empty($displayData['context'])) ? $displayData['context'] . '-error-container' : 'visforms-error-container';//'system-message-container';
        $messages = "{danger: ".json_encode($displayData['errormessages'])."}";
        $alert = 'Joomla.renderMessages(' . $messages . ', "#'.$selector.'")';
        // render empty wrapper div for message
        echo '<div id="'.$selector.'"></div>';
        // add eventhandler
        $script = "document.addEventListener('DOMContentLoaded', function() {Joomla.renderMessages(" . $messages . ", '#".$selector."')});";
        $wa->addInlineScript($script);
        // hide empty wrapper with inline css
        $style = "#visformcontainer #" .$selector. ":empty {display: none; margin-top: 0;}";
        $wa->addInlineStyle($style);
        // add visforms joomla-alert css
        $wa->registerAndUseStyle('com_visforms.joomla-alert','com_visforms/joomla-alert.css');
    endif;  
endif;



        