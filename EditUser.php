<?php
/**
 * EditUser extension by Ryan Schmidt
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'EditUser' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['EditUser'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['EditUserAliases'] = __DIR__ . '/EditUser.alias.php';
	/* wfWarn(
		'Deprecated PHP entry point used for EditUser extension. Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return;
} else {
	die( 'This version of the EditUser extension requires MediaWiki 1.25+' );
}
