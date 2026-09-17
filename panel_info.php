<?php

namespace ProcessWire;

/**
 * Template file which is included in the top panel
 * in the admin.
 * Handy for project links and the like.
 * 
 * @var \ProcessWire\MillcoUtils $mu
 */

echo '<div class="uk-grid-medium uk-grid-row-medium uk-child-width-auto uk-text-small uk-text-muted" uk-grid="margin: uk-grid-margin-medium">';

echo '<div><strong>Processwire Version : </strong>' .  wire('config')->versionName . '</div>';

echo '<div><strong>Utils Version : </strong>' .  $mu->getModuleInfo()['version'] . '</div>';

// PHP_VERSION is always defined. phpversion('tidy') is the tidy
// extension version and is empty when that extension is not installed.
$php_version = PHP_VERSION;

echo '<div><strong>PHP version : </strong>' . $php_version . '</div>';
if ($_SERVER['REMOTE_ADDR']) {
	echo '<div><strong>Your IP address : </strong>' . $_SERVER['REMOTE_ADDR'] . '</div>';
}
echo '<div><strong>Debug : </strong>' . (wire('config')->debug ? '<span class="uk-text-danger">On</span>' : 'Off') . '</div>';


echo '</div>';
