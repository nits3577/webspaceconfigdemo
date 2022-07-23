<?php
/**
 * Visforms decorator class for HTML controls
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

class VisformsHtmlControlDecoratorBt5 extends VisformsHtmlControlDecorator
{
	public function __construct($control) {
		parent::__construct($control);
        $this->breakPoints = array('Sm', 'Md', 'Lg', 'Xl', 'Xxl');
	}

    protected function decorate() {

        $control = $this->control;
        $field = $control->field->getField();
        $clabel = $control->createlabel();
        $ccontrol = $control->getControlHtml();
        $ccustomtext = $control->getCustomText();
        $field->ctrlGroupBtClasses = $this->getCtrlGroupBtClasses();
        $field->indentedBtClasses = $this->getIndentedBtClasses();
        return LayoutHelper::render('visforms.decorators.bt5', array('field' => $field, 'clabel' => $clabel, 'ccontrol' => $ccontrol, 'ccustomtext' => $ccustomtext), null, array('component' => 'com_visforms'));
    }
}

?>