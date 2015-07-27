<?php
/**
* EditUser extension by Ryan Schmidt
*/

if(!defined('MEDIAWIKI')) {
	echo "This file is an extension to the MediaWiki software and is not a valid access point";
	die(1);
}

$dir = dirname(__FILE__) . '/';

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'EditUser',
	'version'        => '1.7.0',
	'author'         => 'Ryan Schmidt',
	'descriptionmsg' => 'edituser-desc',
	'url'            => 'https://www.mediawiki.org/wiki/Extension:EditUser',
);

// Internationlization files
$wgMessagesDirs['EditUser'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['EditUserAliases'] = $dir . 'EditUser.alias.php';
// Special page classes
$wgAutoloadClasses['EditUser'] = $dir . 'EditUser_body.php';
$wgAutoloadClasses['EditUserPreferencesForm'] = $dir . 'EditUserPreferencesForm.php';
$wgSpecialPages['EditUser'] = 'EditUser';
$wgSpecialPageGroups['EditUser'] = 'users';

// Default group permissions
$wgAvailableRights[] = 'edituser';
$wgAvailableRights[] = 'edituser-exempt';
$wgGroupPermissions['bureaucrat']['edituser'] = true;
$wgGroupPermissions['sysop']['edituser-exempt'] = true;
