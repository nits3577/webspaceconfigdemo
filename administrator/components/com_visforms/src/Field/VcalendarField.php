<?php
/**
 * Visform field VCalendar
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 */
namespace Visolutions\Component\Visforms\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\CalendarField;

class VcalendarField extends CalendarField
{
    protected $type = 'Vcalendar';

	public function setup(\SimpleXMLElement $element, $value, $group = null) {
		$return = parent::setup($element, $value, $group);
		if ($return) {
            $defaultValue = $this->form->getData()->get('defaultvalue');
		    if (!empty($defaultValue) && is_object($defaultValue) && !empty($defaultValue->f_date_format)) {
                $fldConfDateFormat = explode(';', $defaultValue->f_date_format);
                if (is_array($fldConfDateFormat) && isset($fldConfDateFormat[1])) {
                    $this->format = $fldConfDateFormat[1];
                }
            }
            $this->showtime = 'false';
		    $this->filltable =  'true';
            $this->filter = "none";
		}

		return $return;
	}
}
