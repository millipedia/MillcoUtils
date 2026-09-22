<?php

namespace ProcessWire;

/**
 * Install ProcessDocumentation (admin help / docs pages)
 */

$wire = wire();

/** @var MillcoUtils $mu */
$mu = $wire->modules->get('MillcoUtils');

if ($wire->modules->isInstalled('ProcessDocumentation')) {
	$wire->message('ProcessDocumentation is already installed');
	return;
}

$module = $mu->installModule(
	'ProcessDocumentation',
	'https://github.com/outflux3/ProcessDocumentation/archive/refs/heads/master.zip'
);

if ($module) {
	$wire->message('ProcessDocumentation has been installed');
} else {
	$wire->error('Could not install ProcessDocumentation — check that module downloads are allowed');
}
