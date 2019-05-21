<?php
class Ninjastars_Custom_Post_Type {
	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

    public function ninjastars_post_type() {
        $labels = array(
            'name'                  => 'NS Reviews',
            'singular_name'         => 'Review',
            'add_new'               => 'Add New Review',
            'add_new_item'          => 'Add New Review',
            'edit_item'             => 'Edit Review',
            'view_item'             => 'View Review',
            'search_item'           => 'Search Reviews',
            'not_found'             => 'No Reviews Found',
            'not_found_in_trash'    => 'No Reviews Found in Trash'
            );

        $args = array (
            'labels'    => $labels,
            'menu_icon'             => 'dashicons-star-filled',
            'public'                => true,
            'has_archive'           => true,
            'supports'              => array('title','thumbnail', 'page-attributes'),
            'description'           => 'These projects appear as an organized list in whatever page you designate.',
            'show_in_nav_menus'     => false,
            'show_in_menu'          => true,
            'menu_position'         => 40,
            'exclude_from_search'   => true,
            'show_ui'               => true,
            'taxonomies'            => array( 'category' ),
            );

        register_post_type('ninjastars', $args);
    }	
}