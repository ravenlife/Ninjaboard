<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Avatar Controller
 *
 * Used mainly to display people avatars in various sizes
 *
 * @package Ninjaboard
 */
class ComNinjaboardControllerAvatar extends ComNinjaboardControllerAttachment
{
	/**
	 * Constructor
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		KRequest::set('get.format', 'file');

		//When no id is set in the url, then we should assume the user wants to see his own profile
		$me		= KService::get('com://site/ninjaboard.model.people')->getMe();
		$config->append(array(
			'request' => array(
				'id'   => $me->id
			)
		));

		parent::__construct($config);

		//@TOD To prevent errors like on profile edit screen, remember to remove this line if we add layouts
		$this->_request->layout = 'default';
		$this->_request->format = 'file';
	}
	
	/**
	 * Read action, creates an row if it don't already exist
	 */
	protected function _actionRead(KCommandContext $context)
	{
		$request	= $this->getRequest();
		$row		= parent::_actionRead($context);
		
		//Only auto create people that exist
		if(!$row->id && $request->id) {
			//Check that the person exists, before creating Ninjaboard record
			$exists = $this->getService('com://site/ninjaboard.model.users')->id($request->id)->getTotal() > 0;
			if(!$exists) {
				JError::raiseWarning(404, JText::_('COM_NINJABOARD_PERSON_NOT_FOUND'));
				return $row;
			}
		
			$row->id = $request->id;
			$row->save();
			
			//In order to get the data from the jos_users table, we need to rerun the query by getting a fresh row and setting the data
			$new = $this->getModel()->id($row->id)->getItem();
			$row->setData($new->getData());
		}

		return $row;
	}
}