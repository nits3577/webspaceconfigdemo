<?php
/**
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$requiredText = (!empty($displayData) && !empty($displayData['requiredText'])) ? $displayData['requiredText'] : Text::_('COM_VISFORMS_REQUIRED');
echo '<div class="form-group">';
echo '<div class="row">';
echo '<div class="col-12">';
echo '<label class="vis_mandatory">' . $requiredText . ' *</label>';
echo '</div>';
echo '</div>';
echo '</div>';
