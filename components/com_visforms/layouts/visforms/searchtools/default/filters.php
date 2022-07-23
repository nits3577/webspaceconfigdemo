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

/** @var array $displayData */
$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$context = (!empty($data['options']['context'])) ? $data['options']['context'] : '';
$hasSearchFilter = false;
?>
<?php if ($filters) : ?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ((empty($context) || (strpos($fieldName, $context) > 0)) && ($fieldName != 'filter_'.$context.'search') && ($fieldName != 'filter_'.$context.'vfsortordering')) :
            $hasSearchFilter = true; ?>
			<div class="js-stools-field-filter"><?php
                echo $field->label;
                echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
    <?php if ($hasSearchFilter) { ?>
        <div class="js-stools-field-filter">
            <button type="button" class="btn btn-secondary visToolTip" title="Suchen" onclick="this.form.submit()" data-bs-toogle="tooltip">
                <span class="visicon-search"></span>
            </button>
        </div>
	<?php }?>
<?php endif; ?>
