<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2018 vi-solutions
 * @since        Joomla 3.0.0
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

$hasSub = VisformsAEF::checkAEF(VisformsAEF::$subscription);

?>
<div class="col-lg-12 col-xl-7 alert viscreatortext"><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_PARAMETER_LINE_ONE'); ?><br/>
	<?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_PARAMETER_LINE_TWO'); ?><br/><br/>
	<em><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FORM_PARAMETER'); ?> <strong><?php echo Text::_('COM_VISFORMS_SAVE_RESULT'); ?></strong></em> <?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_SAVE_RESULT_EXPLANATION'); ?><br/>
	<em><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FORM_PARAMETER'); ?> <strong><?php echo Text::_('COM_VISFORMS_CREATOR_ALLOW_FRONTEND_DATA_VIEW_LABEL'); ?></strong></em> <?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FRONTEND_DATA_VIEW_EXPLANATION'); ?><br/><?php
	if ($hasSub) { ?>
	<em><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FORM_PARAMETER'); ?> <strong><?php echo Text::_('COM_VISFORMS_FRONTEND_DATA_VIEW_OWN_RECORDS_ONLY_LABEL'); ?></strong></em> <?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_OWN_RECORDS_ONLY_EXPLANATION'); ?><br/><?php
    } ?><br/>
	<em><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FIELD_PARAMETER'); ?> <strong><?php echo Text::_('COM_VISFORMS_FRONTEND_DISPLAY'); ?> (<?php echo Text::_('COM_VISFORMS_FRONTEND_DISPLAY_SIGN'); ?> )</strong></em> <?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FRONTEND_DISPLAY_EXPLANATION'); ?><br/>
    <em><?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_FIELD_PARAMETER'); ?> <strong><?php echo Text::_('COM_VISFORMS_REQUIRED'); ?> (<?php echo Text::_('COM_VISFORMS_REQUIRED_SIGN'); ?> )</strong></em> <?php echo Text::_('COM_VISFORMS_CREATOR_WARNING_REQUIRED_EXPLANATION'); ?><br/>
</div>