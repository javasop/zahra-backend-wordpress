<?php
/**
 * Plugin Name: ZAHRA REST API
 * Description: JSON-based REST API for WordPress, developed as part of GSoC 2013.
 * Author: Osamah alghanmi
 * Author URI: http://itechdom.com
 * Version: 1.1.1
 * Plugin URI: https://github.com/rmccue/WP-API
 */

//the main class, derived from WP_Posts from the json-rest-api plugin
require_once "class-wp-json-zahra.php";
require_once "class-wp-json-hospital.php";
require_once "class-wp-json-stories.php";
require_once "class-wp-json-products.php";
require_once "class-wp-json-events.php";
require_once "class-wp-json-booklets.php";
require_once "class-wp-json-brochures.php";
require_once "class-wp-json-questions.php";
require_once "class-wp-json-zahra-users.php";
require_once "class-wp-json-zahra-forms.php";




function zahra_api_init() {

    global $zahra_api_hospital;
    
    $zahra_api = new Zahra_API();
    $hospital = new Hospital();
    $story = new Story();
    $product = new Product();
    $event = new Event();
    $booklet = new Booklet();
    $brochure = new Brochure();
    $question = new Question();
    $members = new Zahra_Users();
    $forms = new Form();




    add_filter( 'json_endpoints', array( $zahra_api, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $hospital, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $story, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $product, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $event, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $booklet, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $brochure, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $question, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $members, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $forms, 'register_routes' ) );




    //add_filter( 'json_prepare_post',    array( $zahra_api_hospital, 'add_hospital_data' ), 10, 3 );

}
add_action( 'wp_json_server_before_serve', 'zahra_api_init' );



?>