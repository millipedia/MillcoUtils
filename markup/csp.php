<?php 
/**
 * CSP markup
 * Soon we'll do something more dynamic and configurable.
 * But for now this is a starting point.
 */

 /** @var MillcoUtils $mu */

?>

<meta http-equiv="Content-Security-Policy" content=" default-src 'self' https://www.youtube-nocookie.com https://player.vimeo.com/ https://cabin.millipedia.net/duration https://stats.millipedia.net/ https://cabin.millipedia.net/; font-src 'self'; style-src 'self' 'unsafe-inline' ; script-src 'self' https://cabin.millipedia.net/duration https://cabin.millipedia.net/ https://cdn.usefathom.com https://www.youtube.com/ https://player.vimeo.com/ 'nonce-<?=$mu->nonce;?>' 'strict-dynamic'; 
connect-src 'self' https://stats.millipedia.net/ https://cabin.millipedia.net/ 'nonce-<?=$mu->nonce;?>' 'strict-dynamic'; 
frame-src 'self' https://player.vimeo.com/ https://www.youtube-nocookie.com; img-src * data: ;">


