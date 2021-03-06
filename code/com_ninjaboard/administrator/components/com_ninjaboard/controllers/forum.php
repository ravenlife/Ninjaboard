<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Forum Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerForum extends ComNinjaboardControllerDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		$config->append(array(
			'request' => array(
				'sort' => 'path_sort_ordering',
				'enabled' => ''
			)
		));
	
		parent::__construct($config);

		$this->registerCallback('after.add', array($this, 'setPermissions'))
			 ->registerCallback('after.edit', array($this, 'setPermissions'));

		$this->getService('com://admin/ninjaboard.controller.maintenance')->forums();
	}

	/**
	 * Generic function for setting the permissions
	 *
	 * @return void
	 */
	public function setPermissions($context)
	{
		//Temp fix
		if(KInflector::isPlural(KRequest::get('get.view', 'cmd')) || KRequest::type() == 'AJAX') return;
		
		$model 		= $this->getModel();
		$table		= $this->getService($this->getService('ninja:template.helper.access')->models->assets)->getTable();
		$query		= $table->getDatabase()->getQuery();
		$item  		= $model->getItem();
	
		$identifier = $this->getIdentifier();
		$id			= $identifier->type.'_'.$identifier->package.'.'.$identifier->name.'.'.$item->id.'.';
		 
		$permissions = (array) KRequest::get('post.permissions', 'int');
		$editable	 = KRequest::get('post.editpermissions', 'boolean', false);

		if(!$permissions && $editable)
		{
			$query->where('tbl.name', 'LIKE', $id.'%');
			$table->select($query)->delete();
		}
		
		$safe 	= array();
		$query	= $table->getDatabase()->getQuery()->where('name', 'like', $id.'%');
		foreach((array)KRequest::get('post.params.permissions', 'raw') as $group => $other)
		{
			$safe[] = $id.$group;
		}
		if($safe) $query->where('name', 'not in', $safe);
		$table->select($query, KDatabase::FETCH_ROWSET)->delete();

		foreach($permissions as $usergroup => $rules)
		{	
			foreach($rules as $name => $permission)
			{
				$this->getService($this->getService('ninja:template.helper.access')->models->assets)
					->name($id.$usergroup.'.'.$name)
					->getItem()
					->setData(array('name' => $id.$usergroup.'.'.$name, 'level' => $permission))
					->save();
			}
		}
	}
}