<?php
/**
 * HTMLHelper for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

namespace Visolutions\Component\Visforms\Administrator\Service\HTML;

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;

/**
 * Utility class for creating HTML Calendar
 *
 * @static
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @since        1.5.5
 */
class Visformscalendar
{
	public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = array(), $layout = '') {
		static $handlerloaded;
		$app = Factory::getApplication();
		$document = $app->getDocument();
        $lang     = $app->getLanguage();
		$calendar = $lang->getCalendar();
		$direction = strtolower($document->getDirection());
		// Get the appropriate file for the current language date helper
		$helperPath = 'system/fields/calendar-locales/date/gregorian/date-helper.min.js';
		if (!empty($calendar) && is_dir(JPATH_ROOT . '/media/system/js/fields/calendar-locales/date/' . strtolower($calendar))) {
			$helperPath = 'system/fields/calendar-locales/date/' . strtolower($calendar) . '/date-helper.min.js';
		}
		$readonly = isset($attribs['readonly']) && $attribs['readonly'] === 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] === 'disabled';
		$todayBtn = isset($attribs['todayBtn']) ? $attribs['todayBtn'] : true;
		$weekNumbers = isset($attribs['weekNumbers']) ? $attribs['weekNumbers'] : true;
		$showTime = isset($attribs['showTime']) ? $attribs['showTime'] : false;
		$fillTable = isset($attribs['fillTable']) ? $attribs['fillTable'] : true;
		$timeFormat = isset($attribs['timeFormat']) ? $attribs['timeFormat'] : 24;
		$singleHeader = isset($attribs['singleHeader']) ? $attribs['singleHeader'] : false;
		$hint = isset($attribs['placeholder']) ? $attribs['placeholder'] : '';
		$class = isset($attribs['class']) ? $attribs['class'] : 'input-medium';
		$onchange = isset($attribs['onChange']) ? $attribs['onChange'] : 'validateDateOnUpdate(this)';
		$showTime = ($showTime) ? "1" : "0";
		$todayBtn = ($todayBtn) ? "1" : "0";
		$weekNumbers = ($weekNumbers) ? "1" : "0";
		$fillTable = ($fillTable) ? "1" : "0";
		$singleHeader = ($singleHeader) ? "1" : "0";
		if (is_array($attribs)) {
			$attribs['class'] = isset($attribs['class']) ? $attribs['class'] : 'input-medium';
			$attribs = ArrayHelper::toString($attribs);
		}
		// Format value when not nulldate ('0000-00-00 00:00:00'), otherwise blank it as it would result in 1970-01-01.
		if ($value && $value !== Factory::getDbo()->getNullDate() && strtotime($value) !== false) {
			$tz = date_default_timezone_get();
			date_default_timezone_set('UTC');
			$value = strftime($format, strtotime($value));
			date_default_timezone_set($tz);
		}
		$cssFileExt = ($direction === 'rtl') ? '-rtl.css' : '.css';
        // Add language strings
        $strings = [
            // Days
            'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY',
            // Short days
            'SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT',
            // Months
            'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER',
            // Short months
            'JANUARY_SHORT', 'FEBRUARY_SHORT', 'MARCH_SHORT', 'APRIL_SHORT', 'MAY_SHORT', 'JUNE_SHORT',
            'JULY_SHORT', 'AUGUST_SHORT', 'SEPTEMBER_SHORT', 'OCTOBER_SHORT', 'NOVEMBER_SHORT', 'DECEMBER_SHORT',
            // Buttons
            'JCLOSE', 'JCLEAR', 'JLIB_HTML_BEHAVIOR_TODAY',
            // Miscellaneous
            'JLIB_HTML_BEHAVIOR_WK',
        ];

        foreach ($strings as $c) {
            Text::script($c);
        }

        // These are new strings. Make sure they exist. Can be generalised at later time: eg in 4.1 version.
        if ($lang->hasKey('JLIB_HTML_BEHAVIOR_AM')) {
            Text::script('JLIB_HTML_BEHAVIOR_AM');
        }

        if ($lang->hasKey('JLIB_HTML_BEHAVIOR_PM')) {
            Text::script('JLIB_HTML_BEHAVIOR_PM');
        }
        // this adds 2 script files and one css file
        // 'field.calendar' refferences the path media/system/js (or css)/fields/calender.min.js (or.css)
        $document->getWebAssetManager()
            ->registerAndUseScript('field.calendar.helper', $helperPath, [], ['defer' => true])
            ->useStyle('field.calendar' . ($direction === 'rtl' ? '-rtl' : ''))
            ->useScript('field.calendar');
		if (!$handlerloaded) {
			$document->addScriptDeclaration('function validateDateOnUpdate (input) {jQuery(input).valid(); jQuery(".isCal").trigger("update"); return true;}');
			$handlerloaded = true;
		}
		switch ($layout) {
			case 'bt3layout' :
				$main_wrapper_class = "";
				$div_class = (!$readonly) ? ' class="input-group"' : '';
				$needControlWrapping = true;
				$needButtonWrapping = true;
				$buttonWrapperClass='input-group-btn';
				$btnClass = 'btn btn-secondary';
				break;
            case 'bt5' :
			case 'bt4mcindividual' :
				$main_wrapper_class = "";
				$div_class = (!$readonly) ? ' class="input-group"' : '';
				$needControlWrapping = true;
				$needButtonWrapping = true;
				$buttonWrapperClass='input-group-append';
				$btnClass = 'btn btn-secondary';
				break;
			case 'uikit2' :
				$main_wrapper_class = "";
				$div_class = (!$readonly) ? ' class="uk-button-group"' : '';
				$needControlWrapping = false;
				$needButtonWrapping = false;
				$buttonWrapperClass='';
				$btnClass = ' uk-button uk-button-primary';
				break;
			case 'uikit3' :
				$main_wrapper_class = "uk-button-group ";
				$div_class = (!$readonly) ? ' class="uk-button-group"' : '';
				$needControlWrapping = false;
				$needButtonWrapping = true;
				$buttonWrapperClass='uk-inline';
				$btnClass = ' uk-button uk-button-primary';
				break;
			default :
				$main_wrapper_class = "";
				$div_class = (!$readonly) ? ' class="input-append"' : '';
				$needControlWrapping = true;
				$needButtonWrapping = false;
				$buttonWrapperClass='';
				$btnClass = 'btn btn-secondary';
				break;
		}
		$btn_style = ($readonly || $disabled) ? ' style="display:none;"' : '';
		?>

        <div class="field-calendar <?php echo $main_wrapper_class; ?>uk-width-1-1"><?php
		if ($needControlWrapping) {
			echo '<div' . $div_class . '>';
		} ?>
        <input type="text" id="<?php echo $id; ?>" name="<?php
		echo $name; ?>" value="<?php
		echo htmlspecialchars(($value !== '0000-00-00 00:00:00') ? $value : '', ENT_COMPAT, 'UTF-8'); ?>" <?php echo $attribs; ?>
			<?php echo !empty($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : ''; ?>
               data-alt-value="<?php
			   echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>" autocomplete="off"
               onchange="<?php echo $onchange; ?>"/>
		<?php if (!empty($needButtonWrapping)) {
			echo '<span class="' .$buttonWrapperClass . '">';
		} ?>
        <button type="button" class="<?php echo $btnClass; ?>" <?php echo $btn_style; ?>
                id="<?php echo $id; ?>_btn"
                data-inputfield="<?php echo $id; ?>"
                data-date-format="<?php echo $format; ?>"
                data-button="<?php echo $id; ?>_btn"
                data-firstday="<?php echo $lang->getFirstDay(); ?>"
                data-weekend="<?php echo $lang->getWeekEnd(); ?>"
                data-today-btn="<?php echo $todayBtn; ?>"
                data-week-numbers="<?php echo $weekNumbers; ?>"
                data-show-time="<?php echo $showTime; ?>"
                data-show-others="<?php echo $fillTable; ?>"
                data-time-24="<?php echo $timeFormat; ?>"
                data-only-months-nav="<?php echo $singleHeader; ?>"
			<?php echo !empty($minYear) ? 'data-min-year="' . $minYear . '"' : ''; ?>
			<?php echo !empty($maxYear) ? 'data-max-year="' . $maxYear . '"' : ''; ?>
        ><span class="visicon-calendar"></span></button>
		<?php if (!empty($needButtonWrapping)) {
			echo '</span>';
		} ?>

        </div><?php
		if ($needControlWrapping) {
			echo '</div>';
		}
	}
}