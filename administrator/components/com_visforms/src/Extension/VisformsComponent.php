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

namespace Visolutions\Component\Visforms\Administrator\Extension;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visforms;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visformscalendar;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visformslocation;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visformssearchtools;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visformsselect;
use Visolutions\Component\Visforms\Administrator\Service\HTML\Visformssignature;

require_once JPATH_ADMINISTRATOR . '/components/com_visforms/include.php';

class VisformsComponent extends MVCComponent implements
	BootableExtensionInterface, RouterServiceInterface
{
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('visforms', new Visforms);
		$this->getRegistry()->register('visformscalendar', new Visformscalendar);
		$this->getRegistry()->register('visformslocation', new Visformslocation);
		$this->getRegistry()->register('visformssearchtools', new Visformssearchtools);
		$this->getRegistry()->register('visformssignature', new Visformssignature);
		$this->getRegistry()->register('visformsselect', new Visformsselect);
	}
}