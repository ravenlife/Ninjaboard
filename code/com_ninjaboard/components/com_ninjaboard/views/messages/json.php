<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2012 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

class ComNinjaboardViewMessagesJson extends KViewJson 
{
	/**
	 * Return the views output
		 *
	 *  @return string 	The output of the view
	 */
	public function display()
	{
		$model		= $this->getModel();
		$template	= $this->getService('com://site/ninjaboard.view.message.html')->getTemplate()->addFilter(array($this->getService('ninja:template.filter.document')));
		$params 	= $this->getService('com://admin/ninjaboard.model.settings')->getParams();
		$me			= $this->getService('com://admin/ninjaboard.model.people')->getMe();
		$data		= array();

		foreach($model->getList() as $message)
		{
			$result			= $message->getData();
			$result['html']	= $template->loadIdentifier('com://site/ninjaboard.view.message.list', array(
				'message'	=> $message,
				'params'	=> $params,
				'me'		=> $me
			))->render(true);
			$data[] = $result;
		}
		
		return json_encode($data);
	}
}