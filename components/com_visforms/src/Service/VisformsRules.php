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

namespace Visolutions\Component\Visforms\Site\Service;

// no direct access
defined('_JEXEC') or die( 'Restricted access' );

use Joomla\CMS\Component\Router\Rules\RulesInterface;

class VisformsRules implements RulesInterface
{

	public function __construct($router) {
		$this->router = $router;
	}

	public function preprocess(&$query) {
	}

	public function build(&$query, &$segments) {
		// StandardRules have already removed any layout which is part of the menu link (i.e. visformsdata&layout=data).
		// We only have to replace other layouts (i.e. visformsdata&layout=detail)
        // if we deal with a data detail view reached from the data list view, there is a additional parameter cid which we put into $segments (on the last position)
		if (isset($query['layout']) && isset($query['cid'])) {
			// $isDetail = true;
			$segments[] = $query['layout'];
			unset($query['layout']);
            $segments[] = $query['cid'];
            unset($query['cid']);
		}
		else if (isset($query['layout'])) {
            $segments[] = $query['layout'];
            unset($query['layout']);
        }
        // if we deal with a data detail with its own menu item, we do not need the cid
		else {
            unset($query['cid']);
        }

		return;
	}

	public function parse(&$segments, &$vars) {
		$count = count($segments);
		// if there is only one segment, then it is the layout
		if ($count >= 1) {
			$vars['layout'] = $segments[0];
			unset($segments[0]);
		}
		if ($count >= 2) {
			$vars['cid'] = $segments[1];
			unset($segments[1]);
			// a visformsdata detail view directly called. Used in finder plugin
			// only more than 2 segements, if nomenurules are enabled
            // we use nomenurules in order to replace view parameter with a sef string
			if ($count >= 3) {
			    // segments[2] is the view which is parsed by the nomenurules
                // nomenurules expect segments[0] to be the view name
			    $segments[0] = $segments[2];
				unset($segments[2]);
			}
			// form name part in sef url
			if ($count >= 4) {
				$part = preg_replace('/-/', ':', $segments[3], 1);
				if (is_string($part)) {
					$parts = explode(':', $part, 2);
					if (count($parts) === 2) {
						$vars['id'] = $parts[0];
					}
				}
				unset($segments[3]);
			}
		}
		return;
	}
}
