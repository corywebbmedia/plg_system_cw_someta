<?php
/**
 * @copyright   Copyright (C) 2016 Cory Webb Media, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die();

/*
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="The Name or Title Here">
<meta itemprop="description" content="This is the page description">
<meta itemprop="image" content="http://www.example.com/image.jpg">

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="Page Title">
<meta name="twitter:description" content="Page description less than 200 characters">
<meta name="twitter:creator" content="@author_handle">
<!-- Twitter summary card with large image must be at least 280x150px -->
<meta name="twitter:image:src" content="http://www.example.com/image.html">

<!-- Open Graph data -->
<meta property="og:title" content="Title Here" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://www.example.com/" />
<meta property="og:image" content="http://example.com/image.jpg" />
<meta property="og:description" content="Description Here" />
<meta property="og:site_name" content="Site Name, i.e. Moz" />
<meta property="article:published_time" content="2013-09-17T05:59:00+01:00" />
<meta property="article:modified_time" content="2013-09-16T19:08:47+01:00" />
<meta property="article:section" content="Article Section" />
<meta property="article:tag" content="Article Tag" />
<meta property="fb:admins" content="Facebook numberic ID" /> 
*/

/**
 * Set Google+/Schema.org metadata
 */
$doc->setMetaData('name', $page_info['name'], 'itemprop');

// Note, the following doesn't work because JDocument treats the name 'description'
//  differently than other names. I'm leaving it here (commented out) in hopes that
//  they will fix this problem.
//  $doc->setMetaData('description', 'test', 'itemprop');
//
// Instead, we have to add a custom tag to the headering using the following,
//  even though it is not ideal:
$doc->addCustomTag('<meta itemprop="description" content="' . trim($page_info['description'] . '" />'));

$doc->setMetaData('image', $page_info['image'], 'itemprop');


/**
 * Set Twitter metadata
 */
$doc->setMetaData('twitter:card', (isset($page_info['image']) ? 'summary_large_image' : 'summary'));

if(isset($page_info['twitter_site']) && $page_info['twitter_site']) {
    $doc->setMetaData('twitter:site', $page_info['twitter_site']);
}
$doc->setMetaData('twitter:title', $page_info['name']);
$doc->setMetaData('twitter:description', trim($page_info['description']));
if(isset($page_info['twitter_creator']) && $page_info['twitter_creator']) {
    $doc->setMetaData('twitter:creator', $page_info['twitter_creator']);
}
if(isset($page_info['image']) && $page_info['image']) {
    $doc->setMetaData('twitter:image:src', $page_info['image']);
}


/**
 * Set Facebook metadata
 */
$doc->setMetaData('og:title', $page_info['name'], 'property');
$doc->setMetaData('og:type', 'article', 'property');
$doc->setMetaData('og:url', $page_info['url'], 'property');
if(isset($page_info['image']) && $page_info['image']) {
    $doc->setMetaData('og:image', $page_info['image'], 'property');
}
$doc->setMetaData('og:description', trim($page_info['description']), 'property');
$doc->setMetaData('og:site_name', $page_info['site_name'], 'property');
$doc->setMetaData('article:published_time', '', 'property');
$doc->setMetaData('article:modified_time', '', 'property');
$doc->setMetaData('article:section', '', 'property');
if(isset($page_info['fb_admins']) && $page_info['fb_admins']) {
    $doc->setMetaData('fb:admins', $page_info['fb_admins'], 'property');
}
if(isset($page_info['fb_app_id']) && $page_info['fb_app_id']) {
    $doc->setMetaData('fb:fb_app_id', $page_info['fb_app_id'], 'property');
}