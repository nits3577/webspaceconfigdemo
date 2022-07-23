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

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
/** @var array $displayData */
$data = $displayData;

$metatitle = Text::_('JGLOBAL_CLICK_TO_SORT_THIS_COLUMN');
?>
<a href="#" onclick="return false;" class="js-stools-column-order visToolTip" data-order="<?php echo $data->order; ?>" data-direction="<?php echo strtoupper($data->direction); ?>" data-name="<?php echo Text::_($data->title); ?>" title="<?php echo $metatitle; ?> data-bs-toggle="tooltip">
	<?php if (!empty($data->icon)) : ?>
		<span class="<?php echo $data->icon; ?>"></span>
	<?php endif; ?>
	<?php if (!empty($data->title)) : ?>
		<?php echo Text::_($data->title); ?>
	<?php endif; ?>
	<?php if ($data->order == $data->selected) : ?>
		<span class="<?php echo $data->orderIcon; ?>"></span>
	<?php endif; ?>
</a>
