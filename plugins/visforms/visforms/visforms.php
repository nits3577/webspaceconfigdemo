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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;
use Visolutions\Component\Visforms\Administrator\Model\VisformModel;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/helpers/visforms.php';

class plgVisformsVisforms extends CMSPlugin
{
    protected $autoloadLanguage = true;

    public function onPreprocessMenuItems($context, $children, $param, $enabled) {
        // Visforms Submenu in Administration
        // set link to '' if a menu item must be removed dynamically or set menu parameter 'menu_show' to 0
        $app = Factory::getApplication();
        if ($app->isClient('site')) {
            return true;
        }
        if ($context != 'com_menus.administrator.module') {
            return true;
        }

        $input = $app->input;
        // should actually always be the case, because plugin is only loaded if we are  in Visforms context
        $isVisformsMenu = ($input->get('option', '', 'cmd') == 'com_visforms');
        $fid = $input->get('fid', 0, 'int');
        if (0 < $fid && $isVisformsMenu) {
            $submenues = MenusHelper::loadPreset('visforms');
            foreach ($children as $i => $child) {
                if ($child->title == 'com_visforms') {
                    $hasSub = VisformsAef::checkAEF(VisformsAef::$subscription);
                    foreach ($submenues->getChildren() as $c) {
                        if (empty($hasSub) && $c->title == 'COM_VISFORMS_PDF_TEMPLATES') {
                            continue;
                        }
                        if ($c->title == 'COM_VISFORMS_VISFORMSDATA_VIEW_DEFAULT_TITLE') {
                            $formModel = new VisformModel();
                            $form = $formModel->getItem($fid);
                            if (empty($form->saveresult)) {
                                continue;
                            }
                        }
                        $c->link .= $fid;
                        $children[$i]->addChild($c);
                    }
                }
            }
        }
    }

    // Re-indixing of Finder Index
    public function onVisformsAfterFormSave($context, $form, $fields) {
        if ($context === 'com_visforms.form') {
            $row = new stdClass();
            $row->id = $form->id . ':' . $form->dataRecordId;
            PluginHelper::importPlugin('finder');
            Factory::getApplication()->triggerEvent('onFinderAfterSave', array($context, $row, true));
        }
        return true;
    }

    public function onVisformsAfterEditFormSave($context, $form, $fields) {
        if ($context === 'com_visforms.form') {
            $row = new stdClass();
            $row->id = $form->id . ':' . $form->dataRecordId;
            PluginHelper::importPlugin('finder');
            Factory::getApplication()->triggerEvent('onFinderAfterSave', array($context, $row, false));
        }
        return true;
    }

    public function onVisformsdataAfterJFormSave($context, $table, $isNew, $data = array()) {
        $app = Factory::getApplication();
        // event is triggered from administration and from site
        // we only want in from administration, because the site is handled by onVisformsAfterEditFormSave
        if ($app->isClient('site')) {
            return true;
        }
        if ($context === 'com_visforms.visdata') {
            $fid = $app->input->get('fid', 0, 'int');
            $row = new stdClass();
            $row->id = $fid . ':' . $table->id;
            PluginHelper::importPlugin('finder');
            $app->triggerEvent('onFinderAfterSave', array($context, $row, $isNew));
        }
        return true;
    }

    public function onVisformsAfterJFormSave($context, $table, $isNew, $data = array()) {
        if (($context === 'com_visforms.visfield') || ($context === 'com_visforms.visform')) {
            PluginHelper::importPlugin('finder');
            Factory::getApplication()->triggerEvent('onFinderAfterSave', array($context, $table, $isNew));
        }
        return true;
    }

    public function onVisformsdataJFormChangeState($context, $pks, $value) {
        if ($context === 'com_visforms.visdata') {
            $app = Factory::getApplication();
            $fid = $app->input->get('fid', 0, 'int');
            foreach ($pks as $i => $pk) {
                $pks[$i] = $fid . ':' . $pk;
            }
            PluginHelper::importPlugin('finder');
            $app->triggerEvent('onFinderChangeState', array($context, $pks, $value));
        }
        return true;
    }

    public function onVisformsJFormChangeState($context, $pks, $value) {
        if ($context === 'com_visforms.visform') {
            // $pks is the selected forms ids
            PluginHelper::importPlugin('finder');
            Factory::getApplication()->triggerEvent('onFinderChangeState', array($context, $pks, $value));
        }
        else if ($context === 'com_visforms.visfield') {
            // $pks is the selected fields ids, we need to reindex on the basis of the form
            $app = Factory::getApplication();
            $fid = $app->input->get('fid', 0, 'int');
            $pks = array($fid);
            PluginHelper::importPlugin('finder');
            $app->triggerEvent('onFinderChangeState', array($context, $pks, $value));
        }
        return true;
    }

    public function onVisformsdataAfterJFormDelete($context, $table) {
        if ($context === 'com_visforms.visdata') {
            $app = Factory::getApplication();
            PluginHelper::importPlugin('finder');
            $fid = $app->input->get('fid', 0, 'int');
            // we need another value in table key field for finder plugin
            $tmp = $fid . ':' . $table->id;
            // clone table object for use in finder plugin
            $finderPreparedTable = clone $table;
            $finderPreparedTable->id = $tmp;
            return $app->triggerEvent('onFinderAfterDelete', array($context, $finderPreparedTable));
        }
        return true;
    }

    public function onVisformsAfterJFormDelete($context, $table) {
        if (($context === 'com_visforms.visform') || ($context === 'com_visforms.visfield')) {
            PluginHelper::importPlugin('finder');
            return Factory::getApplication()->triggerEvent('onFinderAfterDelete', array($context, $table));
        }
        return true;
    }

    // End re-indixing finder index

    public function onVisformsBeforeJFormDelete($context, $data) {

        // Skip plugin if we are deleting something other than a visforms form or field
        if (($context != 'com_visforms.visfield') && ($context != 'com_visforms.visform')) {
            return true;
        }

        if ($context == 'com_visforms.visfield') {
            $success = true;
            $fid = $data->fid;
            $id = $data->id;

            // Convert the defaultvalues field to an array.
            $defaultvalues = VisformsHelper::registryArrayFromString($data->defaultvalue);

            //Remove restrtictions
            //getRestricts
            if ($restricts = VisformsConditionsHelper::setRestricts($data->id, $defaultvalues, $data->name, $fid)) {
                //remove Restrictions
                try {
                    VisformsConditionsHelper::removeRestriction($restricts);
                }
                catch (RuntimeException $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage, 'error');
                    return false;
                }
            }

            $db = Factory::getDbo();
            $tablesAllowed = $db->getTableList();
            if (!empty($tablesAllowed)) {
                $tablesAllowed = array_map('strtolower', $tablesAllowed);
            }
            $tablesToDeleteFrom = array("visforms_" . $fid, "visforms_" . $fid . "_save");
            foreach ($tablesToDeleteFrom as $tn) {
                $tnfull = strtolower($db->getPrefix() . $tn);

                //Delete field in data table when deleting a field
                if (in_array($tnfull, $tablesAllowed)) {

                    $tableFields = $db->getTableColumns('#__' . $tn, false);
                    $fieldname = "F" . $id;

                    if (isset($tableFields[$fieldname])) {

                        $query = "ALTER TABLE #__" . $tn . " DROP " . $fieldname;
                        try {
                            $db->setQuery($query);
                            $db->execute();
                        }
                        catch (RuntimeException $e) {
                            Factory::getApplication()->enqueueMessage($e->getMessage, 'error');
                            $success = false;
                            continue;
                        }
                    }
                }
            }

            if (!$success) {
                //set already deleted restrictions again
                VisformsConditionsHelper::setRestriction($restricts);

            }
            return $success;
        }

        //Delete fields in visfields table when deleting a form, delete datatable if table exists and delete pdfs in vispdf table
        if ($context == 'com_visforms.visform') {
            $success = true;
            $fid = $data->id;
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__visfields'))
                ->where($db->quoteName('fid') . " = " . $fid);
            try {
                $db->setQuery($query);
                $db->execute();
            }
            catch (RuntimeException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage, 'error');
                $success = false;
            }
            $query = $db->getQuery(true);
            $query->delete($db->quoteName('#__vispdf'))
                ->where($db->quoteName('fid') . " = " . $fid);
            try {
                $db->setQuery($query);
                $db->execute();
            }
            catch (RuntimeException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage, 'error');
                $success = false;
            }
            $tablesToDelete = array("visforms_" . $fid, "visforms_" . $fid . "_save");
            foreach ($tablesToDelete as $tn) {
                try {
                    $db->setQuery("drop table if exists #__" . $tn);
                    $db->execute();
                }
                catch (RuntimeException $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage, 'error');
                    $success = false;
                }
            }
        }
        return $success;
    }

    public function onVisformsAfterFormSaveError($context, $form, $fields) {
        $this->removeUploadedFiles($context, $form, $fields);
    }

    public function onVisformsAfterEditFormSaveError($context, $form, $fields) {
        $this->removeUploadedFiles($context, $form, $fields);
    }

    protected function removeUploadedFiles($context, $form, $fields) {
        if ($context != 'com_visforms.form') {
            return true;
        }
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            return true;
        }
        foreach ($fields as $field) {
            if ($field->typefield !== "file") {
                continue;
            }
            if (empty ($field->file) || !is_array($field->file)) {
                continue;
            }
            if (!empty($field->file['filepath'])) {
                $file = Path::clean($field->file['filepath']);
                if (File::exists($file)) {
                    File::delete($file);
                }
            }
        }
        return true;
    }
}
