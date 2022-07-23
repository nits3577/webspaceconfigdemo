<?php
/**
 * Visform table class
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Table;


defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Access\Rules;
use Visolutions\Component\Visforms\Administrator\Table\TablebaseTable as VisTableBase;


class VisformTable extends VisTableBase
{
	public function __construct(DatabaseDriver $db) {
		$this->_jsonEncode = array('exportsettings','emailreceiptsettings','emailresultsettings', 'editemailreceiptsettings', 'editemailresultsettings', 'frontendsettings', 'layoutsettings', 'spamprotection', 'captchaoptions', 'viscaptchaoptions', 'savesettings', 'subredirectsettings');
		parent::__construct('#__visforms', 'id', $db);
	}

    protected function _getAssetName() {
        return 'com_visforms.visform.'.$this->id;
	}

    protected function _getAssetTitle() {
        return $this->title;
	}

    protected function _getAssetParentId(Table $table = null, $id = null) {
        // we will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');
		// default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();
		// the item has the component as asset-parent
		$assetParent->loadByName('com_visforms');
		// return the found asset-parent-id
		if ($assetParent->id) {
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}

    public function bind($array, $ignore = '') {
        // bind the rules
        if (isset($array['rules'])) {
            $rules = new Rules($array['rules']);
            $this->setRules($rules);
        }
        return parent::bind($array, $ignore);
    }

    function check() {
	    $app = Factory::getApplication();
		$return = true;
        if (empty($this->name)) {
            $this->name = "form_" . self::getNextOrder();
        }
		// remove accented UTF-8 characters in field name
		$this->name = ApplicationHelper::stringURLSafe($this->name, ENT_QUOTES);

		// set label
		if (empty($this->title)) {
            $this->title = $this->name;
		}
        
        // check upload directory
        // convert backslashes to slashes
		$this->uploadpath = preg_replace('#\\\\#', '/', $this->uploadpath);
        // remove slashes at the beginning and the end of string
		$this->uploadpath = rtrim($this->uploadpath,'/');
        $this->uploadpath = ltrim($this->uploadpath,'/');
		$check = trim($this->uploadpath);
		if(!empty($check)) {
		    // todo: verify the code
            $check = Path::clean($check);
            if(!Folder::exists($this->uploadpath)) {
                $directory = JPATH_SITE.'/'.$this->uploadpath;
                if(!Folder::exists($directory)) {
                    // using $app->enqueueMessage results in invalide order of message texts due to a headline, inserted in the table class
                    //$app->enqueueMessage(Text::_('COM_VISFORMS_DIRECTORY_DOESNT_EXISTS'), 'error');
                    $this->setError(Text::_('COM_VISFORMS_DIRECTORY_DOESNT_EXISTS'));
                    $return = false;
                }
			}
		} 
		else {
            // using $app->enqueueMessage results in invalide order of message texts due to a headline, inserted in the table class ba Joomla core
            //$app->enqueueMessage(Text::_('COM_VISFORMS_DIRECTORY_EMPTY'), 'error');
            $this->setError(Text::_('COM_VISFORMS_DIRECTORY_EMPTY'));
			$return = false;
		}

		if ((!empty($this->emailresult)) && (empty($this->emailto))) {
			$app->enqueueMessage(Text::sprintf('COM_VISFORMS_RESULT_MAIL_TO_ADDRESS_REQUIRED', Text::_('COM_VISFORMS_FIELDSET_EMAIL')), 'warning');
		}

		return $return;
	}
	
	public function store($updateNulls = false) {
        $this->addCreatedByFields();
		return parent::store($updateNulls);
	}
}