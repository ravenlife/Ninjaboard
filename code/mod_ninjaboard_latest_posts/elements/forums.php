<?php
/**
 * @category	Ninjaboard
 * @package		Modules
 * @subpackage 	Ninjaboard_latest_posts
 * @copyright	Copyright (C) 2010 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * JElementForums Class - for displaying a select list of forums
 */
class JElementForums extends JElement
{
   var   $_name = 'Forums';

   /**
    * Method for building the element
    */
   function fetchElement($name, $value, &$node, $control_name)
   {
		if (file_exists(JPATH_SITE.'/components/com_ninjaboard/ninjaboard.php')){
			$size 		= ( $node->attributes('size') ? $node->attributes('size') : 5 );
			$options 	= array();
			$list 		= KService::get('com://admin/ninjaboard.model.forums')->enabled(1)->getList();

			foreach ($list as $item) {
				$options[] = JHTML::_('select.option', $item->id, str_repeat('&#160;', ($item->level - 1) * 6).$item->title, 'id', 'title');
			}
			
			return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]',  ' multiple="multiple" size="' . $size .'" class="inputbox"', 'id', 'title', $value, $control_name.$name);
	   } else {
		   return JText::_('MOD_NINJABOARD_LATEST_POSTS_NINJABOARD_NOT_INSTALLED');
	   }
   }
}