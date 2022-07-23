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

// Load the form list fields
$list = $data['view']->filterForm->getGroup('filter');
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$context = (!empty($data['options']['context'])) ? $data['options']['context'] : '';
?>
<?php if ($list) : ?>
    <div class="ordering-select hidden-phone">
		<?php foreach ($list as $fieldName => $field) :
			if ($fieldName == 'filter_'.$context.'vfsortordering') : ?>
                <div class="js-stools-field-list"><?php
                    echo $field->label;
                    echo $field->input; ?>
                </div><?php
            endif;
        endforeach; ?>
    </div>
<?php endif; ?>
