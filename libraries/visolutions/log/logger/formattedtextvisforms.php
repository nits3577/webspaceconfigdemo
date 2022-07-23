<?php

/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2021 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Log\Logger\FormattedtextLogger;

class JLogLoggerFormattedtextvisforms extends FormattedtextLogger
{
	/**
	 * @var array Translation array for JLogEntry priorities to SysLog priority names.
	 * @since 11.1
	 */
	protected $priorities = array(
		JLog::EMERGENCY => 'EMG',
		JLog::ALERT => 'ALT',
		JLog::CRITICAL => 'CRI',
		JLog::ERROR => 'ERR',
		JLog::WARNING => 'WRN',
		JLog::NOTICE => 'NTC',
		JLog::INFO => 'INF',
		JLog::DEBUG => 'DBG');
}
?>