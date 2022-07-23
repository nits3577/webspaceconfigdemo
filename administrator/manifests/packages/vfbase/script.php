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

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Date\Date;

class pkg_vfbaseInstallerScript {
	private $release;
	private $oldRelease;
	private $minimum_joomla_release= 4;
	private $maximum_joomla_release = 4;
	private $min_visforms_version;
	private $name;
	private $loggerName;
	private $languageFoldersBackend;
	private $languageFoldersFrontend;

	// construction

	public function __construct($adapter) {
		$this->initializeLogger($adapter);
		$this->addLogEntry('****** starting base package script ******', Log::INFO);
		$this->languageFoldersBackend = array(
			'/administrator/components/com_visforms/language'        => 'com_visforms',
			'/plugins/actionlog/visforms/language'                   => 'plg_actionlog_visforms',
			'/plugins/editors-xtd/visformfields/language'            => 'plg_editors-xtd_visformfields',
			'/plugins/privacy/visforms/language'                     => 'plg_privacy_visforms',
			'/plugins/visforms/spambotcheck/language'                => 'plg_visforms_spambotcheck',
			'/plugins/visforms/visforms/language'                    => 'plg_visforms_visforms',
		);
		$this->languageFoldersFrontend = array(
			'/components/com_visforms/language'                      => 'com_visforms',
			'/modules/mod_visforms/language'                         => 'mod_visforms',
		);
	}

	// interface

	public function preflight($route,  $adapter) {
		$this->release = $adapter->getManifest()->version;
		$this->minimum_joomla_release = $adapter->getManifest()->attributes()->version;
		$this->oldRelease = "";
		$this->min_visforms_version = $adapter->getManifest()->vfminversion;
		$this->name = $adapter->getManifest()->name;
		$jversion = new Version;
		$date = new Date('now');
		$app = Factory::getApplication();
		$this->addLogEntry('*** Start ' . $route . ' of extension ' . $this->name . ' ' . $this->release . ': ' . $date . ' ***', Log::INFO);

		// all version tests go here
		if ($route != 'uninstall') {
			// abort if the current Joomla release is too old or too new
			if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
			    $msg = Text::sprintf('PKG_VFBASE_WRONG_JOOMLA_VERSION', $this->name, $this->minimum_joomla_release);
				$app->enqueueMessage($msg, 'ERROR');
				$this->addLogEntry($msg, Log::ERROR);
				return false;
			}
            if ($jversion::MAJOR_VERSION > $this->maximum_joomla_release) {
			    $msg = Text::sprintf('PKG_VFBASE_WRONG_MAX_JOOMLA_VERSION', $this->name, $this->maximum_joomla_release);
				$app->enqueueMessage($msg, 'ERROR');
				$this->addLogEntry($msg, Log::ERROR);
				return false;
			}
			// tests for update route only
			if ($route == 'update') {
				// abort if this package version is lower than the installed version
				$this->oldRelease = $this->getParam('version', $this->name);
				if (version_compare($this->release, $this->oldRelease, 'lt')) {
					$msg = Text::sprintf('PKG_VFBASE_WRONG_VF_VERSION', $this->oldRelease, $this->release);
					$app->enqueueMessage($msg, 'ERROR');
					$this->addLogEntry($msg, Log::ERROR);
					return false;
				}
				// finally, log that we are on update route
				$this->addLogEntry('Try to update from version ' . $this->oldRelease . ' to ' . $this->release, Log::INFO);
			}
		}

		// test for downloading PHP fonts: is it necessary and possible
		if ($route != 'uninstall') {
			if ( !File::exists(Path::clean(JPATH_ROOT . '/media/com_visforms/tcpdf/fonts/helvetica.php'))) {
				// no fonts installed
				$this->addLogEntry('PDF fonts not installed', Log::INFO);
				// test for PHP directive
				$directive = 'allow_url_fopen';
				$value = ini_get($directive);
				$this->addLogEntry("PHP directive '$directive' set to: $value", Log::INFO);
				// case (false === $value) means: could not read PHP directive: we ignore this for now
				if ('0' === $value) {
					$text = Text::_('PKG_VFBASE_WRONG_PHP_SETTING_ALLOW_URL_FOPEN');
					$app  = Factory::getApplication();
					$app->enqueueMessage($text, 'warning');
					$this->addLogEntry('PDF fonts can not be installed due to PHP settings', Log::INFO);
					// we do not return false: simply show installation message to user and continue
				}
				else {
					$this->addLogEntry('PDF fonts can be installed due to PHP settings', Log::INFO);
				}
			}
		}

		// handle custom translation files
		if ($route != 'uninstall') {
			$this->copyCustomTranslationFiles();
		}

		return true;
	}

	public function postflight($route,  $adapter) {
		if ($route == 'update') {
			$this->deleteOldFiles();
		}
		// todo: clarify difference to: ($route !== 'uninstall')
		if ($route == 'install' || $route == 'update') {
            $manifest = $adapter->getParent()->manifest;
            $packages = $manifest->xpath('files/file');
			if (!empty($packages)) {
			    $this->deleteUpdateSites($packages);
            }
		}
		if ($route !== 'uninstall') {
			// delete possible visforms language folders
			$this->deleteTranslationFolders();
		}
		if ($route !== 'uninstall') {
		    $this->enableExtension('plg_editors-xtd_visformfields', 'plugin', 'visformfields', 'editors-xtd');
            $this->enableExtension('plg_visforms_visforms', 'plugin', 'visforms', 'visforms');
            $this->enableExtension('plg_visforms_spambotcheck', 'plugin', 'spambotcheck', 'visforms');
            echo '<h2>' . (($route == 'update') ? Text::_('PKG_VFBASE_PACKAGE_UPDATE_STATE') : Text::_('PKG_VFBASE_PACKAGE_INSTALLATION_STATUS')) . '</h2>';
            $this->addLogEntry($route . ' of ' . $this->name . ' successful', Log::INFO);
		}

		return true;
	}

	public function uninstall($adapter) {
		$manifestFile = JPATH_MANIFESTS . '/packages/pkg_vfbase.xml';
		if (!file_exists($manifestFile)) {
		    return;
        }
		$xml = simplexml_load_file($manifestFile);
		if (!$xml) {
		    return;
        }
		$release = $xml->version;
		if (empty($release)) {
		    return;
        }
		$language = Factory::getApplication()->getLanguage();
		$language->load('pkg_vfbase', JPATH_ROOT);
        echo '<h2>' .  Text::_('PKG_VFBASE_PACKAGE_REMOVAL_SUCESSFUL') . '</h2>';
	}

	// implementation

	// translation files and directories

	private function copyCustomTranslationFiles() {
		// get installed languages for backend and frontend
		$list       = LanguageHelper::getInstalledLanguages();
		$frontend   = array();
		$backend    = array();
		foreach ($list as $k => $v) {
			foreach ($v as $name => $language) {
				if(is_array($language)) {
					// however, during testing the structure of one additional language nl-NL was doubled
					// like this: $language changed from 'single stdClass' to 'array with two entries'
					// after temporarily manually copied the folder ('nl_NL' --> '__nl_NL') and reinstalled the language in the Joomla Extension Manager
					// Array (
					//[0] => stdClass Object (
					//	[element] => nl-NL
					//	[name] => Dutch (nl-NL)
					//	[client_id] => 1
					//	[extension_id] => 253 )
					//[1] => stdClass Object (
					//	[element] => nl-NL
					//	[name] => Dutch (nl-NL)
					//	[client_id] => 1
					//	[extension_id] => 407)
					//)
					// we just use the first array entry:
					$language = $language[0];
				}
				if('de-DE' == $language->element || 'en-GB' == $language->element) {
					// ignore DE German and GB English
					continue;
				}
				// client_Id = 1 means admin access client_Id = 0 means frontend access
				if($language->client_id) {
					array_push($backend, $language->element);
				}
				else {
					array_push($frontend, $language->element);
				}
			}
		}

		// copy possible language files
		$this->copyCustomTranslationFiles_helper($backend, $this->languageFoldersBackend, '/administrator/language');
		$this->copyCustomTranslationFiles_helper($frontend, $this->languageFoldersFrontend, '/language');
	}

	private function copyCustomTranslationFiles_helper($languages, $folders, $destPathRoot) {
		// copy custom language files of installed languages like:
		// administrator/components/com_visforms/language/de-DE/de-DE.com_visforms.ini
		// administrator/components/com_visforms/language/de-DE/de-DE.com_visforms.sys.ini
		// administrator/components/com_visforms/language/de-DE/com_visforms.ini
		// administrator/components/com_visforms/language/de-DE/com_visforms.sys.ini
		foreach ($languages as $language) {
			// for each backend installed language
			$destPath = JPATH_ROOT . "$destPathRoot/$language";
			// create destination language folder if missing (mkdir gives warning if folder exists)
			if ( !Folder::exists($destPath)) {
				$created = Folder::create($destPath, 0777);
			}
			foreach ($folders as $folder => $name) {
				// for each possible visforms extension language folder
				for($i = 1; $i <= 2; $i++) {
					for($j = 1; $j <= 2; $j++) {
						// twice: with and without '.sys' in file name
						$sys      = (1 == $i ? '' : '.sys');
						// twice: with and without language shortcut decorated in file name
						$fileDest   = "$name$sys.ini";
						$fileSource = (1 == $j ? "$language.$name$sys.ini" : $fileDest);
						$from       = JPATH_ROOT . "$folder/$language/$fileSource";
						$to         = "$destPath/$fileDest";
						$this->copyFile($from, $to);
					}
				}
			}
		}
	}

	private function deleteTranslationFolders() {
		$this->deleteFolders(array_keys($this->languageFoldersBackend));
		$this->deleteFolders(array_keys($this->languageFoldersFrontend));
	}

	private function copyFile($from, $to) {
		// copy if file exists
		if (File::exists($from)) {
			try {
				if(File::copy($from, $to)) {
					$this->addLogEntry("file $from copied to: $to", Log::INFO);
				}
				else {
					$this->addLogEntry("file $from not copied to: $to", Log::INFO);
				}
			}
			catch (RuntimeException $e) {
				$this->addLogEntry('unable to copy ' . $from . ': ' . $e->getMessage(), Log::INFO);
			}
		}
	}

	private function deleteFiles($files = array()) {
		foreach ($files as $file) {
			$oldFile = Path::clean(JPATH_ROOT . $file);
			if (File::exists($oldFile)) {
				try {
					File::delete($oldFile);
					$this->addLogEntry($oldFile . ' successfully deleted', Log::INFO);
				}
				catch (RuntimeException $e) {
					$this->addLogEntry('Deleting ' . $oldFile . ' failed: ' . $e->getMessage(), Log::INFO);
				}
			}
		}
	}

	private function deleteFolders($folders = array()) {
		foreach ($folders as $folder) {
			$oldFolder = Path::clean(JPATH_ROOT . $folder);
			if (Folder::exists($oldFolder)) {
				try {
					Folder::delete($oldFolder);
					$this->addLogEntry($oldFolder . ' successfully deleted', Log::INFO);
				}
				catch (RuntimeException $e) {
					$this->addLogEntry('Deleting ' . $oldFolder . ' failed: ' . $e->getMessage(), Log::INFO);
				}
			}
		}
	}

	private function deleteOldFiles() {
		// list all files and folder which have to be removed on update!
		$files = array(
			'/administrator/components/com_visforms/css/visforms_min.css',
			'/administrator/components/com_visforms/images/icon-16-visforms.png',
			'/administrator/components/com_visforms/js/jquery-ui.js',
			'/administrator/components/com_visforms/js/jquery-ui.min.js',
			'/administrator/components/com_visforms/layouts/td/terminating_line.php',
			'/components/com_visforms/captcha/images/audio_icon.gif',
			'/libraries/visolutions/tcpdf/encodings_maps.php',
			'/libraries/visolutions/tcpdf/htmlcolors.php',
			'/libraries/visolutions/tcpdf/pdf417.php',
			'/libraries/visolutions/tcpdf/spotcolors.php',
			'/libraries/visolutions/tcpdf/tcpdf_filters.php',
			'/libraries/visolutions/tcpdf/unicode_data.php',
			'/media/com_visforms/js/visforms.min.js',
			'/modules/mod_visforms/helper.php',
			'/language/de-DE/de-DE.pkg_vfbase.ini',
			'/language/de-DE/de-DE.pkg_vfbase.sys.ini',
			'/language/de-DE/de-DE.pkg_vfsubscription.ini',
			'/language/de-DE/de-DE.pkg_vfsubscription.sys.ini',
			'/language/de-DE/de-DE.files_vfsubsfiles.sys.ini',
		);
		$folders = array(
			'/administrator/components/com_visforms/helpers/html',
			'/administrator/components/com_visforms/controllers',
			'/administrator/components/com_visforms/Controller',
			'/administrator/components/com_visforms/Extension',
			'/administrator/components/com_visforms/Field',
			'/administrator/components/com_visforms/models',
			'/administrator/components/com_visforms/Model',
			'/administrator/components/com_visforms/Service',
			'/administrator/components/com_visforms/tables',
			'/administrator/components/com_visforms/Table',
			'/administrator/components/com_visforms/views',
			'/administrator/components/com_visforms/View',
			'/administrator/components/com_visforms/lib/placeholder',
			'/components/com_visforms/controllers',
			'/components/com_visforms/Controller',
			'/components/com_visforms/Field',
			'/components/com_visforms/helpers/route',
			'/components/com_visforms/models',
			'/components/com_visforms/Model',
			'/components/com_visforms/Service',
			'/components/com_visforms/views',
			'/components/com_visforms/View',
			'/modules/mod_visforms/helper',
			'/modules/mod_visforms/Helper',
		);
		$this->addLogEntry('*** Try to delete old files and folders ***', Log::INFO);
		$this->deleteFiles($files);
		$this->deleteFolders($folders);
	}

	protected function deleteFilesAndFolders($files, $folders) {
		foreach ($files as $file) {
			$oldFile = Path::clean(JPATH_ROOT . $file);
			if (File::exists($oldFile)) {
				try {
					File::delete($oldFile);
					$this->addLogEntry($oldFile . " deleted", Log::INFO);
				}
				catch (RuntimeException $e) {
					$this->addLogEntry('Unable to delete ' . $oldFile . ': ' . $e->getMessage(), Log::INFO);
				}
			} else {
				$this->addLogEntry($oldFile . " does not exist.", Log::INFO);
			}

		}
		foreach ($folders as $folder) {
			$oldFolder = Path::clean(JPATH_ROOT . $folder);
			if (Folder::exists($oldFolder)) {
				try {
					Folder::delete($oldFolder);
					$this->addLogEntry($oldFolder . "deleted", Log::INFO);
				} catch (RuntimeException $e) {
					$this->addLogEntry('Unable to delete ' . $oldFolder . ': ' . $e->getMessage(), Log::INFO);
				}
			} else {
				$this->addLogEntry($oldFolder . " does not exist.", Log::INFO);
			}

		}
	}

	// miscellaneous

	private function deleteUpdateSites($packages) {
        $db = Factory::getDbo();
        // remove upload site information for all extensions from database
        foreach ($packages as $package) {
            $type = (string) $package->attributes()->type;
            $name = (string) $package->attributes()->id;
            $group = (!empty($package->attributes()->group)) ? (string) $package->attributes()->group : '';
            $id = $this->getExtensionId($type, $name, $group, 0);
            if (!empty($id)) {
                $update_site_ids = $this->getUpdateSites($id);
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
                        $this->addLogEntry("Problems deleting record sets in #__update_sites : " . $e->getMessage(), Log::INFO);
                    }
                    $query = $db->getQuery(true);
                    $query->delete($db->quoteName('#__update_sites_extensions'));
                    $query->where($db->quoteName('extension_id') . ' = ' . $id);
                    try {
                        $db->setQuery($query);
                        $db->execute();
                    }
                    catch (RuntimeException $e) {
                        $this->addLogEntry("Problems deleting record sets in #__update_sites_extensions : " . $e->getMessage(), Log::INFO);
                    }
                }
            }
        }
    }

	private function getParam($pname, $name) {
		// get a variable from the manifest cache in database
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('manifest_cache'))
			->from($db->qn('#__extensions'))
			->where($db->qn('name') . ' = ' . $db->q($name));
		try {
			$db->setQuery($query);
			$manifest = json_decode($db->loadResult(), true);
			return $manifest[$pname];
		}
		catch (Exception $e) {
			$this->addLogEntry('Unable to get ' . $name . ' ' . $pname . ' from manifest cache in databese, ' . $e->getMessage(), Log::ERROR);
			return false;
		}
	}

	private function getExtensionId($type, $name, $group = '', $client_id = 0) {
		$db = Factory::getDbo();
		$where = $db->quoteName('type') . ' = ' . $db->quote($type) . ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($name);
		$query = $db->getQuery(true)
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($where);
		try {
			$db->setQuery($query);
			$id = $db->loadResult();
		}
		catch (RuntimeException $e) {
			$this->addLogEntry('Unable to get extension_id: ' . $name . ', ' . $e->getMessage(), Log::INFO);
			return false;
		}
		return $id;
	}

	private function getUpdateSites($extension) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('update_site_id'))
			->from($db->quoteName('#__update_sites_extensions'))
			->where($db->quoteName('extension_id') . ' = ' . $extension);
		try {
			$db->setQuery($query);
			$update_site_ids = $db->loadColumn();
		}
		catch (RuntimeException $e) {
			$this->addLogEntry('Unable to get update sites id: ' . $extension . ', ' . $e->getMessage(), Log::INFO);
			return false;
		}
		return $update_site_ids;
	}

	private function enableExtension($name, $type, $element, $folder = '') {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'))
			->set($db->quoteName('enabled') . " = 1")
			->where($db->quoteName('name') . ' = ' . $db->quote($name))
			->where($db->quoteName('type') . ' = ' . $db->quote($type))
			->where($db->quoteName('element') . ' = ' . $db->quote($element));
		if (!empty($folder)) {
			$query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
		}
		try {
			$db->setQuery($query);
			$db->execute();
			$this->addLogEntry("Extension successfully enabled", Log::INFO);
		}
		catch (RuntimeException $e) {
			$this->addLogEntry("Unable to enable extension " . $e->getMessage(), Log::ERROR);
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
		catch (RuntimeException $exception)
		{
			// prevent installation routine from failing due to problems with logger
		}
	}
}