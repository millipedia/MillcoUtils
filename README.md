# MillcoUtils

Very early (not really ready for public consumption) and very opinionated ProcessWire module that does a couple of useful things that I like to have across most sites.

One day it might have some more useful things.

I was spurred into action to put these things into a module by Bernhard's [RockFrontend](https://processwire.com/modules/rock-frontend/) module. That's a lot fancier than this is ever likely to be so go and check that out.

# Things it does so far.

## Nonce

Adds a nonce property to your page which you can add to your CSP and then inline script / styles.

    $page->nonce

## Edit bar

Adds an edit link and link to the page tree to front end pages for logged in users. Superusers also get a link to edit the template. This behaves in a simlar fashion to Bernhard's RockFrontend edit bar. You can set the inital position of the toolbar in admin now (it can be dragged when editing a page as well.)

You can also add buttons by adding a link, label and icon to the relevant config field. 
At the moment I've got a limited number of icons included but you can add more or I probably will when I'm adding them. The ones I'm using at the mo are the IBM Carbon icons [grabbed from here](https://icon-sets.iconify.design/carbon/)   

## Inline icons

	$mu->file_icon('whatever');

will inline an svg file with the name whatever.svg if it exists in the site/assets/images/icons folder. 
We normally use 'currentColor' in SVGs loading this way.



# Things it doesn't yet but will soon.

- [ ] Configurable CSP


stephen at [millipedia.com](https://millipedia.com)