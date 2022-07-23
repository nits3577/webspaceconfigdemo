<?php
/**
 * Visforms bootstrap default view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

if ($visforms->published != '1') {
	return;
}
HTMLHelper::_('visforms.includeScriptsOnlyOnce', array('visforms.bt5.min' => true)); ?>

<form action="<?php echo Uri::base(true) . '/' . htmlspecialchars($formLink, ENT_COMPAT, 'UTF-8'); ?>" method="post" name="visform"
      id="<?php echo $visforms->parentFormId; ?>"
      class="bt5 visform <?php echo $visforms->formCSSclass; ?>"<?php if ($upload == true) { ?> enctype="multipart/form-data"<?php } ?>><?php
	//add a progressbar
	if (((!empty($visforms->displaysummarypage)) || ($steps > 1)) && (!empty($visforms->displayprogress))) {
		echo LayoutHelper::render('visforms.progress.default', array('parentFormId' => $visforms->parentFormId, 'steps' => $steps, 'displaysmallbadges' => $visforms->displaysmallbadges, 'displaysummary' => $visforms->displaysummarypage), null, array('component' => 'com_visforms'));
	}
	for ($f = 1; $f < $steps + 1; $f++) {
		$active = ($f === 1) ? ' active' : '';
		echo '<fieldset class="fieldset-' . $f . $active . ' row">';
		if ($f === 1) {
			//Explantion for * if at least one field is requiered at the top of the form
			if ($required == true && $visforms->required == 'top') {
				echo LayoutHelper::render('visforms.requiredtext.bt5', array(), null, array('component' => 'com_visforms'));
			}

			//first hidden fields at the top of the form
			for ($i = 0; $i < $nbFields; $i++) {
				$field = $visforms->fields[$i];
				if ($field->typefield == "hidden") {
					echo $field->controlHtml;
				}
			}
		}
		//then inputs, textareas, selects and fieldseparators
		echo '<div class="row">';
		for ($i = 0; $i < $nbFields; $i++) {
			$field = $visforms->fields[$i];
			if ($field->typefield != "hidden" && empty($field->sig_in_footer) && !isset($field->isButton) && ($field->fieldsetcounter === $f)) {
				//set focus to first visible field
				if ((!empty($setFocus)) && ($firstControl == true) && ((!(isset($field->isDisabled))) || ($field->isDisabled == false))) {
					$script = '';
					$script .= 'jQuery(document).ready( function(){';
					$script .= 'jQuery("#' . $field->errorId . '").focus();';
					$script .= '});';
					$doc = Factory::getApplication()->getDocument();
					$doc->addScriptDeclaration($script);
					$firstControl = false;
				}
				echo $field->controlHtml;
			}
		}
		if ($f === $steps) {
			echo '</div>';
			//no summary page
			if (empty($visforms->displaysummarypage)) {
				echo LayoutHelper::render('visforms.footers.bt5.nosummary', array('form' => $visforms, 'nbFields' => $nbFields, 'hasRequired' => $required), null, array('component' => 'com_visforms'));
			} //with summary page
			else {
				echo LayoutHelper::render('visforms.footers.bt5.withsummary', array('form' => $visforms, 'nbFields' => $nbFields, 'hasRequired' => $required, 'summarypageid' => $visforms->parentFormId), null, array('component' => 'com_visforms'));
			}
		}
		echo '</fieldset>';
	} ?>
    <input type="hidden" name="return" value="<?php echo $return; ?>"/>
    <input type="hidden" value="<?php echo $visforms->id; ?>" name="postid"/>
    <input type="hidden" value="<?php echo $context; ?>" name="context"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>