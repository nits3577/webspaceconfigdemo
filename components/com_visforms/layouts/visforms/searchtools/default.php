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

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/** @var array $displayData */
$data = $displayData;
HTMLHelper::_('visforms.visformsTooltip');

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$context = (!empty($data['options']['context'])) ? $data['options']['context'] : '';
$filters = $data['view']->filterForm->getGroup('filter');
if (VisformsAEF::checkAEF(VisformsAEF::$subscription)) {
    // Check if the no results message should appear.
    if (isset($data['view']->total) && (int) $data['view']->total === 0) {
        $noResultsText = Text::_("COM_VISFORMS_NO_SUBMISSIONS_AVAILABLE");
    }
	// Set some basic options
	$customOptions = array(
		'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
		'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : Factory::getApplication()->get('list_limit', 20),
		'searchFieldSelector' => '#filter_'.$context.'search',
		'orderFieldSelector'  => '#list_fullordering',
        'clearBtnSelector' => (!empty($context)) ? '.'.$context : '.js-stools-btn-clear',
        'showNoResults'       => !empty($noResultsText),
        'noResultsText'       => !empty($noResultsText) ? $noResultsText : '',
	);

	if (!empty($filters) && is_array($filters)) {
		$filtercount = count($filters);
		if ((array_key_exists('filter_'.$context.'search', $filters)) && (!empty($filtercount))) {
			$filtercount--;
		}
		if ((array_key_exists('filter_'.$context.'vfsortordering', $filters)) && (!empty($filtercount))) {
			$filtercount--;
		}
	}
	$customOptions['filterButton'] = (!empty($filtercount)) ? true : false;
	
	$data['options'] = array_merge($customOptions, $data['options']);

	$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#'.$context.'adminForm';
	if (!empty($data['options']['hasLocationRadiusSearch'])) {
		HTMLHelper::_('visformslocation.includeLocationSearchJs');
    }

	// Load search tools
	HTMLHelper::_('visformssearchtools.form', $formSelector, $data['options']);

?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<?php echo LayoutHelper::render('visforms.searchtools.default.bar', $data, JPATH_ROOT. '/components/com_visforms/layouts'); ?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php echo LayoutHelper::render('visforms.searchtools.default.list', $data, JPATH_ROOT. '/components/com_visforms/layouts'); ?>
		</div>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container-filters clearfix">
		<?php echo LayoutHelper::render('visforms.searchtools.default.filters', $data, JPATH_ROOT. '/components/com_visforms/layouts'); ?>
	</div>
</div>
    <?php if ($data['options']['showNoResults']) : ?>
        <?php echo $this->sublayout('noitems', $data); ?>
    <?php endif; ?>
<?php } ?>
