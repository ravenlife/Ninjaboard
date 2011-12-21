<?php
/**
 * @version $Id: profile.php 959 2010-09-21 14:33:17Z stian $
 * @package Ninjaboard
 * @copyright Copyright (C) 2007-2008 Ninja Media Group. All rights reserved.
 * @license GNU/GPL. Please see license.php in Ninjaboard directory 
 * for copyright notices and details.
 * Ninjaboard is free software. This version may have been NOT modified.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Ninjaboard Edit Profile Model
 *
 * @package Ninjaboard
 */
class NinjaboardModelProfile extends NinjaboardModel
{
	/**
	 * profile field sets data array
	 *
	 * @var array
	 */
	var $_profilefieldsets = null;
	
	/**
	 * profile fields data array
	 *
	 * @var array
	 */
	var $_profilefields = null;

	/**
	 * get profile field sets
	 *
	 * @return array
	 */
	function getProfileFieldSets() {
				
		// load the profile field sets
		if (empty($this->_profilefieldsets)) {
			$db		=& JFactory::getDBO();
			$query	= "SELECT s.*"
					. "\n FROM #__ninjaboard_profiles_fields_sets AS s"
					. "\n WHERE s.published = 1"
					. "\n ORDER BY s.ordering"
					;
			$db->setQuery($query);
			$this->_profilefieldsets = $db->loadObjectList();		
		}

		return $this->_profilefieldsets;
	}
	
	/**
	 * get profile fields
	 *
	 * @return array
	 */
	function getProfileFields($ninjaboardUser) {
				
		// load the profile fields
		if (empty($this->_profilefields)) {
			$db		=& JFactory::getDBO();
			$query	= "SELECT f.*"
					. "\n FROM #__ninjaboard_profiles_fields AS f"
					. "\n WHERE f.published = 1"
					. "\n ORDER BY f.ordering"
					;
			$db->setQuery($query);
			$fieldrows = $db->loadObjectList();
			
			$fields = array();
			foreach($fieldrows as $fieldrow) {
				switch ($fieldrow->element) {
					case '0':		// TextBox			
					case '1':		// TextArea				
					case '2':		// CheckBox
					case '3':		// RadioButton
					case '4':		// ListBox
						$fieldrow->value = $ninjaboardUser->get($fieldrow->name);
						break;
					case '5':		// ComboBox
						// list profile field list values		
						$query = "SELECT v.name"
								. "\n FROM #__ninjaboard_profiles_fields_lists_values AS v"
								. "\n WHERE v.id_profile_field_list = ". $fieldrow->id_profile_field_list
								. "\n AND v.value = ". $ninjaboardUser->get($fieldrow->name)
								. "\n AND v.published = 1"
								;
						$db->setQuery( $query );
						$fieldrow->value = $db->loadResult();	
						break;															
					default:
						;
						break;
				}
				$fields[] = $fieldrow;
			}
			$this->_profilefields = $fields;		
		}
		return $this->_profilefields;
	}

}
?>