<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2019 vi-solutions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class visFormsSortOrderHelper
{
	protected $unSortable = array('submit', 'reset', 'image', 'fieldsep', 'hidden', 'signature');
	protected $fid;
	protected $options = array();
	protected $formFrontendSettings = array();
	protected $fields = array();

	public function __construct($fid) {
		$this->dbo = Factory::getDbo();
		$this->fid = $fid;
		$this->setFields();
		$this->setFormFrontendSettings();
	}

	public function getOptions() {
		$this->setOptions();
		return $this->options;
	}

	protected function setOptions() {
		$fields = $this->fields;
		$formFrontendSettings = $this->formFrontendSettings;
		$options = array();
		$options[] = HTMLHelper::_(
			'select.option', '',
			Text::_('COM_VISFORMS_SELECT_SORT_FIELD'), 'value', 'text',
			false
		);
		$options[] = HTMLHelper::_(
			'select.option', 'id',
			Text::_('COM_VISFORMS_ID'), 'value', 'text',
			false
		);
		$options[] = HTMLHelper::_(
			'select.option', 'created',
			Text::_('COM_VISFORMS_SUBMISSIONDATE'), 'value', 'text',
			false
		);
		if ($fields) {
			foreach ($fields as $field) {
				$tmp = HTMLHelper::_(
					'select.option', $field->id,
					$field->label, 'value', 'text',
					false
				);

				// Add the option object to the result set.
				$options[] = $tmp;
			}
		}
		if (isset($formFrontendSettings['displayip']) && (($formFrontendSettings['displayip'] == "1") || ($formFrontendSettings['displayip'] == "2"))) {
			$options[] = HTMLHelper::_(
				'select.option', 'ipaddress',
				Text::_('COM_VISFORMS_IP'), 'value', 'text',
				false
			);
		}
		if (isset($formFrontendSettings['displayismfd']) && (($formFrontendSettings['displayismfd'] == "1") || ($formFrontendSettings['displayismfd'] == "2"))) {
			$options[] = HTMLHelper::_(
				'select.option', 'ismfd',
				Text::_('COM_VISFORMS_MODIFIED'), 'value', 'text',
				false
			);
		}
		if (isset($formFrontendSettings['displaymodifiedat']) && (($formFrontendSettings['displaymodifiedat'] == "1") || ($formFrontendSettings['displaymodifiedat'] == "2"))) {
			$options[] = HTMLHelper::_(
				'select.option', 'modified',
				Text::_('COM_VISFORMS_MODIFIED_AT'), 'value', 'text',
				false
			);
		}
		$this->options = $options;
	}

	protected function setFormFrontendSettings() {
		$db = $this->dbo;
		$query = $db->getQuery(true);
		$query->select($db->qn('frontendsettings'))
			->from($db->qn('#__visforms'))
			->where($db->qn('id') . ' = ' . $this->fid);
		try {
			$db->setQuery($query);
			$formFrontendSettings = $db->loadResult();
			if (!empty($formFrontendSettings)) {
				$this->formFrontendSettings = VisformsHelper::registryArrayFromString($formFrontendSettings);
			}
		}
		catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	protected function setFields() {
		$db = $this->dbo;
		$query = $db->getQuery(true);
		$unSortable = implode('","', $this->unSortable);
		// Create options according to visfield settings
		$query->select($db->qn(array('id', 'label')))
			->from($db->qn('#__visfields'))
			->where($db->qn('fid') . '=' . $this->fid)
			->where($db->qn('published') . '=' . 1)
			->where('('. $db->qn('frontdisplay') . 'is null or ' . $db->qn('frontdisplay') . '=' . 1 .' or ' . $db->qn('frontdisplay') . '=' . 2 .')')
			->where('not' . $db->qn('typefield') .  'in ("'.$unSortable.'")');
		try {
			$db->setQuery($query);
			$this->fields = $db->loadObjectList();
		}
		catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}
}