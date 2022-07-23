<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;

class VisformsAEF
{
    public static $subscription = 12;

    public static function checkAEF($feature) {
        switch ($feature) {
            case self::$subscription :
	            // return false;
	            return (self::featureExists(JPATH_ROOT . '/administrator/manifests/packages/pkg_vfsubscription.xml'));
            default:
                break;
        }
    }

    protected static function featureExists($file) {
        if (!(File::exists(Path::clean($file)))) {
            return false;
        }
        else {
            return true;
        }
    }

    public static function getAefList() {
        $list = array();
        $class = new ReflectionClass('VisformsAEF');
        $aefs = $class->getStaticProperties();
        if ((empty($aefs)) || (!is_array($aefs))) {
            return $list;
        }
        foreach ($aefs as $aef) {
            $list[$aef] = self::checkAEF($aef);
        }
        return $list;
    }

    public static function getVersion($feature) {
        switch ($feature) {
            case self::$subscription :
                return self::extractVersionFromXMLFile(JPATH_ROOT . '/administrator/manifests/packages/pkg_vfsubscription.xml');
            default:
                return false;
        }
    }

    protected static function extractVersionFromXMLFile($file)
    {
        if (!(File::exists(Path::clean($file)))) {
            return false;
        }
        else {
            // suppress warnings
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($file);
            if ($xml === false) {
                return false;
            }
            return $xml->version;
        }
    }
}
