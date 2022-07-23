<?php
/**
 * Mod_Visforms Form
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   mod_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
	
if ($visforms->published != '1') 
{
    return;
    
}

//retrieve helper variables from params
$editIds=$params->get('editIds');
$return = HTMLHelper::_('visforms.base64_url_encode', Uri::getInstance()->toString());
$context = $params->get('context');
?>

<div class="visforms-form">
<?php
if ($menu_params->get('show_title') == 1) 
	{ 
		 ?>
		<h1><?php echo $visforms->title; ?></h1>
	<?php
	}
?>

  <?php if (strcmp ( $visforms->description , "" ) != 0) { ?>
	<div class="category-desc">
	<?php 
		PluginHelper::importPlugin('content');
		echo HTMLHelper::_('content.prepare', $visforms->description);
	?></div>
  <?php } ?>

    <p>
        <?php
        echo Text::_('COM_VISFORMS_REDIRECT_TO_EDIT_VIEW_TEXT');
        ?>
    </p>

    <?php
    foreach ($editIds as $recordId)
    {
        $redirectUri = '&return=' . $return;
        $link = Uri::base() . 'index.php?option=com_visforms&view=edit&layout=edit&task=edit.editdata&id=' . (int)$visforms->id . '&cid=' . (int)$recordId . $redirectUri . '&' . Session::getFormToken() . '=1&Itemid=' . $visforms->dataEditMenuExists;
        ?>
        <p></p><a href="<?php echo htmlspecialchars($link, ENT_COMPAT, 'UTF-8'); ?>"><?php echo Text::_('COM_VISFORMS_EDIT_LINK_TEXT'); ?><?php echo $recordId; ?></a></p>
        <?php
    }
    ?>
  
  
 <?php  if ($visforms->poweredby == '1') { ?>
	<?php echo HTMLHelper::_('visforms.creditsFrontend'); ?>
<?php } ?>

</div>
