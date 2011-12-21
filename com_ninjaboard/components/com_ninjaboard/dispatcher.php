<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: dispatcher.php 1807 2011-04-14 21:31:04Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Dispatcher, obviously
 *
 * @package Ninjaboard
 */
class ComNinjaboardDispatcher extends ComDefaultDispatcher
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options.
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		$view = $this->getController()->getView();

		if(!is_a($view, 'ComNinjaboardViewHtml')) return;

		//Add the "Forums" to the pathway if the current view or the last pathway item isn't "Forums"
		$pathway = KFactory::get('lib.koowa.application')->getPathWay()->getPathway();
		$last	 = end($pathway);
		
		//If no ItemID, we need to check if there exist a menu item entry for Ninjaboard
		if(KRequest::get('get.Itemid', 'int') === NULL)
		{
		    $component    = JComponentHelper::getComponent('com_ninjaboard');
		    $menu         = JSite::getMenu();
		    $items        = $menu->getItems('componentid', $component->id);
		    
		    // If any menu links to Ninjaboard, find out if any root ones exists
		    if(is_array($items))
		    {
		    	foreach ($items as $item)
		    	{
		    	    if(isset($item->query['view']) && $item->query['view'] == 'forums')
		    	    {
		    	        // Perform a 301 redirect to the right menu item to eliminate duplicate entrypoints
		    	        return KFactory::get('lib.joomla.application')
		    	                   ->redirect(JRoute::_('&Itemid='.$item->id, false), '', '', true);
		    	    }
		    	}
		    }
		}
		
		//Parse the query in the pathway url
		$query = array('Itemid' => KRequest::get('get.Itemid', 'int'));
		if(is_object($last) && isset($last->link)) parse_str(str_replace('index.php?',  '', $last->link), $query);

		// We need to find out if the menu item link has a view param
		$menuquery	= array('view' => '');
		$menu		= JSite::getMenu();
		$item		= $menu->getItem($query['Itemid']);
		
		//Menu item id is invalid, so lets get the active menu item
		if(!$item)	$item = $menu->getActive();
		
		//There is no active menu item, so we must be on the default page (without Itemid)
		if(!$item)	$item = $menu->getDefault();

		//Menu is still false, so abort the operation to prevent warnings
		if(!$item) return;

		parse_str(str_replace('index.php?',  '',$item->link), $menuquery); // remove "index.php?" and parse
		if(!isset($menuquery['view'])) $menuquery['view'] = '';
		
		if($view->getName() != 'forums' && (!$last || KInflector::pluralize($menuquery['view']) != 'forums'))
		{
			$forum = KFactory::tmp('site::com.ninjaboard.controller.forum', array('request' => array('view' => 'forums')))->getView();
			$this->registerCallback('after.render', array($forum, 'setBreadcrumbs'));
		}

		//If the view is forums, don't append the crumb if there's something already that links to it
		if($view->getName() == 'forums' && $menuquery['view'] == 'forums') return;

		$this->registerCallback('after.render', array($view, 'setDocumentTitle'));
		$this->registerCallback('after.render', array($view, 'setBreadcrumbs'));
	}

    protected function _initialize(KConfig $config)
    {
        $config->append(array(
                'controller_default' => 'forum'
        ));

        parent::_initialize($config);
    }
}