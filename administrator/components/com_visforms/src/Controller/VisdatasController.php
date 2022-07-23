<?php
/**
 * visdatas controller for Visforms
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

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;

class VisdatasController extends AdminController
{

	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null) {
        parent::__construct($config, $factory, $app, $input);
        $fid = Factory::getApplication()->input->getInt('fid', 0);
        $this->view_list = 'visdatas&fid=' . $fid;
        $this->text_prefix = 'COM_VISFORMS_DATA';
	}

	public function getModel($name = 'Visdata', $prefix = 'Administrator', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function export() {
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
        // form id
		$fid = Factory::getApplication()->input->getInt('fid', -1);
        // get the data model
        $model = $this->getModel('Visdatas', 'Administrator');
        // return if user has no export permission
        if(!$model->canExport($fid)) {
        	$this->setMessage(Text::_('COM_VISFORMS_EXPORT_NOT_PERMITTED'), 'warning');
	        $this->setRedirect('index.php?option=com_visforms&view=visdatas&fid='.$fid);
	        return false;
        }
        
        $cIds = Factory::getApplication()->input->get('cid', array(), 'ARRAY');
        ArrayHelper::toInteger($cIds);

        // create a export data string (body of csv file)
		$model->setCsvHelper(null);
        $buffer = $model->createExportBuffer($cIds);
        $fileName = $model->getCsvFileName();
			
		header("Expires: Sun, 1 Jan 2000 12:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=" . $fileName);
        echo $buffer;
        Factory::getApplication()->close();
	}

    public function forms() {
        $this->setRedirect('index.php?option=com_visforms&view=visforms');
    }

	public function fields() {
		$fid = $this->input->getInt('fid', -1);
		$this->setRedirect( "index.php?option=com_visforms&view=visfields&fid=".$fid);
		return true;
	}

    public function form() {
        $fid = Factory::getApplication()->input->getInt('fid', 0);
        $app = Factory::getApplication();
        $context = "com_visforms.edit.visform.id";
        if ($fid != 0) {
            $app->setUserState($context, (array) $fid);
        }
        $this->setRedirect('index.php?option=com_visforms&view=visform&layout=edit&id=' . $fid);
    }
    
    protected function postDeleteHook(BaseDatabaseModel $model, $cid = NULL) {
        foreach ($cid as $id) {
            $model->deleteOrgData($id);
        }
    }
    
    public function reset(){
        $model = $this->getModel('Visdata', 'Administrator');
        $cid = $this->input->get('cid', array(), 'array');
        foreach ($cid as $id) {
            $model->restoreToUserInputs($id);
            $model->deleteOrgData($id);
        }
        $nText = 'COM_VISFORMS_DATA_N_ITEMS_RESET';
        $this->setMessage(Text::plural($nText, count($cid)));
        $this->setRedirect('index.php?option=com_visforms&view='. $this->view_list);
    }
}