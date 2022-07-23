<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

class VisformsPlaceholderEntryFile extends VisformsPlaceholderEntry {

	protected static $customParams = array (
		'ORGNAME' => 'COM_VISFORMS_PLACEHOLDER_PARAM_ORGNAME',
		'PATH' => 'COM_VISFORMS_PLACEHOLDER_PARAM_LOCAL_PATH',
		'FULLPATH' => 'COM_VISFORMS_PLACEHOLDER_PARAM_FULL_PATH',
		'LINK'  => 'COM_VISFORMS_PLACEHOLDER_PARAM_LINK'
	);

	protected static $customSubscriptionParams = array (
		'ASIMAGE' => 'COM_VISFORMS_PLACEHOLDER_IMAGE_AS_IMAGE'
	);

	public function getReplaceValue() {
		if (empty($this->rawData)) {
			return '';
		}
		$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);
		if ($hasSub) {
			$customParams = array_merge(self::$customParams, self::$customSubscriptionParams);
		}
		else {
			$customParams = self::$customParams;
		}
		if (!empty($this->param) && array_key_exists($this->param, $customParams)) {
			$isImage = VisformsmediaHelper::isImage(HTMLHelper::_('visforms.getUploadFileName', $this->rawData));

			switch ($this->param) {
				case 'ORGNAME' :
					return HTMLHelper::_('visforms.getFileOrgName', $this->rawData);
				case 'PATH' :
					return HTMLHelper::_('visforms.getUploadFilePath', $this->rawData);
				case 'FULLPATH' :
					return HTMLHelper::_('visforms.getUploadFileFullPath', $this->rawData);
				case 'LINK' :
					return HTMLHelper::_('visforms.getUploadFileLink', $this->rawData);
				case 'ASIMAGE' :
					if ($isImage && $hasSub) {
						return '<img src="'.JUri::root(true) . '/' . HTMLHelper::_('visforms.getUploadFilePath', $this->rawData) . '" />';
					}
				default:
					return HTMLHelper::_('visforms.getUploadFileName', $this->rawData);
			}
		}
		// default return is file name
		return HTMLHelper::_('visforms.getUploadFileName', $this->rawData);
	}
}