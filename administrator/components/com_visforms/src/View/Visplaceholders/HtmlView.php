<?php
/**
 * Visfields view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de 
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\View\Visplaceholders;

defined('_JEXEC') or die( 'Restricted access' );

use Visolutions\Component\Visforms\Administrator\View\Visfields\HtmlView as FieldsHtmlView;

class HtmlView extends FieldsHtmlView
{
	protected $form;

	function __construct($config = array()) {
        parent::__construct($config);
        $this->viewName     = 'visplaceholders';
    }
}
