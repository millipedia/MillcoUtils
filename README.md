# MillcoUtils

Very early (not really ready for public consumption) and very opinionated ProcessWire module that does a couple of useful things that I like to have across most sites.

One day it might have some more useful things.

I was spurred into action to put these things into a module by Bernhard's [RockFrontend](https://processwire.com/modules/rock-frontend/) module. That's a lot fancier than this is ever likely to be so go and check that out.

# Things it does so far.

1. Adds a nonce property to your page which you can add to your CSP and then inline script / styles.

    $page->nonce

2. Adds an edit link and link to the page tree to front end pages for logged in users. Superusers also get a link to edit the template. This behaves in a simlar fashion to Bernhard's RockFrontend edit bar. You can set the inital position of the toolbar in admin now (it can be dragged when editing a page as well.)

3. Adds hooks for any php files you add to templates/ajax. This is pretty much a wholesale [copy of a RockFrontend feature](https://www.baumrock.com/en/processwire/modules/rockfrontend/docs/ajax/) and in fact I may well take it back out and rely on RF rather than duplicating it.

# Things it doesn't yet but will soon.

- [ ] Configurable CSP


stephen at [millipedia.com](https://millipedia.com)