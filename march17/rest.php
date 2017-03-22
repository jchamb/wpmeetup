<?php

/**
 * Routes
 */

// Base: wp-json
// Prefix: wp | custom
// version: v2 | custom
// Endpoint: post | dog | etc









/**
 * Registering Custom Routes
 * @var [type]
 */
add_action( 'rest_api_init', function () {

	/**
	 * Register the route
	 * @var namespace
	 * @var route
	 */
	register_rest_route( 'mynamespace/v1', '/posts/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => function( WP_REST_Request $request ) {
			$params = $request->get_params();
			$post = get_post($params['id']);

			if ( empty( $post ) ) {
				return new WP_Error( 'error', 'No post', array( 'status' => 404 ) );
			}

		  	return $post;
		},
		'args' => array(
			'id' => array(
				//'default' => 1,
				'required' => true,
				'validate_callback' => function($param, $request, $key) {
					return is_numeric( $param );
				},
				// 'sanitize_callback' => function($param, $request, $key) {
				// 	return (int) $param;
				// }
			),
		),
	));
});



add_action( 'rest_api_init', function(){

	register_rest_field( 'post', 'seo_meta', array(
		'get_callback' => function( $post ) {

			$seo = array();
			// title
			$title = get_post_meta($post['ID'], '_yoast_wpseo_title', true);
			if( empty($title) ) {
				$title = $post['title']['rendered'];
			}
			$seo['title'] = $title;

			// metadesc
			$metadesc = get_post_meta($post['ID'], '_yoast_wpseo_metadesc', true);
			if( empty($metadesc) ) {
				$metadesc =  str_replace('[&hellip;]', '', strip_tags($post['excerpt']['rendered']));
			}
			$seo['description'] = $metadesc;

			return $seo;
		},
		// 'update_callback' => function( $karma, $comment_obj ) {
        //     $ret = wp_update_comment( array(
        //         'comment_ID'    => $comment_obj->comment_ID,
        //         'comment_karma' => $karma
        //     ) );
        //     if ( false === $ret ) {
        //         return new WP_Error( 'rest_comment_karma_failed', __( 'Failed to update comment karma.' ), array( 'status' => 500 ) );
        //     }
        //     return true;
        // },
        // 'schema' => array(
        //     'description' => __( 'Comment karma.' ),
        //     'type'        => 'integer'
        // ),
	));

});


/**
 * Register custom post type/taxonomy, with REST API support
 *
 */
add_action( 'init', function() {
	$args = array(
		'public'                => true,
		'show_in_rest'          => true,
		'rest_base'             => 'todos',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'label'                 => 'Todo',
		'supports'              => array('title', 'custom-fields')
    );
    register_post_type( 'todo', $args );

	$labels = array(
      'name'              => _x( 'Categories', 'taxonomy general name' ),
      'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
      'search_items'      => __( 'Search Categories' ),
      'all_items'         => __( 'All Categories' ),
      'parent_item'       => __( 'Parent Category' ),
      'parent_item_colon' => __( 'Parent Category:' ),
      'edit_item'         => __( 'Edit Category' ),
      'update_item'       => __( 'Update Category' ),
      'add_new_item'      => __( 'Add New Category' ),
      'new_item_name'     => __( 'New Category Name' ),
      'menu_name'         => __( 'Category' ),
    );

    $args = array(
      'hierarchical'          => true,
      'labels'                => $labels,
      'show_ui'               => true,
      'show_admin_column'     => true,
      'query_var'             => true,
      'rewrite'               => array( 'slug' => 'todos' ),
      'show_in_rest'          => true,
      'rest_base'             => 'todo-categories',
      'rest_controller_class' => 'WP_REST_Terms_Controller',
    );

    register_taxonomy( 'todo_cat', array( 'todo' ), $args );
});



add_action( 'rest_api_init', function(){

	register_rest_field( 'todo', 'completed', array(
		'get_callback' => 'get_todo_completed',
		'update_callback' => 'update_todo_completed',
        'schema' => array(
            'description' => __( 'Todo Completed' ),
            'type'        => 'integer'
        ),
	));

});

function get_todo_completed($todo) {

	return (int) get_post_meta($todo['id'], 'completed', true);
}

function update_todo_completed( $value, $todo ) {

	$result = update_post_meta($todo->ID, $value, true);

	if ( false === $ret ) {
		return new WP_Error( 'todoerror', __( 'Failed to update completed' ), array( 'status' => 500 ) );
	}

	return true;
}
