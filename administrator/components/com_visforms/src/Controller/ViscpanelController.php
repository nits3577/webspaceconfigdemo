<?php
/**
 * Visforms controller for VisCpanel
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

namespace Visolutions\Component\Visforms\Administrator\Controller;

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

class ViscpanelController extends BaseController
{
    function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null){
        parent::__construct($config, $factory, $app, $input);
    }

    public function edit_css() {
        $this->setRedirect("index.php?option=com_visforms&task=vistools.editCSS");
    }

    public function dlid() {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        $dlId = $this->input->post->get('downloadid', '', 'string');
        $this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));
        $model = $this->getModel();
        $model->setState('dlid', $dlId);
        if (!$model->storeDlid()) {
            return false;
        }
        return true;
    }

    public function installDemoForm() {
	    Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
	    $this->setRedirect(Route::_('index.php?option=com_visforms&view=viscpanel', false));
	    $hasSub = \VisformsAEF::checkAEF(\VisformsAEF::$subscription);
	    if (!$hasSub || !$this->app->getIdentity()->authorise('core.create', 'com_visforms')) {
		    $this->app->enqueueMessage(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'), 'error');
	    	return false;
	    }

	    $file = JPATH_ADMINISTRATOR . '/components/com_visforms/json/demoform.json';
	    if (!File::exists($file)) {
		    $this->app->enqueueMessage(Text::_('COM_VISFORMS_DEMOFORM_DEFINITION_FILE_MISSING'), 'error');
		    return false;
	    }
	    $jsonDefinition = @file_get_contents($file);
	    if (empty($jsonDefinition)) {
		    $this->app->enqueueMessage(Text::_('COM_VISFORMS_DEMOFORM_DEFINITION_FILE_EMPTY'), 'error');
		    return false;
	    }
	    $datas = json_decode($jsonDefinition, true);
	    if (empty($datas)) {
		    $this->app->enqueueMessage(Text::_('COM_VISFORMS_DEMOFORM_DEFINITION_INVALID'), 'error');
		    return false;
	    }
	    $helper = new \visFormsImportHelper();
	    if ($helper->importForms($datas, true)) {
		    // create forms, fields and data records first
		    $cpanelModel = $this->getModel();
		    $cpanelModel->storeDemoFormInstalled();
		    $this->setRedirect(Route::_('index.php?option=com_visforms&view=visforms', false));
		    $this->app->enqueueMessage(Text::_('COM_VISFORMS_DEMOFORM_INSTALLED'), 'success');
		    return true;
	    }
	    return false;
    }
}