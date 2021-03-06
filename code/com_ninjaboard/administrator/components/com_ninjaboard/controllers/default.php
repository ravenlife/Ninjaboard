<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Default Controller
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerDefault extends NinjaControllerDefault
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

        $this->registerCallback('before.display', array($this, 'checkInstall'));
		
		$cache = JPATH_ROOT.'/cache/com_'.$this->getIdentifier()->package . '/maintenance.txt';

		if(!JFile::exists($cache))
		{
			if($this->getService('com://admin/ninjaboard.controller.maintenance')->topics() && $this->getService('com://admin/ninjaboard.controller.maintenance')->forums())
			{
			    JFile::write($cache, date('r'));
			}
		}
	}

    /**
     * Checks if the site is a clean and fresh install
     * If true, then it'll display a message pointing to the Tools screen and the Sample Content importer.
     *
     * @author Stian Didriksen <stian@ninjaforge.com>
     */
    public function checkInstall()
    {
    	//Do nothing if there's already data in Ninjaboard
    	$existing = $this->getService('com://admin/ninjaboard.model.forums')->getTotal();
    	if($existing) return $this;
    
    	// Check if migration table exists, and if it contain data
    	$migrated = 0;
    	try {
    		$migrated = $this->getService('com://admin/ninjaboard.model.forums_backups')->getTotal();
    	} catch(KDatabaseTableException $e) {
    		//Do nothing
    	}
    	
    	//We can't auto import sample data
    	if($migrated) return;
    	
    	//$this->getService('com://admin/ninjaboard.controller.tool')->execute('import');
    	//JError::raiseNotice(0, JText::_('COM_NINJABOARD_IN_ORDER_TO_GET_YOU_STARTED_WITH_USING_NINJABOARD_SAMPLE_CONTENT_WAS_JUST_IMPORTED'));
    	JError::raiseNotice(0, sprintf(
    		JText::_('COM_NINJABOARD_IN_ORDER_TO_GET_YOU_STARTED_WITH_USING_NINJABOARD'),
    		'<a href="'.JRoute::_('&option=com_ninjaboard&view=tools&shortcut=demo').'">'.
    		JText::_('COM_NINJABOARD_IMPORT_SAMPLE_CONTENT').
    		'</a>'
    	));
    	
    }

	public function setTitle()
	{
		$title = KRequest::get('post.title', 'string', 'Untitled');
		$id	   = KRequest::get('get.id', 'int', 0);
		
		$table		= $this->getModel()->getTable();
		$primaryKey	= current($table->getPrimaryKey())->name;
		$query		= $table->getDatabase()->getQuery()->where('title', '=', $title)->where($primaryKey, '!=', $id);

		if($table->count($query))
		{
			KRequest::set('post.title', $title . ' ' . JText::_('COM_NINJABOARD_COPY'), 'string');
			if((bool) $table->count($table->getDatabase()->getQuery()->where('title', '=', KRequest::get('post.title', 'string'))->where($primaryKey, '!=', $id))) self::setTitle();
		}
		
		return $this;
	}
	
	/**
	 * Generic function for setting the permissions
	 *
	 * @return void
	 */
	public function setPermissions()
	{
		$model 		= $this->getModel();
		$table		= $this->getService($this->getService('ninja:template.helper.access')->models->assets)->getTable();
		$query		= $table->getDatabase()->getQuery();
		$item  		= $model->getItem();
		$identifier = $this->getIdentifier();
		$id			= $identifier->type.'_'.$identifier->package.'.'.$identifier->name.'.'.(int)$item->id.'.';
		 
		$permissions = (array) KRequest::get('post.permissions', 'int');
		$editable	 = KRequest::get('post.editpermissions', 'boolean', false);
		
		if(!$permissions && $editable)
		{
			$query->where('tbl.name', 'LIKE', $id.'%');
			$table->select($query)->delete();
			return;
		}
		
		foreach($permissions as $name => $permission)
		{
			$this->getService($this->getService('ninja:template.helper.access')->models->assets)
				->name($id.$name)
				->getItem()
				->setData(array('name' => $id.$name, 'level' => $permission))
				->save();
		}	
	}
	
	/**
	 * Raise closable hint
	 *
	 * @author Stian Didriksen <stian@ninjaforge.com>
	 * @param $param
	 */
	protected function _raiseClosableMessage($text)
	{
		$id		= md5($text);
		$close	= $this->getService('ninja:template.helper.document')->formid('close-'.$id);
		$cookie	= $this->getService('ninja:template.helper.document')->formid('show-'.$id);

		if(!KRequest::has('cookie.'.$cookie))
		{
			JFactory::getApplication()->enqueueMessage( 
				JText::_($text).
				' <a href="#" id="' . $close . '">[' .
					JText::_('COM_NINJABOARD_CLOSE') .
				']</a>'
			);
			$this->getService('ninja:template.helper.document')->load('js', 
				"window.addEvent('domready', function(){
					$('$close').addEvent('click', function(event){
						(new Event(event)).stop();
						var parent = this.getParent('li');
						if(!parent.getSiblings().length) {
							parent = parent.getParent().getParent();
						}
						parent.dissolve({onComplete: function(){
							this.element.remove();
						}});
						Cookie.write('$cookie', true, {duration: 365});
					});
				});"
			);
			
			$this->getService('ninja:template.helper.document')->load('css', "
			#$close {
				display: block;
				color: inherit;
				float: right;
				margin-right: 5px;
			}");
		}
	}
}