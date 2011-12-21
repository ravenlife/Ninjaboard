<?php defined( 'KOOWA' ) or die( 'Restricted access' );
 /**
 * @version		$Id: post.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardDatabaseRowPost extends ComNinjaboardDatabaseRowParam
{
	/**
	 * Cached rowset of this users Ninjaboard usergroups
	 *
	 * @var KDatabaseRowsetInterface
	 */
	protected $_usergroups;

	/**
     * Specialized in order to dynamically get the usergroups list when needed
     *
     * @TODO performance optimize this
     *
     * @param  	string 	The column name.
     */
    public function __get($column)
    {
    	if($column == 'usergroups')
		{
			if(!isset($this->_usergroups))
			{
				$user				= KFactory::tmp('admin::com.ninjaboard.model.users')
																						->id($this->created_by)
																						->getItem();
				$this->_usergroups	= $user->usergroups;
			}

			return $this->_usergroups;
		}

		return parent::__get($column);
	}
}