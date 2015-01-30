<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

// Use for styling the menu
'class' => 'navbar',
 
// Here comes the menu strcture
'items' => [

// This is a menu item
'home'  => [
	    'text'  => 'Hem',
            'url'   => '',
            'title' => 'Hem'
        ],

// This is a menu item
'questions'  => [
		 'text'  => 'Frågor',
		 'url'   => 'comments',
		 'title' => 'Frågor'
		 ],
		
// This is a menu item
'tags'  => [
	    'text'  => 'Taggar',
            'url'   => 'tags',
            'title' => 'Taggar'
	    ],

// This is a menu item
'users'  => [
            'text'  => 'Användare',
            'url'   => 'users',
            'title' => 'Användare'
        ],

// This is a menu item
    	'rss' => [
    		'text'  =>'WE <i class="fa fa-heart"></i> SPRING',
    		'url'   => 'rss',
    		'title' => 'RSS om vårinspiration'
    	],
    ],
 
// Callback tracing the current selected menu item base on scriptname
'callback' => function ($url) {
    if ($url == $this->di->get('request')->getRoute()) {
	return true;
    }
},

// Callback to create the urls
'create_url' => function ($url) {
    return $this->di->get('url')->create($url);
    },
];