<?php
/**
 * Visforms message email class
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
use Joomla\CMS\Language\Text;

class VisformsMessageEmail extends VisformsMessage
{

	public function __construct($name, $args) {
		parent::__construct($name, $args);
		$this->text = 'COM_VISFORMS_FIELD_EMAIL_NOT_VALIDE';
	}

	protected function setMessage() {
		$message = Text::sprintf($this->text, $this->name);
		return $message;
	}
}