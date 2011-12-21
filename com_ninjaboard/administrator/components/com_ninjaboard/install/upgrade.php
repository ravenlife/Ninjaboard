<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version		$Id: upgrade.php 1394 2011-01-11 22:03:28Z stian $
 * @category	Ninjaboard
 * @copyright	Copyright (C) 2007 - 2011 NinjaForge. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://ninjaforge.com
 */
 

$db	= JFactory::getDBO();
foreach(array(
	'assets',
	'attachments',
	'profile_fields',
	'forums',
	'posts',
	'ranks',
	'settings',
	'subscriptions',
	'topics',
	'people',
	'user_groups',
	'user_group_maps',
	'joomla_user_group_maps',
	'iconsets'
) as $name)
{
	$tables[] = '#__ninjaboard_'.$name;
}
$tables = $db->getTableFields($tables);
foreach($tables as $name => $fields)
{
	unset($tables[$name]);
	$tables[str_replace('#__ninjaboard_', '', $name)] = $fields;
}

//Check if this is a migration from Ninjaboard 0.5
if(isset($tables['forums']['id']))
{
	//Backup tables
	$extname = 'com_ninjaboard'; //$extname is also used in the migrate.php itself
	$path	 = JPATH_ADMINISTRATOR.'/components/'.$extname.'/install/migrate.php';
	if(JFile::exists($path)) require_once $path;
}

//Modify assets table
if(isset($tables['assets']['parent_id']))
{
	$db->setQuery('DROP INDEX idx_parent_id ON #__ninjaboard_assets');
	$db->query();
	
	$db->setQuery('DROP INDEX idx_lft_rgt ON #__ninjaboard_assets');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets DROP COLUMN parent_id');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets DROP COLUMN lft');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets DROP COLUMN rgt');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets DROP COLUMN title');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets DROP COLUMN rules');
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_assets CHANGE COLUMN `ninjaboard_asset_id` `ninjaboard_asset_id` BIGINT(20) UNSIGNED NOT NULL auto_increment COMMENT ''  FIRST");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_assets CHANGE COLUMN `level` `level` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0  COMMENT 'The permission level. 0 = No Access, 1 = Has Access, 2 = and Can Create, 3 = and Can Manage'  AFTER `ninjaboard_asset_id`");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_assets CHANGE COLUMN `name` `name` VARCHAR(255) NOT NULL DEFAULT ''  COMMENT 'The unique name for the permission object'  AFTER `level`");
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_assets ENGINE = MyISAM');
	$db->query();
}

//Modify forums table
if(isset($tables['forums']['asset_id']))
{
	$db->setQuery('ALTER TABLE #__ninjaboard_forums DROP COLUMN asset_id');
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_forums DROP COLUMN lft');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_forums DROP COLUMN rgt');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_forums DROP COLUMN access');
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_forums CHANGE COLUMN `parent_id` `parent_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0  COMMENT 'Needed for some converters'  AFTER `ninjaboard_forum_id`");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_forums CHANGE COLUMN `level` `level` INT(10) UNSIGNED NOT NULL DEFAULT 0  COMMENT 'Hierarchy depth'  AFTER `parent_id`");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_forums CHANGE COLUMN `path` `path` VARCHAR(255) NOT NULL DEFAULT '/'  COMMENT 'Needed for the enumerated path implementation for the forum hierarchies'  AFTER `level`");
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_forums ENGINE = MyISAM');
	$db->query();
}

//Upgrade forums table to allow ordering and sorting
if(!isset($tables['forums']['path_sort']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_forums ADD COLUMN path_sort TEXT NOT NULL DEFAULT ''  COMMENT 'Cached ordering by path (path+ninjaboard_forum_id)'  AFTER `ordering`");
	$db->query();

	$db->setQuery("ALTER TABLE #__ninjaboard_forums ADD COLUMN path_sort_title TEXT NOT NULL DEFAULT '' COMMENT 'path_sort with ids replaced with forum titles'  AFTER `path_sort`");
	$db->query();

	$db->setQuery("ALTER TABLE #__ninjaboard_forums ADD COLUMN path_sort_ordering TEXT NOT NULL DEFAULT ''  COMMENT 'path_sort with ids replaced with ordering values'  AFTER `path_sort_title`");
	$db->query();
}

//Upgrade people table to allow Email Updates
if(!isset($tables['people']['notify_on_create_topic']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_people ADD COLUMN notify_on_create_topic TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Subscribe to threads I create'  AFTER `params`");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_people ADD COLUMN notify_on_reply_topic TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Subscribe to threads I reply to'  AFTER `notify_on_create_topic`");
	$db->query();
}

//Upgrade the subscriptions table, and change the email notification defaults on the people table
if(isset($tables['subscriptions']['joomla_user_id']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_people CHANGE `notify_on_create_topic` `notify_on_create_topic` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Subscribe to threads I create'");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_people CHANGE `notify_on_reply_topic` `notify_on_reply_topic` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Subscribe to threads I reply to'");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions CHANGE `joomla_user_id` `created_by` int(11) UNSIGNED NOT NULL DEFAULT '0';");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions ADD `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'  AFTER `created_by`;");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions ADD `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last activity for an watch'  AFTER `created_on`;");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions MODIFY COLUMN `subs_type_id` int(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `guest_email`;");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions CHANGE `subs_type_id` `subscription_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The subscription type. Currently forum, topic or person.';");
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_subscriptions CHANGE `id_subs_item` `subscription_type_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The subscription type id. Example: ninjaboard_forum_id.';");
	$db->query();
}

//Upgrade the subscriptions table, and change the email notification defaults on the people table
if(!isset($tables['profile_fields']['label']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_profile_fields CHANGE `name` `name` varchar(255) NOT NULL DEFAULT '' COMMENT '@Filter(".'"lib.koowa.filter.alpha"'.")';");
	$db->query();

	$db->setQuery("ALTER TABLE #__ninjaboard_profile_fields ADD `label` TEXT NOT NULL DEFAULT '' COMMENT 'Label of the column field'  AFTER `name`;");
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_profile_fields ENGINE = MyISAM');
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_profile_fields ADD UNIQUE `name` (`name`);');
	$db->query();
}

//Add move topic support
if(isset($tables['topics']['moved_from_forum_id']))
{
	$db->setQuery('DROP INDEX moved_from_forum_id ON #__ninjaboard_topics');
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_topics DROP COLUMN moved_from_forum_id');
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_topics DROP COLUMN access');
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_topics ADD COLUMN show_symlinks TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Show symlinks to moved topics'  AFTER `params`");
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_topics ENGINE = MyISAM');
	$db->query();
}

//Upgrade people table to allow setting custom display name
if(!isset($tables['people']['which_name']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_people ADD COLUMN which_name VARCHAR(8) NOT NULL DEFAULT '' COMMENT 'username, name or alias'  AFTER `signature`");
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_people ADD KEY (which_name)');
	$db->query();
	
	$db->setQuery("ALTER TABLE #__ninjaboard_people ADD COLUMN alias VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Custom screen name defined by person'  AFTER `which_name`");
	$db->query();
}

//Add theme support
if(!isset($tables['settings']['theme']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_settings ADD COLUMN theme VARCHAR(100) NOT NULL DEFAULT ''  AFTER `ordering`");
	$db->query();
	
	$db->setQuery('ALTER TABLE #__ninjaboard_settings ADD KEY (theme)');
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_settings ENGINE = MyISAM');
	$db->query();
}

//Add sortables support to usergroups
if(!isset($tables['user_groups']['ordering']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_user_groups ADD COLUMN ordering INT(11) NOT NULL DEFAULT '1'  AFTER `title`");
	$db->query();

	$db->setQuery('ALTER TABLE #__ninjaboard_user_groups ENGINE = MyISAM');
	$db->query();
}

//Add visibility support to usergroups
if(!isset($tables['user_groups']['visible']))
{
	$db->setQuery("ALTER TABLE #__ninjaboard_user_groups ADD COLUMN visible TINYINT(1) NOT NULL DEFAULT '1'  AFTER `ordering`");
	$db->query();
}