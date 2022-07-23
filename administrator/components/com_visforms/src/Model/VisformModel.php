<?php
/**
 * visform model for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Administrator\Model;

defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\String\StringHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Visolutions\Component\Visforms\Administrator\Table\VisformTable;

class VisformModel extends ItemModelBase
{
    public $typeAlias = 'com_visforms.visform';
	protected $aefList;
    
    public function __construct($config = array(), MVCFactoryInterface $factory = null, FormFactoryInterface $formFactory = null) {
        $config['events_map'] = array(
            'delete' => 'visforms',
            'save' => 'visforms',
            'change_state' => 'visforms'
            );
        $config['event_before_save'] = 'onVisformsBeforeJFormSave';
        $config['event_after_save'] = 'onVisformsAfterJFormSave';
        $config['event_before_delete'] = 'onVisformsBeforeJFormDelete';
        $config['event_after_delete'] = 'onVisformsAfterJFormDelete';
        $config['event_change_state'] = 'onVisformsJFormChangeState';
		$this->aefList = \VisformsAEF::getAefList();
        parent::__construct($config, $factory, $formFactory);
    }

	public function batch($commands, $pks, $contexts) {
		// sanitize user ids
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);

		// remove any values of zero
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			$this->setError(Text::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$result = $this->batchCopy($commands, $pks, $contexts);
		if (is_array($result)) {
			$pks = $result;
		}
		else {
			return false;
		}

		if (!empty($commands['assetgroup_id'])) {
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts)) {
                return false;
			}
		}

		if (!empty($commands['language_id'])) {
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts)) {
                return false;
			}
		}

		// clear the cache
		$this->cleanCache();

		return true;
	}

	protected function batchCopy($commands, $pks, $contexts) {
		$app = Factory::getApplication();
		$table = $this->getTable();
		$i = 0;
		// check that the user has create permission for the component
		$extension = Factory::getApplication()->input->get('option', '');
		if (!$this->user->authorise('core.create', $extension)) {
			$this->setError(Text::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		// parent exists so let's proceed
		while (!empty($pks)) {
			// pop the first ID off the stack
			$pk = array_shift($pks);
			$saveResult = false;
			$table->reset();
			// check that the row actually exists
			if (!$table->load($pk)) {
                if ($error = $table->getError()) {
                    // fatal error
					$this->setError($error);
					return false;
				}
				else {
                    // not fatal error
					$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}
			
			if ($table->saveresult == "1") {
				$saveResult = true;
			}
			
			// alter the title & alias
			$data = $this->generateNewTitle( '', $table->name, $table->title);
			$table->title = $data['0'];
			$table->name = $data['1'];

			// reset the ID and hits because we are making a copy
			$table->id = 0;
            $table->hits = 0;

			// check the row.
			if (!$table->check()) {
                $this->setError($table->getError());
				return false;
			}

			// store the row
			if (!$table->store()) {
                $this->setError($table->getError());
				return false;
			}
			
			$cmd = ArrayHelper::getValue($commands, 'copy_fields', 'c');
			$cmdPdf = ($this->aefList[\VisformsAEF::$subscription]) ? ArrayHelper::getValue($commands, 'copy_pdf_templates', 'c') : 'n';
			
			// set the new item ID
			$newId = $table->get('id');
            // create a data table for the copied form if necessary
			if ($saveResult === true) {
				$this->createDataTables($newId);
			}
			
			if ($cmd == "c") {
				// duplicate all fields of copied form
				$this->batchCopyFields ($pk, $newId, $contexts);
            }

			if ($cmdPdf == "c") {
				// duplicate all pdfs of copied form
				$this->batchCopyPdfs ($pk, $newId, $contexts);
			}

			PluginHelper::importPlugin('visforms');
			$results = $app->triggerEvent('onVisformsAfterBatchCopyForm', array($pk, $newId));

			// add the new ID to the array
			$newIds[$i]	= $newId;
			$i++;
		}

		// clean the cache
		$this->cleanCache();

		return $newIds;
	}

	protected function batchCopyFields ($pk, $newId, $contexts) {
		$fieldsModel = new VisfieldModel();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query 
			->select($db->qn('id'))
			->from($db->qn('#__visfields'))
			->where($db->qn('fid') .' = ' .$pk)
            ->order($db->qn('ordering') . ' ASC');
		$db->setQuery($query);
		
		$fields = $db->loadColumn();
        $fieldsModel->batch(array('form_id' => $newId, 'unpublish' => false, 'isFormCopy' => true), $fields, $contexts);
		
		// clean the cache
		$this->cleanCache();
	}

	protected function batchCopyPdfs($pk, $newId, $contexts) {
    	$pdfModel = new VispdfModel();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->qn('id'))
			->from($db->qn('#__vispdf'))
			->where($db->qn('fid') .' = ' .$pk);
		try {
			$db->setQuery($query);
			$pdfs = $db->loadColumn();
			if (!empty($pdfs)) {
				$pdfModel->batch(array('form_id' => $newId, 'unpublish' => false, 'isFormCopy' => true), $pdfs, $contexts);
			}
		}
		catch (\RuntimeException $e) {

		}
		// todo update pdf id in form option "select pdf template"
		// clean the cache
		$this->cleanCache();
	}

	public function updatePdfIdsInFormOptions($fid, $pdfIdMapper) {
    	$item = $this->getItem($fid);
    	if (empty($item)) {
    		return;
	    }
    	$modified = false;
		if (isset($item->frontendsettings['singleRecordPdfTemplate']) && array_key_exists($item->frontendsettings['singleRecordPdfTemplate'], $pdfIdMapper)) {
			$item->frontendsettings['singleRecordPdfTemplate'] = $pdfIdMapper[$item->frontendsettings['singleRecordPdfTemplate']];
			$modified = true;
		}
		if (isset($item->frontendsettings['listPdfTemplate']) && array_key_exists($item->frontendsettings['listPdfTemplate'], $pdfIdMapper)) {
			$item->frontendsettings['listPdfTemplate'] = $pdfIdMapper[$item->frontendsettings['listPdfTemplate']];
			$modified = true;
		}
		$item->frontendsettings = \VisformsHelper::registryStringFromArray($item->frontendsettings);
		if (isset($item->subredirectsettings['pdf_download_link_template']) && array_key_exists($item->subredirectsettings['pdf_download_link_template'], $pdfIdMapper)) {
			$item->subredirectsettings['pdf_download_link_template'] = $pdfIdMapper[$item->subredirectsettings['pdf_download_link_template']];
			$modified = true;
		}
		$item->subredirectsettings = \VisformsHelper::registryStringFromArray($item->subredirectsettings);
		if ($modified) {
			$db = Factory::getDbo();
			try {
				$db->updateObject('#__visforms', $item, 'id');
			}
			catch (\RuntimeException $e) {
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'INFO');
			}
		}
	}

	public function save($data) {
        $app = Factory::getApplication();
        if (isset($data['captcha']) && ($data['captcha'] == "2") && (!PluginHelper::isEnabled('captcha', 'recaptcha'))) {
            $data['captcha'] = "0";
			$app->enqueueMessage(Text::_('COM_VISFORMS_PLG_RECAPTCHA_NOT_ENABLED'), 'warning');
		}
        if (isset($data['editemailresultsettings']) && is_array($data['editemailresultsettings'])) {
            if ((!empty($data['editemailresultsettings']['editemailresult'])) && (empty($data['editemailresultsettings']['editemailto']))) {
                Factory::getApplication()->enqueueMessage(Text::sprintf('COM_VISFORMS_RESULT_MAIL_TO_ADDRESS_REQUIRED', Text::_('COM_VISFORMS_FIELDSET_EDIT_EMAIL')));
            }
        }
        if (isset($data['frontendsettings']) && is_array($data['frontendsettings'])) {
            if (empty($this->aefList[\VisformsAEF::$subscription])) {
                $data['frontendsettings']['ownrecordsonly'] = 0;
	            $data['frontendsettings']['displaypdfexportbutton'] = 0;
	            $data['frontendsettings']['singleRecordPdfTemplate'] = 0;
	            $data['frontendsettings']['listPdfTemplate'] = 0;
	            $data['frontendsettings']['display_csv_export_button'] = 0;
            }
        }
        if (isset($data['layoutsettings']) && is_array($data['layoutsettings'])) {
			if (empty($this->aefList[\VisformsAEF::$subscription])) {
                $data['layoutsettings']['displaysummarypage'] = 0;
				$data['layoutsettings']['displayprogress'] = 0;
                $data['layoutsettings']['mpdisplaytype'] = 0;
				$data['layoutsettings']['displaysublayout'] = 'horizontal';
				$data['layoutsettings']['preventsubmitonenter'] = 0;
				$data['layoutsettings']['defaultresponsive'] = 0;
			}
			if (empty($data['layoutsettings']['mpdisplaytype'])) {
				$data['layoutsettings']['firstpanelcollapsed'] = 0;
			}
        }
		if (isset($data['subredirectsettings']) && is_array($data['subredirectsettings'])) {
			if (empty($this->aefList[\VisformsAEF::$subscription])) {
				$data['subredirectsettings']['allow_content_plugin_custom_redirect'] = '';
		        $data['subredirectsettings']['redirect_to_previous_page'] = 0;
		        $data['subredirectsettings']['textresult_previouspage_link'] = 0;
		        $data['subredirectsettings']['return_link_text'] = '';
		        $data['subredirectsettings']['message_position'] = 0;
	        }
		}
		if (isset($data['savesettings']) && is_array($data['savesettings'])) {
			if (empty($this->aefList[\VisformsAEF::$subscription])) {
				$data['savesettings']['save_exclude_ip'] = 0;
			}
		}
		if (isset($data['exportsettings']) && is_array($data['exportsettings'])) {
			if (empty($this->aefList[\VisformsAEF::$subscription])) {
				$data['exportsettings']['expfilename'] = '';
				$data['exportsettings']['expfilenameappend'] = 0;
			}
		}
		if (empty($this->aefList[\VisformsAEF::$subscription])) {
			$data['redirecttoeditview'] = 0;
		}
	
		// Alter the title for save as copy, and imported form definitions
		$task = $app->input->get('task');
		if ($task == 'save2copy' || $task == 'importform' || $task == 'installDemoForm') {
			list($title, $name) = $this->generateNewTitle( '', $data['name'], $data['title']);
			$data['title']	= $title;
			$data['name']	= $name;
		}

		if (parent::save($data)) {
			// use to save data from plugin specific form fields in different database table
			$fid = $this->getState($this->getName() . '.id');
			$isNew = $this->getState($this->getName() . '.new');
			PluginHelper::importPlugin('visforms');
			$results = $app->triggerEvent('onVisformsSaveJFormExtraData', array($data, $fid, $isNew));
			return true;
		}

		return false;
	}
	
	protected function generateNewTitle($catid, $name, $title) {
		// alter the title & name
		$table = $this->getTable();
		while ($table->load(array('name' => $name))) {
			$title = StringHelper::increment($title);
			$name = StringHelper::increment($name, 'dash');
		}
		return array($title, $name);
	}

	 public function createDataTables ($fid = null, $saveresult = true) {
		if (!$fid) {
            // no form id given
			// todo correct message text!
			Factory::getApplication()->enqueueMessage(Text::_( 'COM_VISFORMS_PROBLEM_WITH' ), 'error');
			return false;
		}
        if (!$this->createDataTable($fid, $saveresult)) {
            // todo throw an error
        }
        if (!$this->createDataTable($fid, $saveresult, true)) {
            // todo throw an error
        }
        return true;
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_visforms.visform', 'visform', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$id = Factory::getApplication()->input->getInt('id', 0);
		// modify the form based on Edit State access controls
		if (!($this->canEditState($id))) {
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		// only display article, menu and image editors-xtd plugins as default and visformsplaceholder button on selected editors
		$plugins = PluginHelper::getPlugin('editors-xtd');
		if (!empty($plugins)) {
			$exclude = array();
			$include = array('visformfields','article','menu','image');
			foreach ($plugins as $plugin) {
				if (!in_array($plugin->name, $include)) {
					$exclude[] = $plugin->name;
				}
			}
			$editorsNoVisformsPlacehoder = array(
				array('description', null),
				array('formprocessingmessage','layoutsettings'),
				array('summarydescription','layoutsettings'),
				array('frontdescription', null));
			$hiddenEditorButtonsNoVisformsPlaceholder = implode(',', $exclude) . ',visformfields';
			foreach ($editorsNoVisformsPlacehoder as $editor1) {
				$form->setFieldAttribute($editor1[0], 'hide', $hiddenEditorButtonsNoVisformsPlaceholder, $editor1[1]);
			}
			$editorsWithVisformsPlacehoder = array(
				array('textresult', null),
				array('emailresulttext', null),
				array('emailreceipttext', null),
				array('editemailresulttext','editemailresultsettings'),
				array('editemailreceipttext','editemailreceiptsettings'));
			$hiddenEditorButtonsWithVisformsPlaceholder = implode(',', $exclude);
			foreach ($editorsWithVisformsPlacehoder as $editor2) {
				$form->setFieldAttribute($editor2[0], 'hide', $hiddenEditorButtonsWithVisformsPlaceholder, $editor2[1]);
			}
		}
        // use to modify form (i.e. add plugin specific form fields)
        PluginHelper::importPlugin('visforms');
		$results = Factory::getApplication()->triggerEvent('onVisformsPrepareJForm', array($form));
		return $form;
	}

    protected function loadFormData() {
        // check the session for previously entered form data
        $app = Factory::getApplication();
        $data = $app->getUserState('com_visforms.edit.visform.data', array());
        if (empty($data)) {
            $data = $this->getItem();
            if (!empty($data) && is_object($data) && empty($data->id)) {
            	//use global Visforms configuration settings as default values in form
	            $configParams = ComponentHelper::getParams('com_visforms')->toObject();
                foreach ($configParams as $name => $value) {
                	//Joomla! item structure from db is: first level is object, second level is array
                	if (is_object($value)) {
                		foreach ($value as $k => $v) {
                			$data->$name[$k] = $v;
		                }
	                }
	                else {
                		//special treatment necessary because we cannot use showon for field nodes in nested fields nodes due to error in Joomla! code
		                if ($name === 'ownrecordsonly') {
			                $data->frontendsettings[$name] = $value;
		                } else {
			                $data->$name = $value;
		                }
	                }
                }
            }
        }
        return $data;
    }

    protected function loadFormFieldsParameters() {
        $item = $this->item;
        $item->exportsettings = \VisformsHelper::registryArrayFromString($item->exportsettings);
        $item->emailreceiptsettings = \VisformsHelper::registryArrayFromString($item->emailreceiptsettings);
        $item->emailresultsettings = \VisformsHelper::registryArrayFromString($item->emailresultsettings);
        $item->editemailreceiptsettings = \VisformsHelper::registryArrayFromString($item->editemailreceiptsettings);
        $item->editemailresultsettings = \VisformsHelper::registryArrayFromString($item->editemailresultsettings);
        $item->frontendsettings = \VisformsHelper::registryArrayFromString($item->frontendsettings);
        $item->layoutsettings = \VisformsHelper::registryArrayFromString($item->layoutsettings);
        $item->spamprotection = \VisformsHelper::registryArrayFromString($item->spamprotection);
        $item->captchaoptions = \VisformsHelper::registryArrayFromString($item->captchaoptions);
        $item->viscaptchaoptions = \VisformsHelper::registryArrayFromString($item->viscaptchaoptions);
	    $item->savesettings = \VisformsHelper::registryArrayFromString($item->savesettings);
	    $item->subredirectsettings = \VisformsHelper::registryArrayFromString($item->subredirectsettings);
        if (empty($this->aefList[\VisformsAEF::$subscription])) {
            $item->redirecttoeditview = 0;
		    $item->redirect_to_previous_page = 0;
		    $item->textresult_previouspage_link = 0;
		    $item->return_link_text = '';
		    $item->message_position = 0;
	    }
    }

	protected function getReorderConditions($table) {
		$condition = array();
		return $condition;
	}
	
	protected function canDelete($record) {
		if (!empty($record->id)) {
            $user = Factory::getApplication()->getIdentity();
			return $user->authorise('core.delete', 'com_visforms.visform.'.(int) $record->id);
		}
		else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record) {
		$user = Factory::getApplication()->getIdentity();
		// check for existing form
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_visforms.visform.'.(int) $record->id);
		}
		// default to component settings if form has no own settings
		else {
			return parent::canEditState($record);
		}
	}

	//we do not delete data tables which were create at some point, even if the form option saveresult is set to false later
	//we have to maintain these data tables then, if there are any changes to the field structure...
    protected function createDataTable ($fid, $saveresult, $save = false) {
    	$app = Factory::getApplication();
        $db	= Factory::getDbo();
        $tn = "#__visforms_".$fid;
		$tnFull = strtolower($db->getPrefix(). 'visforms_'.$fid);
        if ($save === true) {
            $tn .= "_save" ;
           $tnFull .= "_save";
        }
        $tablesAllowed = $db->getTableList();
        if (!empty($tablesAllowed)) {
           $tablesAllowed = array_map('strtolower', $tablesAllowed);
		}

	    if (!in_array($tnFull, $tablesAllowed) && !$saveresult) {
        	return true;
	    }
	 	// create the table to save the data if it does not exist and saveresult is true
		if (!in_array($tnFull, $tablesAllowed)) {
			$dbDriver = $db->getServerType();
			if (!File::exists(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/datatable.sql') || !File::exists(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/savedatatable.sql')) {
				$app->enqueueMessage(Text::sprintf('COM_VISFORMS_SQL_DEFINITION_FILE_MISSING', Text::_('COM_VISFORMS_DATATABLES')), 'error');
				return false;
			}
            // create table
			$query = "create table if not exists ".$db->qn($tn);
			if ($save === true) {
				$query .= @file_get_contents(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/savedatatable.sql');
			} else {
				$query .= @file_get_contents(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/datatable.sql');
			}
			$query = $db->convertUtf8mb4QueryToUtf8($query);
			try {
				$db->setQuery($query);
				$db->execute();
			}
			catch (\RuntimeException $e) {
				$app->enqueueMessage(Text::_( 'COM_VISFORMS_PROBLEM_WITH' ).' ('.$query.') ' . $e->getMessage(), 'error');
				return false;
			}
		}
			
		// check/add existing Fields
	    $query = $db->getQuery(true);
        $query->select('*')
	        ->from($db->qn('#__visfields'))
	        ->where($db->qn('fid') . ' = ' . $fid);
		$fields = $this->_getList($query);

		$tableFields = $db->getTableColumns($tn,false);
		$n=count($fields );
		for ($i=0; $i < $n; $i++) {
			$rowField = $fields[$i];
			$fieldName = "F" . $rowField->id;
			if (!isset( $tableFields[$fieldName] )) {
                $query = "ALTER TABLE ".$db->qn($tn)." ADD ".$db->qn($fieldName)." TEXT NULL";
				try {
					$db->setQuery($query);
					$db->execute();
				}
				catch (\RuntimeException $e) {
					$app->enqueueMessage(Text::_( 'COM_VISFORMS_PROBLEM_WITH' ).' ('.$query.') ' . $e->getMessage(), 'error');
					return false;
				}
			}
		}
		return true;
    }

	public function createSpambotTableIfNotExist()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$dbDriver = $db->getServerType();
		if (!File::exists(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/spambottable.sql')) {
			$app->enqueueMessage(Text::sprintf('COM_VISFORMS_SQL_DEFINITION_FILE_MISSING', Text::_('COM_VISFORMS_SPAMBOTATTEMPTS_TABLE')), 'error');
			return false;
		}
		$query = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_visforms/sql/others/'.$dbDriver.'/spambottable.sql');
		$query = $db->convertUtf8mb4QueryToUtf8($query);
		try {
			$db->setQuery($query);
			$db->execute();
		}
		catch (\RuntimeException $e) {
			$app->enqueueMessage(Text::_( 'COM_VISFORMS_PROBLEM_WITH' ).' ('.$query.') ' . $e->getMessage(), 'error');
			return false;
		}
	}
}