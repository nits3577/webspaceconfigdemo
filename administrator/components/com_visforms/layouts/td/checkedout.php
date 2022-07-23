<?php
/**
 * Visforms
 *
 * @author       Ingmar Vack
 * @package      Joomla.Administrator
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2017 vi-solutions
 * @since        Joomla 3.0.0
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<td class="has-context">
    <div class="pull-left"><?php
        if ($displayData['item']->checked_out) {
            echo HTMLHelper::_('jgrid.checkedout',
                $displayData['data']->i,
                $displayData['view']->user->name,
                $displayData['item']->checked_out_time,
                $displayData['view']->viewName. '.',
                $displayData['data']->canCheckin);
        }
        if ($displayData['data']->canEdit || $displayData['data']->canEditOwn) { ?>
            <a href="<?php echo $displayData['data']->linkEdit; ?>" title="<?php echo Text::_('JACTION_EDIT'); ?>"><?php echo $displayData['view']->escape($displayData['item']->title); ?></a>
            <p class="small"><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $displayData['view']->escape($displayData['item']->name)); ?></p><?php
        }
        else {
            echo $displayData['view']->escape($displayData['item']->title); ?>
            <p class="small"><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $displayData['view']->escape($displayData['item']->name)); ?></p><?php
        } ?>
    </div>
</td>