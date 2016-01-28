<?php

class Brochure extends Zahra_API {

    public function register_routes($routes) {
        
        $routes['/zahra/brochures'] = array(
            array(array($this, 'get_posts'), WP_JSON_Server::READABLE)
                //array( array( $this, 'new_post'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        /**
          $routes['/zahra/hospitals/(?P<id>\d+)'] = array(
          array( array( $this, 'get_post'), WP_JSON_Server::READABLE ),
          array( array( $this, 'edit_post'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
          array( array( $this, 'delete_post'), WP_JSON_Server::DELETABLE ),
          );
         * */
        return $routes;
    }

    public function get_posts($filter = array(), $context = 'view', $type = 'brochures', $page = 1) {

        $posts = parent::get_posts($filter, $context, 'brochures', $page);

        return $posts;
    }

    /**
     * Prepares post data for return in an XML-RPC object.
     *
     * @access protected
     *
     * @param array $post The unprepared post data
     * @param string $context The context for the prepared post. (view|view-revision|edit|embed)
     * @return array The prepared post data
     */
    protected function prepare_post($post, $context = 'view') {

        $data = parent::prepare_post($post, $context);

        unset($data['content']);

        //add product media
        $media = get_attached_media('APPLICATION/PDF', $post['ID']);
        foreach ($media as $k => $v):
            $data['media'] = $v->guid;
        endforeach;


        return apply_filters('json_prepare_brochure', $data, $post, $context);
    }

}

?>