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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Installer\Installer;

class com_visformsInstallerScript {
	private $name;
    private $release;
	private $oldRelease;
	private $minimum_joomla_release = 4;
	private $maximum_joomla_release = 4;
	private $min_visforms_version;
	private $max_downgrade_version= '4.1.0';
	private $vfsubminversion;
	private $versionsWithPostflightFunction;
	private $last_modified_view_files_version;
	private $status;
	private $forms;
	private $loggerName;

	// construction

	public function __construct($adapter) {
		$this->initializeLogger($adapter);
		$this->addLogEntry('*** starting com_visforms component script ***', Log::INFO);
	}

	// interface

	public function preflight($route, $adapter) {
		$this->name = $adapter->getManifest()->name;
		$this->release = $adapter->getManifest()->version;
		$this->oldRelease = "";
		$this->minimum_joomla_release = $adapter->getManifest()->attributes()->version;
		$this->min_visforms_version = $adapter->getManifest()->vfminversion;
		$max_downgrade_version = $this->getLastCompatibleVersion();
		$this->max_downgrade_version = (!empty($max_downgrade_version)) ? $max_downgrade_version : $this->release;
		$this->vfsubminversion = $adapter->getManifest()->vfsubminversion;

		// list all updates with special post flight functions here
		$this->versionsWithPostflightFunction = array('4.1.0','4.1.5','4.1.7');
		$this->last_modified_view_files_version = $adapter->getManifest()->last_modified_view_files_version;
		$this->status = new stdClass();
		$this->status->fixTableVisforms = array();
		$this->status->modules = array();
		$this->status->plugins = array();
		$this->status->tables = array();
		$this->status->folders = array();
		$this->status->component = array();
		$this->status->messages = array();
		$this->forms = $this->getForms();
		$jversion = new Version;
		$date = new JDate('now');
		$app = Factory::getApplication();
		$this->addLogEntry('*** Start ' . $route . ' of extension ' . $this->name . ' ' . $this->release . ': ' . $date . ' ***', Log::INFO);

		// abort if system requirements are not met
		if ($route != 'uninstall') {
            if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
                $msg = Text::_('COM_VISFORMS_WRONG_JOOMLA_VERSION') . $this->minimum_joomla_release;
                $app->enqueueMessage($msg, 'ERROR');
                $this->addLogEntry($msg, Log::ERROR);
                return false;
            }
			if ($jversion::MAJOR_VERSION > $this->maximum_joomla_release) {
                $msg = Text::sprintf('COM_VISFORMS_WRONG_MAX_JOOMLA_VERSION', $this->maximum_joomla_release);
				$app->enqueueMessage($msg, 'ERROR');
				$this->addLogEntry($msg, Log::ERROR);
				return false;
			}

			// abort if the component being installed is lower than the last downgradable version
			if ($route == 'update') {
			    // try to get old version from manifest_cache in #__extentsion; fall back on max_downgrad_version, stored in a visforms database table
				$this->oldRelease = ($this->getExtensionParam('version')) ?? $max_downgrade_version;
				$this->addLogEntry("Installed version is: " . $this->oldRelease . " Update version is : " . $this->release, Log::INFO);
				if (version_compare($this->release, $this->max_downgrade_version, 'lt')) {
				    $msg = Text::sprintf('COM_VISFORMS_WRONG_VERSION_NEW', $this->oldRelease, $this->release);
					$app->enqueueMessage($msg, 'ERROR');
					$this->addLogEntry($msg, Log::ERROR);
					return false;
				}

                // abort if the installed version is to old
                if (version_compare($this->oldRelease, $this->min_visforms_version, 'lt')) {
				    $msg = Text::sprintf('COM_VISFORMS_INCOMPATIBLE_VERSION_NEW', $this->min_visforms_version, $this->oldRelease, $this->release);
	                $app->enqueueMessage($msg, 'ERROR');
                    $this->addLogEntry($msg, Log::ERROR);
                    return false;
                }
                
                // abort if we have a 4.0.x Version
                if (version_compare($this->oldRelease, '4.0.0', 'ge') && version_compare($this->oldRelease, '4.1.0', 'lt')) {
                    $msg = Text::sprintf('COM_VISFORMS_INCOMPATIBLE_VERSION_NEW', $this->min_visforms_version, $this->oldRelease, $this->release);
                    $app->enqueueMessage($msg, 'ERROR');
                    $this->addLogEntry($msg, Log::ERROR);
                    return false;
                }

				// set permissions for css files (which might be edited through backend and set to readonly) so they can be updated
				$files = array('bootstrapform.css', 'bootstrapform.min.css', 'jquery.searchtools.css', 'jquery.searchtools.min.css', 'visdata.css', 'visdata.min.css', 'visforms.bootstrap4.css', 'visforms.bootstrap4.min.css', 'visforms.css', 'visforms.min.css', 'visforms.default.css', 'visforms.default.min.css', 'visforms.full.bootstrap4.css', 'visforms.full.bootstrap4.min.css', 'visforms.uikit2.css', 'visforms.uikit2.min.css', 'visforms.uikit3.css', 'visforms.uikit3.min.css');
				foreach ($files as $cssfile) {
					@chmod(Path::clean(JPATH_ROOT . '/media/com_visforms/css/' . $cssfile), 0755);
				}
			}
			else {
				$this->addLogEntry("*** Start Install: " . $date . " ***", Log::INFO);
				$this->addLogEntry("Version is: " . $this->release, Log::INFO);
			}
			// create installation success message (only display if complete installation is executed successfully)
			if ($route == 'update') {
				$msg = Text::_('COM_VISFORMS_UPDATE_VERSION') . $this->release . Text::_('COM_VISFORMS_SUCESSFULL');
				if (version_compare($this->oldRelease, $this->last_modified_view_files_version, 'lt')) {
					$msg .= '<br /><strong style="color: red;">' . Text::_('COM_VISORMS_DELETE_TEMPLATE_OVERRIDES') . '</strong>';
				}
			}
			else if ($route == 'install') {
					$msg = Text::_('COM_VISFORMS_INSTALL_VERSION') . $this->release . Text::_('COM_VISFORMS_SUCESSFULL');
            }
            if (!empty($msg)) {
	            $this->status->component = array('name' => 'visForms', 'type' => $route, 'msg' => $msg);
            }
		}

		if ($route == 'uninstall') {
			$language = Factory::getApplication()->getLanguage();
			$language->load('com_visforms', JPATH_ADMINISTRATOR . '/components/com_visforms' , 'en-GB', true);
			$language->load('com_visforms', JPATH_ADMINISTRATOR . '/components/com_visforms' , null, true);
			$language->load('com_visforms', JPATH_ROOT  , 'en-GB', true);
			$language->load('com_visforms', JPATH_ROOT  , null, true);
		}

		return true;
	}

	public function postflight($route, $adapter) {
		if ($route == 'update') {
			// run specific component adaptation for specific update versions
			if (!empty($this->oldRelease) && (version_compare($this->oldRelease, '3.14.0', 'ge'))) {
				foreach ($this->versionsWithPostflightFunction as $versionWithDatabaseChanges) {
					if (version_compare($this->oldRelease, $versionWithDatabaseChanges, 'lt')) {
						$postFlightFunctionPostfix = str_replace('.', '_', $versionWithDatabaseChanges);
						$postFlightFunctionName = 'postFlightForVersion' . $postFlightFunctionPostfix;
						if (method_exists($this, $postFlightFunctionName)) {
							$this->$postFlightFunctionName();
						}
					}
				}
			}
			// we must check if tables are not yet converted to utf8mb4 every time, because the conversion can only be performed if the mysql engine supports utf8mb4
			$this->convertTablesToUtf8mb4();
			$this->warnSubUpdateRequired($route);
		}

		if ($route == 'install') {
			$this->createFolder(array('images', 'visforms'));
			$this->setLastCompatibleVersion($this->release);
		}

		if ($route == 'install' || $route == 'update') {
			// set "add" parameter in forms menu item in administration
			$db = Factory::getDbo();
            $where = $db->quoteName('menutype') . ' = ' . $db->quote('main') . ' AND ' . $db->quoteName('client_id') . ' = 1 AND ' . $db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_visforms&view=visforms') . ' AND ' . $db->quoteName('alias') . ' = ' . $db->quote('com-visforms-submenu-forms');
		    $this->setParams(array("menu-quicktask" => "index.php?option=com_visforms&task=visform.add"), 'menu', 'params', $where);
			$this->installationResults($route);
        }

		return true;
	}

	public function uninstall( $adapter) {
		$this->status = new stdClass();
		$this->status->modules = array();
		$this->status->plugins = array();
		$this->status->tables = array();
		$this->status->folders = array();
		$this->status->component = array();
		$this->status->messages = array();
		$this->forms = $this->getForms();

		$date = new JDate('now');
		$this->addLogEntry('*** Start uninstall of extension Visforms: ' . $date . ' ***', Log::INFO);
		$db = Factory::getDbo();

		//delete all visforms related tables in database
		$dataTables = $this->getPrefixFreeDataTableList();
		if (!empty($dataTables)) {
			$this->addLogEntry("*** Try to delete data tables ***", Log::INFO);
			foreach ($dataTables as $tn) {
			    $this->dropTable($tn);
			}
		}
		$visTables = array('#__visfields', '#__visforms', '#__visverificationcodes',
            '#__visforms_lowest_compat_version', '#__visforms_utf8_conversion', '#__visforms_spambot_attempts',
            '#__viscreator', '#__vispdf');
		foreach ($visTables as $visTable) {
		    $this->dropTable($visTable);
        }

		//delete folders in image folder
		$this->addLogEntry("*** Try to delete custom files and folders ***", Log::INFO);
		$folder = JPATH_ROOT .  '/images/visforms';
		if (JFolder::exists($folder)) {
			$result = array();
			try {
				$result[] = Folder::delete($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					$this->addLogEntry("Folder successfully removed: " . $folder, Log::INFO);
				}
				else {
					$this->addLogEntry('Problems removing folder: ' . $folder, Log::ERROR);
				}
			}
			catch (RuntimeException $e) {
				$this->addLogEntry('Problems removing folder: ' . $folder . ', ' . $e->getMessage(), Log::ERROR);
			}

		}

		// delete visuploads folder
		$folder = JPATH_ROOT . '/visuploads';
		if (Folder::exists($folder)) {
			$result = array();
			try {
				$result[] = Folder::delete($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					$this->addLogEntry("Folder successfully removed: " . $folder, Log::INFO);
				}
				else {
					$this->addLogEntry('Problems removing folder: ' . $folder, Log::ERROR);
				}
			}
			catch (RuntimeException $e) {
				$this->addLogEntry('Problems removing folder: ' . $folder . ', ' . $e->getMessage(), Log::ERROR);
			}
		}

		$this->uninstallationResults();
	}

	// implementation

	private function dropTable($table) {
		$db = Factory::getDbo();
		try {
			$db->setQuery("drop table if exists $table");
			$db->execute();
			$this->status->tables[] = array('message' => Text::sprintf('COM_VISFORMS_TABLE_DROPPED', $table));
			$this->addLogEntry('Table dropped: ' . $table, Log::INFO);
		}
		catch (RuntimeException $e) {
			$this->status->tables[] = array('message' => Text::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage()));
			$this->addLogEntry('Unable to drop table: '.$table.', ' . $e->getMessage(), Log::ERROR);
		}
    }

    private function postFlightForVersion4_1_0() {
        $this->uninstallSearchPlugin();
	    $this->addLogEntry('*** Perform postflight for Version 4.1.0 ***', Log::INFO);
	    $this->setLastCompatibleVersion('4.1.0');
    }

    private function postFlightForVersion4_1_5() {
        $this->addLogEntry('*** Perform postflight for Version 4.1.5 ***', Log::INFO);
        $this->convertCheckoutFieldsInDataTableToAllowNull();
        $this->deleteFolder('/components/com_visforms/layouts/joomla');
        $this->setLastCompatibleVersion('4.1.5');
    }

    private function postFlightForVersion4_1_7() {
        $this->addLogEntry('*** Perform postflight for Version 4.2.0 ***', Log::INFO);
        $this->addLogEntry("*** Try convert option f_hidden_filluid to f_hidden_fillwith ***", Log::INFO);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName(array('id', 'defaultvalue')))
            ->from($db->quoteName('#__visfields'))
            ->where($db->quoteName('typefield') . ' = ' . $db->q('hidden'));
        $results = new stdClass();
        try {
            $db->setQuery($query);
            $results = $db->loadObjectList();
            $this->addLogEntry(count($results) . ' recordsets to process', Log::INFO);
        }
        catch (RuntimeException $e) {
            $this->addLogEntry('Unable to convert option f_hidden_filluid to f_hidden_fillwith, ' . $e->getMessage(), Log::ERROR);
        }
        if ($results) {
            foreach ($results as $result) {
                $params = json_decode($result->defaultvalue, true);
                if (isset($params['f_hidden_filluid'])) {
                    $params['f_hidden_fillwith'] = $params['f_hidden_filluid'];
                    unset($params['f_hidden_filluid']);
                }
                else {
                    $params['f_hidden_fillwith'] = "0";
                }
                // store the combined new and existing values back as a JSON string
                $paramsString = json_encode($params);
                try {
                    $db->setQuery('UPDATE #__visfields SET defaultvalue = ' .
                        $db->quote($paramsString) . ' WHERE id=' . $result->id);
                    $db->execute();
                    $this->addLogEntry("Option f_hidden_filluid successfully converted to f_hidden_fillwith for field with id " . $result->id, Log::INFO);
                }
                catch (RuntimeException $e) {
                    $this->addLogEntry('Problems converting f_hidden_filluid to f_hidden_fillwith for field with id' . $result->id . ': ' . $e->getMessage(), Log::ERROR);
                }
            }
        }
        $this->setLastCompatibleVersion('4.2.0');
    }

	private function getLowerCaseTableList() {
		$db = Factory::getDbo();
		$tablesAllowed = $db->getTableList();
		if (!empty($tablesAllowed)) {
			return array_map('strtolower', $tablesAllowed);
		}
		else {
		    return false;
        }
    }

    private function getForms() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('*')
		    ->from($db->qn('#__visforms'));
	    try {
		    $db->setQuery($query);
		    return $db->loadObjectList();
	    }
	    catch (RuntimeException $e) {
		    $this->addLogEntry('Unable to load form list from database: ' . $e->getMessage(), Log::INFO);
		    return false;
	    }
    }

    private function getPrefixFreeDataTableList () {
	    $prefixFreeTableList = array();
	    $forms = $this->forms;
	    if (empty($forms)) {
	        return $prefixFreeTableList;
        }
	    $db = Factory::getDbo();
	    $tableList = $this->getLowerCaseTableList();
	    if (empty($tableList)) {
		    return $prefixFreeTableList;
	    }
        foreach ($forms as $form) {
            $tnfulls = array(strtolower($db->getPrefix() . "visforms_" . $form->id), strtolower($db->getPrefix() . "visforms_" . $form->id . "_save"));
            foreach ($tnfulls as $tnfull) {
                if (in_array($tnfull, $tableList)) {
                    $prefixFreeTableList[] = str_replace(strtolower($db->getPrefix()), "#__", $tnfull);
                }
            }
        }

	    return $prefixFreeTableList;
    }

	private function installationResults($route) {
		$language = Factory::getApplication()->getLanguage();
		$language->load('com_visforms');
		$image = ($route == 'update') ? 'logo-banner-u.png' : 'logo-banner.png';
		$src = "https://www.vi-solutions.de/images/f/$this->release/$image";

		$extension_message = array();
		$extension_message[] = ($route == 'update') ? '<img src="'.$src.'" alt="visForms" />' : '<h2><img src="'.$src.'" alt="visForms" style="margin-right: 0.5rem;"/>' . Text::_('COM_VISFORMS_INSTALL_MESSAGE') . '</h2>';
		$extension_message[] = '<h2>' . (($route == 'update') ? Text::_('COM_VISFORMS_UPDATE_STATE') : Text::_('COM_VISFORMS_INSTALLATION_STATUS')) . '</h2>';
        $extension_message[] = '<div>'.Text::_('COM_VISFORMS_EXTENSION').': <strong>' . $this->status->component['msg'] . '</strong></div>';

		if (count($this->status->folders)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_FILESYSTEM').'</h3>';
            foreach ($this->status->folders as $folder) {
                $folder_message = (($folder['result']) ? '<strong>' . Text::_('COM_VISFORMS_CREATED') . '</strong>' : '<strong style="color: red">' . Text::_('COM_VISFORMS_NOT_CREATED'). '</strong>');
                $extension_message[] = '<div>'.ucfirst($folder['folder']).': <strong>' . $folder_message . '</strong></div>';
            }
        }
        if (count($this->status->fixTableVisforms)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_UPDATE_FIX_FOR_FORM_DATA').'</h3>';
            foreach ($this->status->fixTableVisforms as $recordset) {
                $table_message = ($recordset['result']) ? '<strong>' . $recordset['resulttext']. '</strong>' : '<strong style="color: red">' . $recordset['resulttext']. '</strong>';
                $extension_message[] = '<div>'.Text::_('COM_VISFORMS_FORM_WITH_ID') . $recordset['form'] . ': ' . $table_message . '</div>';
            }
        }
        if (count($this->status->messages)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_MESSAGES').'</h3>';
            foreach ($this->status->messages as $message) {
                $extension_message[] = '<div><strong style="color: red">' . $message['message'] . '</strong></div>';
            }
        }

		Factory::getApplication()->enqueueMessage(implode(' ', $extension_message));
	}

	private function uninstallationResults() {
		$language = Factory::getApplication()->getLanguage();
		$language->load('com_visforms');
		$src = "https://www.vi-solutions.de/images/f/$this->release/logo-banner-d.png";

		$extension_message = array();
        $extension_message[] = '<h2><img src="'.$src.'" alt="visForms" align="right" style="margin-right: 0.5rem;"/>' . Text::_('COM_VISFORMS_REMOVAL_STATUS') . '</h2>';
        $extension_message[] = '<div>'.Text::_('COM_VISFORMS_EXTENSION').': <strong>'. Text::_('COM_VISFORMS_REMOVED').'</strong></div>';

		if (count($this->status->tables)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_TABLES').'</h3>';
            foreach ($this->status->tables as $table) {
                $extension_message[] = '<div>' . ucfirst($table['message']) . '</div>';
            }
        }
		/*if (count($this->status->folders)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_FILESYSTEM').'</h3>';
            foreach ($this->status->folders as $folder) {
                $folder_message = '';
                $folder_message = (($folder['result']) ?  Text::_('COM_VISFORMS_DELETED') : '<strong style="color: red">' . Text::_('COM_VISFORMS_NOT_DELETED'). '</strong>');
                $extension_message[] = '<div>'.ucfirst($folder['folder']).': '. $folder_message . '</div>';
            }
        }*/
		if (count($this->status->messages)) {
            $extension_message[] = '<h3>'.Text::_('COM_VISFORMS_MESSAGES').'</h3>';
            foreach ($this->status->messages as $message) {
                $extension_message[] = '<div><strong style="color: red">' . $message['message'] . '</strong></div>';
            }
        }

		Factory::getApplication()->enqueueMessage(implode(' ', $extension_message));
	}

	private function createFolder($folders = array()) {
		$this->addLogEntry("*** Try to create folders ***", Log::INFO);
		// create visforms folder in image directory and copy an index.html into it
		$folder = JPATH_ROOT;
		foreach ($folders as $name) {
			$folder .= '/' . $name;
		}

		if (($folder != JPATH_ROOT) && !(Folder::exists($folder))) {
			$result = array();
			try {
				$result[] = Folder::create($folder);
				$this->status->folders[] = array('folder' => $folder, 'result' => $result[0]);
				if ($result[0]) {
					$this->addLogEntry("Folder successfully created: " . $folder, Log::INFO);
				} 
				else {
					$this->addLogEntry("Problems creating folder: " . $folder, Log::ERROR);
				}
			} 
			catch (RuntimeException $e) {
				$this->addLogEntry("Problems creating folders, " . $e->getMessage(), Log::ERROR);
			}

			$src = JPATH_ROOT . '/media/com_visforms/index.html';
			$dest = Path::clean($folder .  '/index.html');

			try {
				$result[] = File::copy($src, $dest);
				$this->status->folders[] = array('folder' => $folder . '/index.html', 'result' => $result[1]);
				if ($result[1]) {
					$this->addLogEntry("File successfully copied: " . $dest, Log::INFO);
				} 
				else {
					$this->addLogEntry("Problems copying file: " . $dest, Log::ERROR);
				}
			} 
			catch (RuntimeException $e) {
				$this->addLogEntry("Problems copying files, " . $e->getMessage(), Log::ERROR);
			}
		}
	}

	private function getExtensionParam($name, $eid = 0) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		// check if a extenstion id is given. If yes we want a parameter from this extension
		if ($eid != 0) {
			$query->where($db->quoteName('extension_id') . ' = ' . $db->quote($eid));
		} 
		else {
			// we want a parameter from component visForms
			$query->where($this->getComponentWhereStatement());
		}
		try {
			$db->setQuery($query);
			$manifest = json_decode($db->loadResult(), true);
			if ($manifest[$name]) {
                return $manifest[$name];
            }
		} 
		catch (RuntimeException $e) {
			$message = Text::sprintf('COM_VISFORMS_UNABLE_TO_GET_VALUE_OF_PARAM', $name) . " " . Text::sprintf('COM_VISFORMS_DB_FUNCTION_FAILED', $e->getMessage());
			$this->status->messages[] = array('message' => $message);
			$this->addLogEntry('Unable to get value of param ' . $name . ', ' . $e->getMessage(), Log::ERROR);
		}

		return false;
	}

	private function setParams($param_array, $table, $fieldName, $where = "") {
		if (count($param_array) > 0) {
			$this->addLogEntry("*** Try to add params to table: #__" . $table . " ***", Log::INFO);
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query
				->select($db->quoteName(array('id', $fieldName)))
				->from($db->quoteName('#__' . $table));
			if ($where != "") {
				$query->where($where);
			}
			$results = new stdClass();
			try {
				$db->setQuery($query);
				$results = $db->loadObjectList();
				$this->addLogEntry(count($results) . ' recordsets to process', Log::INFO);
			}
			catch (RuntimeException $e) {
				$this->addLogEntry('Unable to load param fields, ' . $e->getMessage(), Log::ERROR);
			}
			if ($results) {
				foreach ($results as $result) {
					$params = json_decode($result->$fieldName, true);
					// add the new variable(s) to the existing one(s)
					foreach ($param_array as $name => $value) {
						$params[(string)$name] = (string)$value;
					}
					// store the combined new and existing values back as a JSON string
					$paramsString = json_encode($params);
					try {
						$db->setQuery('UPDATE #__' . $table . ' SET ' . $fieldName . ' = ' .
							$db->quote($paramsString) . ' WHERE id=' . $result->id);
						$db->execute();
						$this->addLogEntry("Params successfully added", Log::INFO);
					}
					catch (RuntimeException $e) {
						$this->addLogEntry('Problems with adding params ' . $e->getMessage(), Log::ERROR);
					}
				}
			}
		}
	}

	private function getComponentWhereStatement() {
		// create where statement to select visforms component record in #__extensions table
		$db = Factory::getDbo();
		$where = $db->quoteName('type') . ' = ' . $db->quote('component') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote('com_visforms') . ' AND ' . $db->quoteName('name') . ' = ' . $db->quote('visforms');
		return $where;
	}

	private function deleteFile($fileToDelete) {
        $oldfile = Path::clean(JPATH_ROOT . $fileToDelete);
        if (File::exists($oldfile)) {
            try {
                File::delete($oldfile);
                $this->addLogEntry($oldfile . " deleted", Log::INFO);
            }
            catch (RuntimeException $e) {
                $this->addLogEntry('Unable to delete ' . $oldfile . ': ' . $e->getMessage(), Log::INFO);
            }
        }
        else {
            $this->addLogEntry($oldfile . " does not exist.", Log::INFO);
        }
    }

    private function deleteFolder($folderToDelete) {
        $folder = Path::clean(JPATH_ROOT . $folderToDelete);
        if (Folder::exists($folder)) {
            try {
                Folder::delete($folder);
                $this->addLogEntry($folder . "deleted", Log::INFO);
            } catch (RuntimeException $e) {
                $this->addLogEntry('Unable to delete ' . $folder . ': ' . $e->getMessage(), Log::INFO);
            }
        }
        else {
            $this->addLogEntry($folder . " does not exist.", Log::INFO);
        }
    }

	private function addColumns($columnsToAdd = array(), $table = "visforms") {
		if (count($columnsToAdd) > 0) {
			$this->addLogEntry("*** Try to add new fields to table: #__" . $table . " ***", Log::INFO);
			$this->addLogEntry(count($columnsToAdd) . " fields to add", Log::INFO);
			$db = Factory::getDbo();
			foreach ($columnsToAdd as $columnToAdd) {
				// we need at least a column name
				if (!(isset($columnToAdd['name'])) || ($columnToAdd['name'] == "")) {
					continue;
				}
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__' . $table) . "ADD COLUMN " . $db->quoteName($columnToAdd['name']) .
					((isset($columnToAdd['type']) && ($columnToAdd['type'] != "")) ? " " . $columnToAdd['type'] : " text") .
					((isset($columnToAdd['length']) && ($columnToAdd['length'] != "")) ? "(" . $columnToAdd['length'] . ")" : "") .
					((isset($columnToAdd['attribute']) && ($columnToAdd['attribute'] != "")) ? " " . $columnToAdd['attribute'] : "") .
					((isset($columnToAdd['notNull']) && ($columnToAdd['notNull'] == true)) ? " not NULL" : "") .
					((isset($columnToAdd['default']) && ($columnToAdd['default'] !== "")) ? " DEFAULT " . $db->quote($columnToAdd['default']) : " DEFAULT ''"));
				try {
					$db->setQuery($queryStr);
					$db->execute();
					$this->addLogEntry("Field added: " . $columnToAdd['name'], Log::INFO);
				}
				catch (RuntimeException $e) {
					$this->addLogEntry("Unable to add field: " . $columnToAdd['name'] . ', ' . $e->getMessage(), Log::ERROR);
				}
			}
		}
	}

	private function dropColumns($columnsToDrop = array(), $table = "visforms") {
		$this->addLogEntry("*** Try to drop fields from table #__" . $table . " ***", Log::INFO);
		if (count($columnsToDrop) > 0) {
			$this->addLogEntry(count($columnsToDrop) . " fields to drop", Log::INFO);
			$db = Factory::getDbo();
			foreach ($columnsToDrop as $columnToDrop) {
				$queryStr = ("ALTER TABLE " . $db->quoteName('#__' . $table) . "DROP COLUMN " . $db->quoteName($columnToDrop));
				try {
					$db->setQuery($queryStr);
					$db->execute();
					$this->addLogEntry("Field successfully dropped: " . $columnToDrop, Log::INFO);
				}
				catch (RuntimeException $e) {
					$this->addLogEntry("Problems dropping field: " . $columnToDrop . ', ' . $e->getMessage(), Log::ERROR);
				}
			}
		}
		else {
			$this->addLogEntry('No fields to drop', Log::INFO);
		}
	}

	private function enableExtension($extWhere) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . " = 1")
			->where($extWhere);
		try {
			$db->setQuery($query);
			$db->execute();
			$this->addLogEntry("Extension successfully enabled", Log::INFO);
		}
		catch (RuntimeException $e) {
			$this->addLogEntry("Unable to enable extension " . $e->getMessage(), Log::ERROR);
		}
	}

	private function convertTablesToUtf8mb4() {
	    // Joomla! will use character set utf8 as default, if utf8mb4 is not supported
        // if we have successfully converted to utf8md4, we set a flag in the database
		$db = Factory::getDbo();
		$serverType = $db->getServerType();
		if ($serverType != 'mysql') {
			return;
		}

		try {
			$db->setQuery('SELECT ' . $db->quoteName('converted')
				. ' FROM ' . $db->quoteName('#__visforms_utf8_conversion')
			);
			$convertedDB = $db->loadResult();
		}
		catch (Exception $e) {
			// Render the error message from the Exception object
			$this->addLogEntry("Unable to run sql query: " . $e->getMessage(), Log::ERROR);
			return;
		}

		if ($db->hasUTF8mb4Support()) {
			$converted = 2;
		}
		else {
			$converted = 1;
		}

		if ($convertedDB == $converted) {
			return;
		}
		$tablelist = $db->getTableList();
		foreach ($tablelist as $table) {
			if ((strpos($table, '_visforms') !== false) || (strpos($table, '_visfields') !== false) || (strpos($table, '_viscreator') !== false) || (strpos($table, '_vispdf') !== false)) {
				if (!$this->runQuery('ALTER TABLE ' . $table . ' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
				    $converted = 0;
                }
				if (!$this->runQuery('ALTER TABLE ' . $table . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')){
				    $converted = 0;
                }

			}
			if (strpos($table, '_visverificationcode') !== false) {
			    // table has a key on a varchar field. This may result in data loss on conversion.
                // Therefore we must drop the key, enlarge column and set the key later again.
                // Character set of key column is set to utf8mb4_bin not utf8mb4_unicode_ci
				if (!$this->runQuery('ALTER TABLE ' . $table . ' DROP KEY `idx_email`')) {
					$converted = 0;
				}
				if (!$this->runQuery('ALTER TABLE ' . $table . '  MODIFY `email` varchar(400) NOT NULL DEFAULT ""')) {
					$converted = 0;
				}
				if (!$this->runQuery('ALTER TABLE ' . $table . ' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
					$converted = 0;
				}
				if (!$this->runQuery('ALTER TABLE ' . $table . '  MODIFY `email` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT ""')) {
					$converted = 0;
				}
				if (!$this->runQuery('ALTER TABLE ' . $table . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')){
					$converted = 0;
				}
				if (!$this->runQuery('ALTER TABLE ' . $table . ' ADD KEY `idx_email` (`email`(100))')) {
					$converted = 0;
				}
			}
		}
        try {
	        $db->setQuery('UPDATE ' . $db->quoteName('#__visforms_utf8_conversion')
		        . ' SET ' . $db->quoteName('converted') . ' = ' . $converted . ';')->execute();
        }
        catch (Exception $e) {
	        $this->addLogEntry("Unable to run sql query: " . $e->getMessage(), Log::ERROR);
        }
    }

	private function runQuery($sql) {
		$this->addLogEntry('Try to run sql query: ' . $sql, Log::INFO);
		$db = Factory::getDbo();
		$query = $sql;
		try {
			$db->setQuery($query);
			$db->execute();
			return true;
		}
		catch (Exception $e) {
			$this->addLogEntry("Unable to run sql query: " . $e->getMessage(), Log::ERROR);
			return false;
		}
	}

	function cmp($a, $b) {
		if (strlen($a) == strlen($b)) {
			return 0;
		}

		return (strlen($a) > strlen($b)) ? 1 : -1;
	}

	private function warnSubUpdateRequired ($route){
		$this->addLogEntry('Check if Subscription update is necessary', Log::INFO);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('manifest_cache'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element') . ' = ' . $db->q('pkg_vfsubscription'))
			->where($db->qn('type') . ' = ' . $db->q('package'));
		try {
			$db->setQuery($query);
			$manifest = json_decode($db->loadResult(), true);
			$version = $manifest['version'];
			if (!empty($version) && version_compare($version, $this->vfsubminversion, 'lt')) {
				$msg = '<br /><strong style="color: red;"><br />' . Text::sprintf('COM_VISORMS_SUBSCRIPTION_UPDATE_REQUIRED', $this->vfsubminversion) . '</strong>';
				if (!empty($this->status->component)) {
				    $this->status->component['msg'] .= $msg;
                }
				else {
					$this->status->component = array('name' => 'visForms', 'type' => $route, 'msg' => $msg);
				}
			}
		}
		catch (Exception $e) {
			return false;
		}
		return false;
	}

	private function setLastCompatibleVersion($version) {
		$this->addLogEntry('Try to set lowest compatible version sequence.', Log::INFO);
	    $db = Factory::getDbo();
	    $lowestCompatVersion = $this->getLastCompatibleVersion();
	    // Fix Table #__visforms_lowest_compat_version has no recordset yet (as a result of an incomplete installation due to an inclomplete script)
	    if ($lowestCompatVersion === faLse || is_null($lowestCompatVersion)) {
	        try {
                $db->setQuery('INSERT INTO ' . $db->quoteName('#__visforms_lowest_compat_version')
                    . ' (' . $db->quoteName('vfversion') . ') VALUES (' . $db->q($version) . ')')->execute();
            }
            catch (Exception $e) {
                $this->addLogEntry("Unable to create record set in table lowest compatible version sequence: " . $e->getMessage(), Log::ERROR);
            }
        }
	    // update existing record
	    else {
            try {
                $db->setQuery('UPDATE ' . $db->quoteName('#__visforms_lowest_compat_version')
                    . ' SET ' . $db->quoteName('vfversion') . ' = ' . $db->q($version))->execute();
            } catch (Exception $e) {
                $this->addLogEntry("Unable to set lowest compatible version sequence from db: " . $e->getMessage(), Log::ERROR);
            }
        }
    }

	private function getLastCompatibleVersion() {
		$this->addLogEntry('Try to get last compatible version sequence', Log::INFO);
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('vfversion'))
                ->from($db->qn('#__visforms_lowest_compat_version'));
		try {
			$db->setQuery($query);
		    return $db->loadResult();
        }
		catch (Exception $e) {
			$this->addLogEntry("Unable to get last compatible version sequence from db: " . $e->getMessage(), Log::INFO);
            return false;
		}
	}

    private function removeJ3UpdateSiteLinks() {
        // remove all outdated Joomla! 3 update site links Visforms and Subscription
        $this->addLogEntry("Try to remove Joomla 3 update site links", Log::INFO);
        $linksToRemove = "'http://vi-solutions.de/updates/visforms_3_8/extension.xml', 'https://vi-solutions.de/updates/visforms_3_8/extension.xml', 'http://vi-solutions.de/updates/visforms/extension.xml', 'https://vi-solutions.de/updates/visforms/extension.xml', 'http://vi-solutions.de/updates/vfsubscription/j3/extension.xml', 'https://vi-solutions.de/updates/vfsubscription/j3/extension.xml'";
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('update_site_id'))
            ->from($db->qn('#__update_sites'))
            ->where($db->qn('location') . ' in (' . $linksToRemove . ')');
        try {
            $db->setQuery($query);
            $updateSiteIds = $db->loadColumn();
        }
        catch (RuntimeException $e) {
            $this->addLogEntry("Unable to get update site ids: " . $e->getMessage(), Log::ERROR);
        }
        if (!empty($updateSiteIds)) {
            $this->removeUpdateSiteLinks($updateSiteIds);
        }
    }

    private function removeUpdateSiteLinks($update_site_ids) {
        $db = Factory::getDbo();
        if (!empty($update_site_ids)) {
            $update_sites_ids_a = implode(',', $update_site_ids);
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__update_sites'));
            $query->where($db->quoteName('update_site_id') . ' IN (' . $update_sites_ids_a . ')');
            try {
                $db->setQuery($query);
                $db->execute();
            }
            catch (RuntimeException $e) {
                $this->addLogEntry("Problems deleting record sets in #__update_sites : " . $e->getMessage(), \JLog::INFO);
            }
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__update_sites_extensions'));
            $query->where($db->quoteName('update_site_id') . ' IN (' . $update_sites_ids_a . ')');
            try {
                $db->setQuery($query);
                $db->execute();
            }
            catch (RuntimeException $e) {
                $this->addLogEntry("Problems deleting record sets in #__update_sites_extensions : " . $e->getMessage(), \JLog::INFO);
            }
        }
    }

    private function uninstallSearchPlugin() {
        $db = Factory::getDbo();
	    $name = 'visformsdata';
        $group = 'search';
        $plgWhere = $db->quoteName('type') . ' = ' . $db->quote('plugin') . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name) . ' AND ' . $db->quoteName('folder') . ' = ' . $db->quote($group);
        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions'))
            ->where($plgWhere);
        try {
            $db->setQuery($query);
            $extensions = $db->loadColumn();
        }
        catch (RuntimeException $e) {
            $this->addLogEntry('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), Log::ERROR);
        }
        if (count($extensions)) {
            foreach ($extensions as $id) {
                $this->uninstallVfPlugin($id, $name, $group);
            }
        }
    }

    private function uninstallVfPlugin($id, $name, $group) {
        $installer = new Installer;
        try {
            $result = $installer->uninstall('plugin', $id);
            if ($result) {
                $this->addLogEntry('Plugin sucessfully removed: ' . $name, Log::INFO);
            }
            else {
                $this->addLogEntry('Removal of plugin failed: ' . $name, Log::ERROR);
            }
        }
        catch (RuntimeException $e) {
            $this->addLogEntry('Removal of plugin failed: ' . $name . ', ' . $e->getMessage(), Log::ERROR);
        }
    }

    private function convertCheckoutFieldsInDataTableToAllowNull () {
        $dataTables = $this->getPrefixFreeDataTableList();
        if (!empty($dataTables)) {
            $this->addLogEntry("*** Try to convert data tables fields checked_out and checked_out_time DDL to allow NULL ***", Log::INFO);
            foreach ($dataTables as $tn) {
                if ($this->runQuery('ALTER TABLE ' . $tn . ' modify checked_out int unsigned null')) {
                    $this->addLogEntry("DDL of field checked_out in table ' . $tn . ' successfully changed", Log::INFO);
                }
                if ($this->runQuery('ALTER TABLE ' . $tn . ' modify checked_out_time datetime null')) {
                    $this->addLogEntry("DDL of field checked_out_time in table ' . $tn . ' successfully changed", Log::INFO);
                }
            }
        }
    }

	// logging

	private function initializeLogger($adapter) {
		$this->loggerName = (string) $adapter->getManifest()->loggerName;
		$options['format']              = "{CODE}\t{MESSAGE}";
		$options['text_entry_format']   = "{PRIORITY}\t{MESSAGE}";
		$options['text_file']           = 'visforms_update.php';
		try {
			Log::addLogger($options, Log::ALL, array($this->loggerName, 'jerror'));
		}
		catch (RuntimeException $e) {}
	}

	private function addLogEntry($message, $code = Log::ERROR) {
		try {
			Log::add($message, $code, $this->loggerName);
		}
		catch (RuntimeException $exception) {}
	}
}