<?php
/**
 * Visforms create control HTML class
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

/**
 * create visforms bthorizontal text HTML control
 *
 * @package		Joomla.Site
 * @subpackage	com_visforms
 * @since		1.6
 */
class VisformsHtmlControlBthorizontalText extends VisformsHtmlControl
{
   
    /**
    * Method to create the html string for control
    * @return string html
    */
   public function getControlHtml()
   {
       $field = $this->field->getField();       
        //input
        $html = '';
        $layout = new JLayoutFile('visforms.bthorizontal.text.control', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        $html .= $layout->render(array('field' => $field));
        return $html;       
   }
   
   /**
    * Method to create the html string for control label
    * @return string html
    */
   public function createLabel()
   {
        $field = $this->field->getField();
        $labelClass = $this->getLabelClass();
        $field->labelClass = $labelClass;
        //label
        $html = '';

        $layout = new JLayoutFile('visforms.bthorizontal.text.label', null);
        $layout->setOptions(array('component' => 'com_visforms'));
        $html .= $layout->render(array('field' => $field));
        return $html;
   }
}