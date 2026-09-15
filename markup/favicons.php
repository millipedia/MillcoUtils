<?php namespace ProcessWire; 

/**
 * Write out Favicons
 * This matches the files provided by https://realfavicongenerator.net/
 * 
 */

 /** @var Page $page */
 /** @var Pages $pages */
 /** @var Sanitizer $sanitizer */
 /** @var array $options */


// Site name
if(isset($options['site_name'])){
	$site_name=$options['site_name'];
}else{
	$site_name=wire()->pages->get('/')->title; // use the site name from the home page if we've not set it.
}

?>

<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<meta name="apple-mobile-web-app-title" content="<?php echo $site_name; ?>">
<link rel="manifest" href="/site.webmanifest">