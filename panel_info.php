<?php

namespace ProcessWire;

/**
 * Template file which is included in the top panel
 * in the admin.
 * Handy for project links and the like.
 * 
 * @var \ProcessWire\ProcessMillcoUtils $this
 */

echo '<div class="uk-grid-small uk-text-small uk-text-muted" uk-grid>';

echo '<div class="uk-width-1-3"><strong>Processwire Version : </strong>' .  wire('config')->versionName . '</div>';

echo '<div class="uk-width-1-3"><strong>Utils Version : </strong>' .  $mu->getModuleInfo()['version'] . '</div>';

// PHP_VERSION is always defined. phpversion('tidy') is the tidy
// extension version and is empty when that extension is not installed.
$php_version = PHP_VERSION;

echo '<div class="uk-width-1-3"><strong>PHP version : </strong>' . $php_version . '</div>';
if ($_SERVER['REMOTE_ADDR']) {
	echo '<div class="uk-width-1-2"><strong>Your IP address : </strong>' . $_SERVER['REMOTE_ADDR'] . '</div>';
}
echo '<div class="uk-width-1-2"><strong>Debug : </strong>' . (wire('config')->debug ? '<span class="uk-text-danger">On</span>' : 'Off') . '</div>';


echo '</div>';
