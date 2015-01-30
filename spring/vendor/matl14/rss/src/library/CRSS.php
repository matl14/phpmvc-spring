<?php

namespace matl14\library;

class CRSS
{
    private $feed;
	
	public function __construct(array $feedUrls)
    {
    	require_once(__DIR__ . '/../autoloader.php');
    	
    	// We'll process this feed with all of the default options.
    	$feed = new \SimplePie();
    	
    	$feed->set_cache_location(__DIR__ . '/cache');
    	
    	// Set which feed to process.
    	$feed->set_feed_url($feedUrls);
    	
    	// Run SimplePie.
    	$feed->init();
    	
    	// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
    	$feed->handle_content_type();
    	$this->feed = $feed;
    }
    
    public function printFeed() {
    	$feed = $this->feed;
    	
    	$html = "<div class='header'>
    	<h1><a href='{$feed->get_permalink()}'>{$feed->get_title()}</a></h1>
    	<p>{$feed->get_description()}</p>
    	</div>";
    	
    	foreach ($feed->get_items() as $item) {
    		$html .= "<div class='item'>
    		<h2><a href='{$item->get_permalink()}'>{$item->get_title()}</a></h2>
    		<p>{$item->get_description()}</p>
    		<p><small>Posted on {$item->get_date('j F Y | g:i a')}</small></p>
    		</div>";
    	}
    	
    	return $html;
    }
}