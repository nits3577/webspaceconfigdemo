<?php
/**
 * Visforms Layout class Bootstrap default
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

class VisformsLayoutBt5 extends VisformsLayout
{
	protected function getCustomRequiredCss($parent) {
		$fullParent = 'form#' . $parent;
		$css = array();
		//css for required fields
		$css[] = $fullParent . ' div.required .asterix-ancor:after ';
		$css[] = '{content:"*"; color:red; display: inline-block; padding-left: 10px;} ';
		return implode('', $css);
	}

	protected function addCustomCss($parent) {
		$fullParent = 'form#' . $parent;
		$css        = array();
		//add some space between summary page and buttons on multi page layouts
		$css[] = $fullParent . ' #' . $parent .'_summarypage {margin-bottom: 15px;}';
		return implode('', $css);
	}
}