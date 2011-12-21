<?php defined( 'KOOWA' ) or die( 'Restricted access' );
/**
 * @version		$Id: themes.php 1357 2011-01-10 18:45:58Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */

/**
 * Ninjaboard Themes model
 *
 * @TODO reconnect the napi install api, so we can install new templates.
 *		 and make templates editable.
 * 
 * @author Stian Didriksen <stian@ninjaforge.com>
 */
class ComNinjaboardModelThemes extends ComNinjaModelFilesystem
{
	protected function _parseTemplate($xml)
	{
		$template = new KObject;
		foreach ($xml as $name) 
		{
			if($name->name() == 'form') {
				$template->set($name->name(), $name);
			} else {
				$template->set($name->name(), $name->data());
			}
		}
		return $template;
	}
	
	public function getItem()
	{
		return (object) array(
			'xml'		=> simplexml_load_file(JPATH_ROOT.'/components/com_ninjaboard/themes/'.$this->_state->name.'/'.$this->_state->name.'.xml'),
			'params'	=> KFactory::tmp('admin::com.ninjaboard.model.settings')->id(KRequest::get('get.setting', 'int'))->getParams()
		);
	}
}