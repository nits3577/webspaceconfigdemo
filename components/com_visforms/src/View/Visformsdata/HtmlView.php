<?php
/**
 * Visformsdata view for Visforms
 *
 * @author       Aicha Vack
 * @package      Joomla.Site
 * @subpackage   com_visforms
 * @link         https://www.vi-solutions.de
 * @license      GNU General Public License version 2 or later; see license.txt
 * @copyright    2012 vi-solutions
 * @since        Joomla 1.6 
 */

namespace Visolutions\Component\Visforms\Site\View\Visformsdata;
 
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class HtmlView extends BaseHtmlView
{
	protected $form;
	protected $items;
	protected $state;
	protected $menu_params;
	protected $fields;
	protected $itemid;
	protected $canDo;
	protected $counterOffest;
	protected $uniqueContext;


	public function display($tpl = null) {
		$this->state = $this->get('State');
		//it is important to get the 'menu' params from the state because the params could be set by the plugin as well
		$this->menu_params = $this->state->get('params', new Registry);
		$this->form = $this->get('Form');
		$app = Factory::getApplication();
		$isEditLayout = ($this->_layout == "detailedit" || $this->_layout == "dataeditlist") ? true : false;
		if (empty($this->form)) {
			$app->enqueueMessage(Text::_('COM_VISFORMS_DATAVIEW_FORM_MISSING'), 'error');
			return;
        }
        //visforms data views can be accessed without menu link. Allow visforms data views only if the form option is enabled
        //visforms data edit views can only be accessed if a menu item existes. The menu options control, which record sets can be viewed by a user
		if (empty($this->form->allowfedv) && empty($isEditLayout)) {
            $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
         //check if user access level allows view
        $user = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();
        $access = (isset($this->form->frontendaccess) && in_array($this->form->frontendaccess, $groups)) ? true : false;
		if ($access == false) {
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
        $this->canDo = \VisformsHelper::getActions($this->form->id);
        // get params from menu
        $title = '';
		if (isset($this->menu_params) && $this->menu_params->get('page_title')) {
            $title = $this->menu_params->get('page_title') ;
        }
		if ($app->get('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} 
		elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }
        $this->document->setTitle($title);
		
		if ($this->menu_params['menu-meta_description']) {
			$this->document->setDescription($this->menu_params['menu-meta_description']);
		}

		if ($this->menu_params['menu-meta_keywords']) {
			$this->document->setMetadata('keywords', $this->menu_params['menu-meta_keywords']);
		}	
		//Item id
        $this->itemid = $this->state->get('itemid', '0');		
		//form id
		$this->id = Factory::getApplication()->input->getInt('id', -1);

		// name of layout files for detail view must start with string detail
		if (strpos($this->_layout, 'detail') === 0) {
			// Get data from the model
			$this->item = $this->get('Detail');	
		} 
		
		// Get data from the model
		$this->items = $this->get('Items');		
        $this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->pagination = $this->get('Pagination');	
		$this->fields = $this->get('Datafields');
		// if we have multipe data views on one page (using content plugin), we need unique identifiers for each view
        // in order make the javascript based features like pagination, filter etc. work for each data view individually
        // the class member $context, provide by the Joomla core, might be overridden in plugin events
        // therefore we have abandonded using it here and store our context value in this->uniqueContext for later use
        $this->uniqueContext = $this->get('Context');
        $this->counterOffest = $this->get('Start');
        $this->total = $this->get('Total');
		HTMLHelper::_('visforms.includeScriptsOnlyOnce', array('visforms.min' => false, 'visdata.min' => true), array('validation' => false));
		parent::display($tpl);
		
	}
}
?>