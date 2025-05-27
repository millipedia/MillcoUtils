<?php

namespace ProcessWire;

/**
 * Template file which is included in the top panel
 * in the admin.
 * Handy for project links and the like.
 */


echo '<div class="uk-grid-small" uk-grid>';

echo '<div class="uk-width-1-2"><strong>Processwire Version : </strong>' .  wire('config')->versionName . '</div>';
echo '<div class="uk-width-1-2"><strong>PHP version : </strong>' . phpversion('tidy') . '</div>';
if ($_SERVER['REMOTE_ADDR']) {
	echo '<div class="uk-width-1-2"><strong>Your IP address : </strong>' . $_SERVER['REMOTE_ADDR'] . '</div>';
}
echo '<div class="uk-width-1-2"><strong>Debug : </strong>' . (wire('config')->debug ? '<span class="uk-text-danger">On</span>' : 'Off') . '</div>';


echo '</div>';
