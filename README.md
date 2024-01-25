# MillcoUtils

Very early (not really ready for public consumption) and very opinionated ProcessWire module that does a couple of useful things that I like to have across most sites.

One day it might have some more useful things.

I was spurred into action to put these things into a module by Bernhard's [RockFrontend](https://processwire.com/modules/rock-frontend/) module. That's a lot fancier than this is ever likely to be so go and check that out.

# Things it does so far.

1. Adds a nonce property to your page which you can add to your CSP and then inline script / styles.

    $page->nonce

2. Adds an edit link and link to the page tree to front end pages for logged in users. Superusers also get a link to edit the template. This behaves in a simlar fashion to Bernhart's RockFrontend edit bar.

# Things it doesn't yet but might one day.

- [ ] Configurable CSP
- [ ] Open graph ...  this might be better an an include


stephen at [millipedia.com](https://millipedia.com)