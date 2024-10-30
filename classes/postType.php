<?php
/**
 * Class PostType for creating a new post type
 */
class PostType {

	private $_postTYpe, $_singular, $_plural, $_slug, $_menu_icon;

	function __construct( $post_type, $options = array() )
	{
		//Extract optional values
		$singular = $plural = $slug = $menu_icon = $args = null;
		extract($options, EXTR_IF_EXISTS);
		// Set class properties
		$this->_postType = $post_type;
		$this->_singular = ( $singular ) ? $singular : ucfirst($this->_postType) ;
		$this->_plural = ( $plural ) ? $plural : $this->_singular.'s';
		$this->_slug = ( $slug ) ? $slug : strtolower($this->_plural);
		$this->_menu_icon = $menu_icon;
		$this->_args = $args;
		// Register post type
		add_action( 'init', array($this, 'register_post_type') );
	}


	function register_post_type()
	{
		// Default array of arguments for post type
			$defaults = array('labels' => array('name' => $this->_plural,
												'singular_name' => $this->_singular,
												'add_new' => __('Add new ', LOCATOR).' '.$this->_singular,
												'add_new_item' => __('Add new item ', LOCATOR). ' '.$this->_singular,
												'edit_item' => __('Edit ', LOCATOR).' '.$this->_singular,
												'new_item' => __('New ', LOCATOR).' '.$this->_singular,
												'view_item' => __('View ', LOCATOR).' '.$this->_singular,
												'search_items' => __('Search ', LOCATOR).' '.$this->_plural,
												'not_found' => sprintf( __('No %s found', LOCATOR), $this->_plural ),
												'not_found_in_trash' => sprintf( __('No %s found in Trash', LOCATOR), $this->_plural ) ),
							  'public' => true,
							  'has_archive' => true,
							  'rewrite' => array('slug' => $this->_slug)
							  //'menu_icon' => $this->_menu_icon
							  );

			// Merge default arguments with passed arguments
			$args = wp_parse_args( $this->_args, $defaults );
			// Register the post type
			register_post_type($this->_postType, $args);
	}

}