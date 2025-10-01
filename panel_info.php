<?php

namespace ProcessWire;

/**
 * Template file which is included in the top panel
 * in the admin.
 * Handy for project links and the like.
 */

 $mu = wire('modules')->get('MillcoUtils');


echo '<div class="uk-grid-small" uk-grid>';

if(isset($moduleConfig['holding_page']) && $moduleConfig['holding_page'] != ''){
	// Show a warning if the holding page password is set.
	echo '<div class="uk-width-1-1"><div class="uk-alert-warning" uk-alert >' . $mu->icon('warning') . ' Holding page password set - this will prevent non-logged in users from viewing the site unless they have the password.</div></div>';
}

echo '<div class="uk-width-1-2"><strong>Processwire Version : </strong>' .  wire('config')->versionName . '</div>';
echo '<div class="uk-width-1-2"><strong>PHP version : </strong>' . phpversion('tidy') . '</div>';
if ($_SERVER['REMOTE_ADDR']) {
	echo '<div class="uk-width-1-2"><strong>Your IP address : </strong>' . $_SERVER['REMOTE_ADDR'] . '</div>';
}
echo '<div class="uk-width-1-2"><strong>Debug : </strong>' . (wire('config')->debug ? '<span class="uk-text-danger">On</span>' : 'Off') . '</div>';


echo '</div>';
