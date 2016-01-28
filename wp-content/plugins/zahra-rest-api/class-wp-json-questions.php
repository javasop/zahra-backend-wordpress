<?php

class Question extends Zahra_API {

    public function register_routes($routes) {
        $routes['/zahra/questions'] = array(
            array(array($this, 'get_posts'), WP_JSON_Server::READABLE),
            array(array($this, 'ask'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
        );
        return $routes;
    }

    public function get_posts($filter = array(), $context = 'view', $type = 'questions', $page = 1) {

        $posts = parent::get_posts($filter, $context, 'questions', $page);

        return $posts;
    }

    public function ask($data) {

        $post = array(
            'post_name' => $data['title'], // The name (slug) for your post
            'post_title' => $data['title'], // The title of your post.
            'post_status' => 'draft',
            'post_type' => 'questions'
        );

        wp_insert_post($post, $wp_error);

        $response = new WP_JSON_Response();

        $response->set_status(201);

        return $response;
    }

}

?>