# MillcoUtils

Very early (not really ready for public consumption) and very opinionated ProcessWire module that does a couple of useful things that I like to have across most sites.

One day it might have some more useful things.

I was spurred into action to put these things into a module by Bernhard's [RockFrontend](https://processwire.com/modules/rock-frontend/) module. That's a lot fancier than this is ever likely to be so go and check that out.

# Things it does so far.

## Nonce

Adds a nonce property to your page which you can add to your CSP and then inline script / styles.

    $page->nonce

	or

	$mu->nonce

## Edit bar

Adds an edit link and link to the page tree to front end pages for logged in users. Superusers also get a link to edit the template. This behaves in a simlar fashion to Bernhard's RockFrontend edit bar. You can set the inital position of the toolbar in admin now (it can be dragged when editing a page as well.)

You can also add buttons by adding a link, label and icon to the relevant config field. 
At the moment I've got a limited number of icons included but you can add more or I probably will when I'm adding them. The ones I'm using at the mo are the IBM Carbon icons [grabbed from here](https://icon-sets.iconify.design/carbon/)   

## Inline icons

	$mu->file_icon('whatever');

will inline an svg file with the name whatever.svg if it exists in the site/assets/images/icons folder. 
We normally use 'currentColor' in SVGs loading this way.

## Source set and defaults for image markup

	$mu->source_set($image);

Source set is pushing it a bit, but if you pass an image to $mu->source_set then we'll wrap it in a picture element with webp and original versions of the image. 
We also create a smaller version of the image for mobile devices.

The function takes an image, optional width and height and then an array of various options:

	echo $mu->source_set($page->featured_image, 640, 480, ["class" => " card_image", "no_caption" => 1,  "not_lazy" =>1, "quality" => "high"]);

The function checks a custom image field 'img_caption' and will wrap the picture element in figure tags with a caption.

Alt text is pulled from either an 'img_alt' field or the img 'description' value.

# Things it doesn't yet but will soon.

- [ ] Configurable CSP


stephen at [millipedia.com](https://millipedia.com)