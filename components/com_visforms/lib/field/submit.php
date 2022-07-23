<?php
/**
 * Visforms field submit class
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

class VisformsFieldSubmit extends VisformsField
{
    protected function setField() {
        $this->cleanFieldProperties();
        $this->extractDefaultValueParams();
	    $this->extractGridSizesParams();
        $this->setIndividualProperties();
        $this->setFieldDefaultValue();
        $this->setCustomJs();
    }
    

    protected function setFieldDefaultValue() {
        //Nothing to do for Submit buttons
    }
    
    protected function setIndividualProperties() {
        $this->field->isButton = true;
    }
    
    protected function setDbValue() {
        return;
    }

    protected function setRedirectParam() {
        return;
    }

    protected function cleanFieldProperties() {
        //$this->field->customtext = '';
    }
}