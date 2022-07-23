<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2019 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class VisformsBt5BaseHelper {

	protected $breakPoints;

	public function __construct() {
		$this->breakPoints = array('Sm', 'Md', 'Lg', 'Xl', 'Xxl');
	}
}