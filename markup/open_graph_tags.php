<?php namespace ProcessWire; 

/**
 * Write out Open Graph tags
 * with a couple of defaults
 * 
 */


// meh this isn't always right but it'll do as a fallback for the mo.
$site_url_canonical=$_SERVER['HTTP_HOST'];


// Site name
if(isset($options['site_name'])){
	$site_name=$options['site_name'];
}else{
	$site_name=$pages->get('/')->title; // use the site name from the home page if we've not set it.
}


// Page title
if(isset($options['page_title'])){
	$millco_title=$options['page_title'];
}else{
	$millco_title=$page->title;
}

// Page description
$summary_field_name='summary'; // default field name for the summary
$desc=$page->title; // default description

if(isset($options['summary_field_name'])){
	$summary_field_name=$options['summary_field_name'];
}

// Do we have a page summary? 
if($page->{$summary_field_name}){
	$desc=strip_tags($page->{$summary_field_name});
}elseif($page->content){ // if we don't have a summary, see if we have a content field.
	$desc=$page->content;
}

// Truncate string to closest sentence within 165 characters
$desc = $sanitizer->truncate($desc, 165, 'sentence');


// NB. ProCache removes the quotes from the following metatags
// so they should NOT end with a slash for closing the tag. Who knew?
echo '<meta property="og:type" content="website">' . PHP_EOL;
echo '<meta property="og:title" content="'.$millco_title.'">' . PHP_EOL;
echo '<meta property="og:description" content="'.$desc.'">' . PHP_EOL;
echo '<meta property="og:url" content="' . $page->httpUrl . '">' . PHP_EOL;
echo '<meta property="og:site_name" content="'.$site_name.'">' . PHP_EOL;


// Open Graph image

$open_graph_image_field='featured_image'; // default field name for the image

if(isset($options['image_field'])){
	$open_graph_image_field=$options['image_field'];
}

// try and use the image from the page, unless we've been told to use the default images.
$use_default_images=false;

if(isset($options['use_default_images'])){
	$use_default_images=$options['use_default_images'];
}

if($page->{$open_graph_image_field} && !$use_default_images){

	// create a landscape image
	$og_landscape_image = $page->{$open_graph_image_field}->size(1200, 630, [
		'cropping' => 'center',
		'quality' => 60,
		'upscaling' => true,
		'sharpening' => 'medium'
	]);

	echo '<meta property="og:image" content="'.$og_landscape_image->httpUrl.'">' . PHP_EOL;
	echo '<meta property="og:image:width" content="1200">' . PHP_EOL;
	echo '<meta property="og:image:height" content="630">' . PHP_EOL;

	// create a square image
	$og_square_image = $page->{$open_graph_image_field}->size(1200, 1200, [
		'cropping' => 'center',
		'quality' => 60,
		'upscaling' => true,
		'sharpening' => 'medium'
	]);

	echo '<meta property="og:image" content="'.$og_square_image->httpUrl.'">' . PHP_EOL;
	echo '<meta property="og:image:width" content="1200">' . PHP_EOL;
	echo '<meta property="og:image:height" content="1200">' . PHP_EOL;


}else{

	// do we have default images?
	// TODO: should check for png as well really.
	$og_landscape_image='og_landscape.jpg';
	$og_square_image='og_square.jpg';

	if(isset($options['og_landscape_image'])){
		$og_landscape_image=$options['og_landscape_image'];
	}

	if(isset($options['og_square_image'])){
		$og_square_image=$options['og_square_image'];
	}

	$og_landscape_image_path=wire('config')->paths->assets . 'images/' . $og_landscape_image;
	$og_square_image_path=wire('config')->paths->assets . 'images/' . $og_square_image;

	if(file_exists($og_landscape_image_path)){

		// just need the relative path to the image
		$og_landscape_image_path=wire('config')->urls->assets . 'images/' . $og_landscape_image;

		echo '<meta property="og:image" content="'.$og_landscape_image_path.'">' . PHP_EOL;
		echo '<meta property="og:image:width" content="1200" />' . PHP_EOL;
		echo '<meta property="og:image:height" content="630" />' . PHP_EOL;
	}	

	if(file_exists($og_square_image_path)){

		// just need the relative path to the image
		$og_square_image_path=wire('config')->urls->assets . 'images/' . $og_square_image;

		echo '<meta property="og:image" content="'.$og_square_image_path.'">' . PHP_EOL;
		echo '<meta property="og:image:width" content="1200" />' . PHP_EOL;
		echo '<meta property="og:image:height" content="1200" />' . PHP_EOL;
	}
}















// <meta property="og:image" content="https://www.example.com/images/image-landscape.jpg" />
// <meta property="og:image:width" content="1200" />
// <meta property="og:image:height" content="630" />
// <meta property="og:image" content="https://www.example.com/images/image-square.jpg" />
// <meta property="og:image:width" content="1200" />
// <meta property="og:image:height" content="1200" />




