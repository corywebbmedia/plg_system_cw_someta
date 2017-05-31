<?php
/**
 * @copyright   Copyright (C) 2016 Cory Webb Media, LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! SEF Plugin.
 *
 * @since  1.5
 */
class PlgSystemCw_someta extends JPlugin
{
	/**
	 * Add the social meta tags to the head
	 *
	 * @return  void
	 */
	public function onBeforeCompileHead()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if ($app->getName() != 'site' || $doc->getType() !== 'html' || $app->input->get('tmpl') == 'component')
		{
			return;
		}
		
		$page_info = $this->getPageInfo();

		if($page_info) {

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

			$doc->setMetaData('og:title', $page_info['name']);
			$doc->setMetaData('og:type', 'article');
			$doc->setMetaData('og:url', $page_info['url']);
			if(isset($page_info['image']) && $page_info['image']) {
				$doc->setMetaData('og:image', $page_info['image']);
			}
			$doc->setMetaData('og:description', trim($page_info['description']));
			$doc->setMetaData('og:site_name', $page_info['site_name']);
			$doc->setMetaData('article:published_time', '');
			$doc->setMetaData('article:modified_time', '');
			$doc->setMetaData('article:section', '');
			if(isset($page_info['fb_admins']) && $page_info['fb_admins']) {
				$doc->setMetaData('fb:admins', $page_info['fb_admins']);
			}
			if(isset($page_info['fb_app_id']) && $page_info['fb_app_id']) {
				$doc->setMetaData('fb:fb_app_id', $page_info['fb_app_id']);
			}

		}

	}

	protected function getPageInfo()
	{

		// Initialize the page info array
		$page_info = array();

		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$config = JFactory::getConfig();
		$menu_item = $app->getMenu()->getActive();

		$option = $app->input->get('option', '');
		$view = $app->input->get('view', '');
		$id = $app->input->getInt('id', 0);

		if($option == 'com_content')
		{
			switch ($view) {
				case 'article':
					$page_info = $this->getArticleInfo($id);
					break;

				case 'categories':
					if($id) {
						$page_info = $this->getCategoryInfo($id);						
					} else {
						$page_info = $this->getCategoriesInfo($menu_item);
					}
					break;

				case 'category':
					$page_info = $this->getCategoryInfo($id);
					break;

				case 'featured':
					$page_info = $this->getMenuItemInfo($menu_item);
					break;
			}
		}
		elseif ($option == 'com_tags')
		{
			switch ($view) {
				case 'tag':
					if(is_array($id) && count($id > 1)) {
						$page_info = $this->getMenuItemInfo($menu_item);
					} else {
						$page_info = $this->getTagInfo($id);
					}
					break;

				case 'tags':
					$page_info = $this->getTagsInfo($menu_item);
					break;
			}
		} else {
			$page_info = $this->getMenuItemInfo($menu_item);
		}

		$page_info['type'] = 'article';
		$page_info['url'] = JURI::current();
		if(!isset($page_info['image'])) {
			$page_info['image'] = $this->params->get('default_image', '');
			if($page_info['image']) {
				$page_info['image'] = JURI::base() . $page_info['image'];
			}
		}
		if(!isset($page_info['description']) || !$page_info['description']) {
			$page_info['description'] = $config->get('MetaDesc');
		}
		$page_info['twitter_site'] = $this->params->get('twitter_site', '');
		$page_info['twitter_creator'] = $this->params->get('twitter_creator', '');
		$page_info['fb_app_id'] = $this->params->get('fb_app_id', '');
		$page_info['fb_admins'] = $this->params->get('fb_admins', '');
		$page_info['site_name'] = $config->get('sitename');
		if(!isset($page_info['name']) || !$page_info['name']) {
			$page_info['name'] = $page_info['site_name'];
		}

		return $page_info;

	}

	private function getArticleInfo($article_id)
	{
		$article_info = array();

		$article = $this->getArticle($article_id);

		$article_info['name'] = htmlspecialchars($article->title);
		$article_info['description'] = strip_tags($article->introtext);
		$images = json_decode($article->images);

		// Set image only if this article has a fulltext image.
		if ($images AND $images->image_fulltext)
		{
			$article_info['image'] = JURI::base() . $images->image_fulltext;
		}

		return $article_info;

	}

	private function getArticle($article_id)
	{
		$article = JTable::getInstance("content");
		$article->load($article_id);

		return $article;
	}

	private function getCategoryInfo($category_id) {

		$category_info = array();

		$category = $this->getCategory($category_id);

		$category_info['name'] = htmlspecialchars($category->title);
		$category_info['description'] = strip_tags($category->description);
		$params = json_decode($category->params);

		if($params->image) {
			$category_info['image'] = JURI::base() . $params->image;
		}

		return $category_info;


	}

	private function getCategory($category_id)
	{
		$category = JTable::getInstance("category");
		$category->load($category_id);

		return $category;
	}

	private function getCategoriesInfo($menu_item)
	{
		$categories_info = array();

		$categories_info['name'] = htmlspecialchars($menu_item->title);
		$categories_info['description'] = strip_tags($menu_item->params->get('categories_description'));

		return $categories_info;

	}

	private function getTagInfo($tag_id)
	{
		$tag_info = array();

		$tag = $this->getTag($tag_id);

		$tag_info['name'] = htmlspecialchars($tag->title);
		$tag_info['description'] = strip_tags($tag->description);
		$images = json_decode($tag->images);

		// Set image only if this tag has a fulltext image.
		if ($images && $images->image_fulltext) {
			$tag_info['image'] = JURI::base() . $images->image_fulltext;
		}

		return $tag_info;
	}

	private function getTag($tag_id)
	{
		if(is_array($tag_id)) {
			$id = $tag_id[0];
		} else {
			$id = $tag_id;
		}

		$tag = JTable::getInstance('Tag', 'TagsTable');
		$tag->load($id);

		return $tag;
	}

	private function getTagsInfo($menu_item)
	{
		$app = JFactory::getApplication();
		$tags_info = array();

		if($app->input->getInt('parent_id', 0)) {

			$tags_info = $this->getTagInfo($app->input->getInt('parent_id', 0));

		} else {

			$tags_info = $this->getMenuItemInfo($menu_item);

			if($menu_item->all_tags_description) {
				$tags_info['description'] = $menu_item->all_tags_description;
			}
			if($menu_item->all_tags_description_image) {
				$tags_info['image'] = JURI::base() . $menu_item->all_tags_description_image;
			}

		}

		return $tags_info;
	}

	private function getMenuItemInfo($menu_item)
	{
		$info = array();

		if($menu_item) {
			if($menu_item->params->get('page_heading', '') != '') {
				$info['name'] = $menu_item->params->get('page_heading', '');
			} elseif($menu_item->params->get('page_title', '') != '') {
				$info['name'] = $menu_item->params->get('page_title', '');
			} else {
				$info['name'] = htmlspecialchars($menu_item->title);
			}
			$info['description'] = strip_tags($menu_item->params->get('menu-meta_description'));
		}

		return $info;

	}

}


/*
 <!-- Update your html tag to include the itemscope and itemtype attributes. -->
<html itemscope itemtype="http://schema.org/Article">

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