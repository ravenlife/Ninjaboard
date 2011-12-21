<?php
/**
 * @version		$Id: orderable.php 1357 2011-01-10 18:45:58Z stian $
 * @package		Ninjaboard
 * @copyright	Copyright (C) 2011 NinjaForge. All rights reserved.
 * @license 	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */ 

/**
 * Specializes the core orderable class in koowa to support hierarchies
 *
 * @author		Stian Didriksen <stian@ninjaforge.com>
 * @package     Ninjaboard
 * @subpackage 	Behaviors
 */
class ComNinjaboardDatabaseBehaviorOrderable extends KDatabaseBehaviorOrderable
{
	/**
	 * Resets the order of all rows
	 *
	 * @return	KDatabaseTableAbstract
	 */
	public function reorder()
	{
		$table	= $this->getTable();
		$db 	= $table->getDatabase();
		$query 	= $db->getQuery();
		
		//Build the where query
		$this->_buildQueryWhere($query);

		$db->execute("SET @order = 0");
		$db->execute(
			 'UPDATE #__'.$table->getBase().' '
			.'SET ordering = (@order := @order + 1) '
			.(string) $query.' '
			.'ORDER BY path, ordering ASC'
		);

		return $this;
	}
}