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

use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\Component\Privacy\Administrator\Plugin\PrivacyPlugin;

/**
 * Privacy plugin managing Visforms data
 *
 * @since  3.9.0
 */
class PlgPrivacyVisforms extends PrivacyPlugin
{
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
	}

	public function onPrivacyExportRequest(PrivacyTableRequest $request, User $user = null) {
		if (!$user && !$request->email) {
			return array();
		}
		$domains = array();
		$domain = $this->createDomain('user_visforms', 'joomla_user_visforms_data');
		$domains[] = $domain;
		//ToDo Replace F1 with actual field label
		$tablelist = $this->getPrefixFreeDataTableList();
		if (empty($tablelist)) {
			return array();
		}
		foreach ($tablelist as $table) {
			$query = $this->db->getQuery(true)
				->select('*')
				->from($this->db->quoteName($table))
				->order($this->db->quoteName('created') . ' ASC')
				->where($this->db->quoteName('created_by') . ' = ' . (int) $user->id);
			try {
				$items = $this->db->setQuery($query)->loadObjectList();
			}
			catch (RuntimeException $e) {
				continue;
			}
			if (empty($items)) {
				continue;
			}
			foreach ($items as $item) {
				$domain->addItem($this->createItemFromArray((array) $item));
			}
			// no custom fields in Visforms
			// $domains[] = $this->createCustomFieldsDomain('com_visforms.data', $items);
		}
		return $domains;
	}

	private function getLowerCaseTableList() {
		$tablesAllowed = $this->db->getTableList();
		if (!empty($tablesAllowed)) {
			return array_map('strtolower', $tablesAllowed);
		}
		else {
			return false;
		}
	}

	private function getPrefixFreeDataTableList() {
		$prefixFreeTableList = array();
		$forms = $this->getForms();
		if (empty($forms)) {
			return $prefixFreeTableList;
		}
		$tableList = $this->getLowerCaseTableList();
		if (empty($tableList)) {
			return $prefixFreeTableList;
		}
		foreach ($forms as $form) {
			$tnfulls = array(strtolower($this->db->getPrefix() . "visforms_" . $form->id), strtolower($this->db->getPrefix() . "visforms_" . $form->id . "_save"));
			foreach ($tnfulls as $tnfull) {
				if (in_array($tnfull, $tableList)) {
					$prefixFreeTableList[] = str_replace(strtolower($this->db->getPrefix()), "#__", $tnfull);
				}
			}
		}
		return $prefixFreeTableList;
	}

	private function getForms() {
		$query = $this->db->getQuery(true);
		$query->select('*')
			->from($this->db->qn('#__visforms'));
		try {
			$this->db->setQuery($query);
			return $this->db->loadObjectList();
		}
		catch (RuntimeException $e) {
			return false;
		}
	}

	public function onPrivacyCollectAdminCapabilities() {
		$this->loadLanguage();
		return array(
			Text::_('COM_VISFORMS_PRIVACY') => array(
				Text::_('COM_VISFORMS_PRIVACY_INFORMATION'),
			),
			Text::_('PLG_VISFORMS_SPAMBOTCHECK') => array(
				Text::_('PLG_VISFORMS_SPAMBOTCHECK_PRIVACY_INFORMATION'),
			)
		);
	}
}
