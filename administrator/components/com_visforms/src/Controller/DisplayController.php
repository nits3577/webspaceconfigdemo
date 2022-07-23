<?php
/**
 * Default controller for Visforms
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Controller;
 
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default controller class for Visforms
 *
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 *
 * @since        Joomla 1.6 
 */

class DisplayController extends BaseController
{
	/**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'viscpanel';

	public function __construct(array $config, MVCFactoryInterface $factory = null, $app = null, $input = null) {
		parent::__construct($config, $factory, $app, $input);
	}

	public function display($cachable = false, $urlparams = false) {
        //get Input from Request
        $visformsInput = Factory::getApplication()->input;
        
        $view	= $visformsInput->get('view', $this->default_view);
        $layout = $visformsInput->get('layout', 'default');
        $id		= $visformsInput->get('id');
        $fid     = $visformsInput->get('fid', 0);
        $canDo = \VisformsHelper::getActions();
		
		// Check for edit form for forms.
		if ($view == 'visform' && $layout == 'edit' && !$this->checkEditId('com_visforms.edit.visform', $id)
		) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=visforms', false));

			return false;
		}
		
		// Check for edit form for fields.
		if ($view == 'visfield' && $layout == 'edit' && !$this->checkEditId('com_visforms.edit.visfield', $id)
		) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=visfields&fid=' . $fid, false));

			return false;
		}
        
        // Check for edit form for datas
        if ($view == 'visdata' && $layout == 'edit' && !$this->checkEditId('com_visforms.edit.visdata', $id)
		) {
			// Somehow the person just went to the form - we don't allow that.
	        $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=visdatas&fid=' . $fid, false));

			return false;
		}

		// Check for edit form for pdftemplates
		if ($view == 'vispdf' && $layout == 'edit' && !$this->checkEditId('com_visforms.edit.vispdf', $id)
		) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=vispdfs&fid=' . $fid, false));

			return false;
		}

		// Check for Core Create ACL for creator
		if ($view == 'creator' && !$canDo->get('core.edit.state')
		) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));

			return false;
		}
		if (empty($visformsInput->get('view', null))) {
			$this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));
		}
		
		parent::display($cachable, $urlparams);
		return $this;
	}
}

?>
