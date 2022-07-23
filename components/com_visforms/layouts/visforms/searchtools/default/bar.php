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
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

/** @var array $displayData */
$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$context = (!empty($data['options']['context'])) ? $data['options']['context'] : '';
if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

// Options
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');
// ToDo add parameter "make small/use icons" to menu/plugin configuration; use parameter value in condition
$fitlerButtonText = (true) ? Text::_('JSEARCH_TOOLS') : '<span class="visicon-filter"></span>';
$clearButtonText = (true) ? Text::_('JSEARCH_FILTER_CLEAR') : '<span class="visicon-unpublish"></span>';
?>

<?php if (!empty($filters['filter_'.$context.'search'])) : ?>
	<?php if ($searchButton) : ?>
		<label for="filter_search" class="sr-only">
			<?php echo Text::_('JSEARCH_FILTER'); ?>
		</label>
		<div class="btn-group mr-2">
            <div class="btn-wrapper input-group">
                <?php echo $filters['filter_'.$context.'search']->label; ?>
                <?php echo $filters['filter_'.$context.'search']->input; ?>
                <span class="input-group-btn">
                    <?php if ($filters['filter_'.$context.'search']->description) : ?>
                        <?php HTMLHelper::_('bootstrap.tooltip', 'filter_'.$context.'search', array('title' => Text::_($filters['filter_'.$context.'search']->description))); ?>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-secondary visToolTip" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_SUBMIT'); ?>" data-bs-toogle="tooltip">
                        <span class="visicon-search"></span>
                    </button>
                </span>
            </div>
		</div>
		<?php if ($filterButton) : ?>
			<div class="btn-group">
				<button type="button" class="btn btn-secondary js-stools-btn-filter" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_TOOLS_DESC'); ?>">
					<?php echo $fitlerButtonText;?> <span class="fa fa-caret-down"></span>
				</button>
			</div>
		<?php endif; ?>
        <div class="btn-group">
            <div class="btn-wrapper">
                <button type="button" class="btn btn-secondary js-stools-btn-clear  <?php echo $context; ?>" title="<?php echo HTMLHelper::_('tooltipText', 'JSEARCH_FILTER_CLEAR'); ?>" >
                    <?php echo $clearButtonText;?>
                </button>
            </div>
        </div>
	<?php endif; ?>
<?php endif;
