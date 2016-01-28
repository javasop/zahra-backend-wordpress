<?php

class Zahra_API {

    /**
     * Server object
     *
     * @var WP_JSON_ResponseHandler
     */
    protected $server;

    /**
     * Constructor
     *
     * @param WP_JSON_ResponseHandler $server Server object
     */
    //public function __construct(WP_JSON_ResponseHandler $server) {
    //	$this->server = $server;
    //}

    /**
     * Register the post-related routes
     *
     * @param array $routes Existing routes
     * @return array Modified routes
     */
    public function register_routes($routes) {
        $post_routes = array(
            // Post endpoints
            '/zahra/posts' => array(
                array(array($this, 'get_posts'), WP_JSON_Server::READABLE),
                array(array($this, 'new_post'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
            ),
            '/zahra/posts/(?P<id>\d+)' => array(
                array(array($this, 'get_post'), WP_JSON_Server::READABLE),
                array(array($this, 'edit_post'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON),
                array(array($this, 'delete_post'), WP_JSON_Server::DELETABLE),
            ),
            '/zahra/posts/(?P<id>\d+)/revisions' => array(
                array($this, 'get_revisions'), WP_JSON_Server::READABLE
            ),
            // Meta
            '/zahra/posts/(?P<id>\d+)/meta' => array(
                array(array($this, 'get_all_meta'), WP_JSON_Server::READABLE),
                array(array($this, 'add_meta'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
            ),
            '/zahra/posts/(?P<id>\d+)/meta/(?P<mid>\d+)' => array(
                array(array($this, 'get_meta'), WP_JSON_Server::READABLE),
                array(array($this, 'update_meta'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON),
                array(array($this, 'delete_meta'), WP_JSON_Server::DELETABLE),
            ),
            // Comments
            '/zahra/posts/(?P<id>\d+)/comments' => array(
                array(array($this, 'get_comments'), WP_JSON_Server::READABLE),
            ),
            '/zahra/posts/(?P<id>\d+)/comments/(?P<comment>\d+)' => array(
                array(array($this, 'get_comment'), WP_JSON_Server::READABLE),
                array(array($this, 'delete_comment'), WP_JSON_Server::DELETABLE),
            ),
            // Meta-post endpoints
            '/zahra/posts/types' => array(
                array($this, 'get_post_types'), WP_JSON_Server::READABLE
            ),
            '/zahra/posts/types/(?P<type>\w+)' => array(
                array($this, 'get_post_type'), WP_JSON_Server::READABLE
            ),
            '/zahra/posts/statuses' => array(
                array($this, 'get_post_statuses'), WP_JSON_Server::READABLE
            ),
        );
        return array_merge($routes, $post_routes);
    }

    /**
     * Get revisions for a specific post.
     *
     * @param int $id Post ID
     * @uses wp_get_post_revisions
     * @return WP_JSON_Response
     */
    public function get_revisions($id) {
        $id = (int) $id;

        $parent = get_post($id, ARRAY_A);

        if (empty($id) || empty($parent['ID'])) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        if (!$this->check_edit_permission($parent)) {
            return new WP_Error('json_cannot_view', __('Sorry, you cannot view the revisions for this post.'), array('status' => 403));
        }

        // Todo: Query args filter for wp_get_post_revisions
        $revisions = wp_get_post_revisions($id);

        $struct = array();
        foreach ($revisions as $revision) {
            $post = get_object_vars($revision);

            $struct[] = $this->prepare_post($post, 'view-revision');
        }

        return $struct;
    }

    /**
     * Retrieve posts.
     *
     * @since 3.4.0
     *
     * The optional $filter parameter modifies the query used to retrieve posts.
     * Accepted keys are 'post_type', 'post_status', 'number', 'offset',
     * 'orderby', 'taxonomy' and 'order'
     *
     * The optional $fields parameter specifies what fields will be included
     * in the response array.
     *
     * @uses wp_get_recent_posts()
     * @see WP_JSON_Posts::get_post() for more on $fields
     * @see get_posts() for more on $filter values
     *
     * @param array $filter Parameters to pass through to `WP_Query`
     * @param string $context
     * @param string|array $type Post type slug, or array of slugs
     * @param int $page Page number (1-indexed)
     * @return stdClass[] Collection of Post entities
     */
    public function get_posts($filter = array(), $context = 'view', $type = 'post', $page = 1, $tax = NULL) {

        $query = array();
        $args = array();


        // Validate post types and permissions
        $query['post_type'] = array();

        foreach ((array) $type as $type_name) {

            $post_type = get_post_type_object($type_name);
            if (!( (bool) $post_type ) || !$post_type->show_in_json) {
                return new WP_Error('json_invalid_post_type', sprintf(__('The post type "%s" is not valid'), $type_name), array('status' => 403));
            }

            $query['post_type'] = $post_type->name;
            $args['post_type'] = $post_type->name;
        }



        global $wp;

        // Allow the same as normal WP
        $valid_vars = apply_filters('query_vars', $wp->public_query_vars);

        // If the user has the correct permissions, also allow use of internal
        // query parameters, which are only undesirable on the frontend
        //
		// To disable anyway, use `add_filter('json_private_query_vars', '__return_empty_array');`

        if (current_user_can($post_type->cap->edit_posts)) {
            $private = apply_filters('json_private_query_vars', $wp->private_query_vars);
            $valid_vars = array_merge($valid_vars, $private);
        }

        // Define our own in addition to WP's normal vars
        $json_valid = array('posts_per_page');
        $valid_vars = array_merge($valid_vars, $json_valid);

        // Filter and flip for querying
        $valid_vars = apply_filters('json_query_vars', $valid_vars);
        $valid_vars = array_flip($valid_vars);

        // Exclude the post_type query var to avoid dodging the permission
        // check above
        unset($valid_vars['post_type']);

        foreach ($valid_vars as $var => $index) {
            if (isset($filter[$var])) {
                $query[$var] = apply_filters('json_query_var-' . $var, $filter[$var]);
            }
        }

        //get the taxonomy query to pass to WP Query ...
        if ($tax != NULL) {
            $key = array_keys($tax)[0];
            $term = $tax[$key];
            $args = array(
                'tax_query' => array(
                    array(
                        'taxonomy' => $key,
                        'field' => 'slug',
                        'terms' => $term
                    )
                )
            );

            $args = array_merge($args, $query);
        }


        // Special parameter handling
        //$query['paged'] = absint( $page );
        $post_query = new WP_Query();
        $posts_list = $post_query->query($args);
        $response = new WP_JSON_Response();
        $response->query_navigation_headers($post_query);

        if (!$posts_list) {
            $response->set_data(array());
            return $response;
        }

        // holds all the posts data
        $struct = array();

        $response->header('Last-Modified', mysql2date('D, d M Y H:i:s', get_lastpostmodified('GMT'), 0) . ' GMT');

        foreach ($posts_list as $post) {
            $post = get_object_vars($post);

            // Do we have permission to read this post?
            if (!$this->check_read_permission($post)) {
                continue;
            }

            $response->link_header('item', json_url('/posts/' . $post['ID']), array('title' => $post['post_title']));
            $post_data = $this->prepare_post($post, $context);
            if (is_wp_error($post_data)) {
                continue;
            }

            $struct[] = $post_data;
        }
        $response->set_data($struct);

        return $response;
    }

    /**
     * Check if we can read a post
     *
     * Correctly handles posts with the inherit status.
     * @param array $post Post data
     * @return boolean Can we read it?
     */
    protected function check_read_permission($post) {
        $post_type = get_post_type_object($post['post_type']);

        // Ensure the post type can be read
        if (!$post_type->show_in_json) {
            return false;
        }

        // Can we read the post?
        if ('publish' === $post['post_status'] || current_user_can($post_type->cap->read_post, $post['ID'])) {
            return true;
        }

        // Can we read the parent if we're inheriting?
        if ('inherit' === $post['post_status'] && $post['post_parent'] > 0) {
            $parent = get_post($post['post_parent'], ARRAY_A);

            if ($this->check_read_permission($parent)) {
                return true;
            }
        }

        // If we don't have a parent, but the status is set to inherit, assume
        // it's published (as per get_post_status())
        if ('inherit' === $post['post_status']) {
            return true;
        }

        return false;
    }

    /**
     * Check if we can edit a post
     * @param array $post Post data
     * @return boolean Can we edit it?
     */
    protected function check_edit_permission($post) {
        $post_type = get_post_type_object($post['post_type']);

        if (!current_user_can($post_type->cap->edit_post, $post['ID'])) {
            return false;
        }

        return true;
    }

    /**
     * Create a new post for any registered post type.
     *
     * @since 3.4.0
     * @internal 'data' is used here rather than 'content', as get_default_post_to_edit uses $_REQUEST['content']
     *
     * @param array $content Content data. Can contain:
     *  - post_type (default: 'post')
     *  - post_status (default: 'draft')
     *  - post_title
     *  - post_author
     *  - post_excerpt
     *  - post_content
     *  - post_date_gmt | post_date
     *  - post_format
     *  - post_password
     *  - comment_status - can be 'open' | 'closed'
     *  - ping_status - can be 'open' | 'closed'
     *  - sticky
     *  - post_thumbnail - ID of a media item to use as the post thumbnail/featured image
     *  - custom_fields - array, with each element containing 'key' and 'value'
     *  - terms - array, with taxonomy names as keys and arrays of term IDs as values
     *  - terms_names - array, with taxonomy names as keys and arrays of term names as values
     *  - enclosure
     *  - any other fields supported by wp_insert_post()
     * @return array Post data (see {@see WP_JSON_Posts::get_post})
     */
    public function new_post($data) {
        unset($data['ID']);

        $result = $this->insert_post($data);
        if ($result instanceof WP_Error) {
            return $result;
        }

        $response = json_ensure_response($this->get_post($result));
        $response->set_status(201);
        $response->header('Location', json_url('/posts/' . $result));

        return $response;
    }

    /**
     * Retrieve a post.
     *
     * @uses get_post()
     * @param int $id Post ID
     * @param array $fields Post fields to return (optional)
     * @return array Post entity
     */
    public function get_post($id, $context = 'view') {
        $id = (int) $id;

        if (empty($id)) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        $post = get_post($id, ARRAY_A);

        if (empty($post['ID'])) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        if (!$this->check_read_permission($post)) {
            return new WP_Error('json_user_cannot_read', __('Sorry, you cannot read this post.'), array('status' => 401));
        }

        // Link headers (see RFC 5988)

        $response = new WP_JSON_Response();
        $response->header('Last-Modified', mysql2date('D, d M Y H:i:s', $post['post_modified_gmt']) . 'GMT');

        $post = $this->prepare_post($post, $context);

        if (is_wp_error($post)) {
            return $post;
        }
        /**
          foreach ( $post['meta']['links'] as $rel => $url ) {
          $response->link_header( $rel, $url );
          }
         * */
        $response->link_header('alternate', get_permalink($id), array('type' => 'text/html'));
        $response->set_data($post);

        return $response;
    }

    /**
     * Edit a post for any registered post type.
     *
     * The $data parameter only needs to contain fields that should be changed.
     * All other fields will retain their existing values.
     *
     * @since 3.4.0
     * @internal 'data' is used here rather than 'content', as get_default_post_to_edit uses $_REQUEST['content']
     *
     * @param int $id Post ID to edit
     * @param array $data Data construct, see {@see WP_JSON_Posts::new_post}
     * @param array $_headers Header data
     * @return true on success
     */
    public function edit_post($id, $data, $_headers = array()) {
        $id = (int) $id;

        if (empty($id)) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        $post = get_post($id, ARRAY_A);

        if (empty($post['ID'])) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        if (isset($_headers['IF_UNMODIFIED_SINCE'])) {
            // As mandated by RFC2616, we have to check all of RFC1123, RFC1036
            // and C's asctime() format (and ignore invalid headers)
            $formats = array(DateTime::RFC1123, DateTime::RFC1036, 'D M j H:i:s Y');

            foreach ($formats as $format) {
                $check = WP_JSON_DateTime::createFromFormat($format, $_headers['IF_UNMODIFIED_SINCE']);

                if ($check !== false) {
                    break;
                }
            }

            // If the post has been modified since the date provided, return an error.
            if ($check && mysql2date('U', $post['post_modified_gmt']) > $check->format('U')) {
                return new WP_Error('json_old_revision', __('There is a revision of this post that is more recent.'), array('status' => 412));
            }
        }

        $data['ID'] = $id;

        $retval = $this->insert_post($data);
        if (is_wp_error($retval)) {
            return $retval;
        }

        return $this->get_post($id);
    }

    /**
     * Delete a post for any registered post type
     *
     * @uses wp_delete_post()
     * @param int $id
     * @return true on success
     */
    public function delete_post($id, $force = false) {
        $id = (int) $id;

        if (empty($id)) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        $post = get_post($id, ARRAY_A);

        if (empty($post['ID'])) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        $post_type = get_post_type_object($post['post_type']);

        if (!current_user_can($post_type->cap->delete_post, $id)) {
            return new WP_Error('json_user_cannot_delete_post', __('Sorry, you are not allowed to delete this post.'), array('status' => 401));
        }

        $result = wp_delete_post($id, $force);

        if (!$result) {
            return new WP_Error('json_cannot_delete', __('The post cannot be deleted.'), array('status' => 500));
        }

        if ($force) {
            return array('message' => __('Permanently deleted post'));
        } else {
            // TODO: return a HTTP 202 here instead
            return array('message' => __('Deleted post'));
        }
    }

    /**
     * Delete a comment.
     *
     * @uses wp_delete_comment
     * @param int $id Post ID
     * @param int $comment Comment ID
     * @param boolean $force Skip trash
     * @return array
     */
    public function delete_comment($id, $comment, $force = false) {
        $comment = (int) $comment;

        if (empty($comment)) {
            return new WP_Error('json_comment_invalid_id', __('Invalid comment ID.'), array('status' => 404));
        }

        $comment_array = get_comment($comment, ARRAY_A);

        if (empty($comment_array)) {
            return new WP_Error('json_comment_invalid_id', __('Invalid comment ID.'), array('status' => 404));
        }

        if (!current_user_can('edit_comment', $comment_array['comment_ID'])) {
            return new WP_Error('json_user_cannot_delete_comment', __('Sorry, you are not allowed to delete this comment.'), array('status' => 401));
        }

        $result = wp_delete_comment($comment_array['comment_ID'], $force);

        if (!$result) {
            return new WP_Error('json_cannot_delete', __('The comment cannot be deleted.'), array('status' => 500));
        }

        if ($force) {
            return array('message' => __('Permanently deleted comment'));
        } else {
            // TODO: return a HTTP 202 here instead
            return array('message' => __('Deleted comment'));
        }
    }

    /**
     * Retrieve comments
     *
     * @param int $id Post ID to retrieve comments for
     * @return array List of Comment entities
     */
    public function get_comments($id) {
        //$args = array('status' => $status, 'post_id' => $id, 'offset' => $offset, 'number' => $number )l
        $comments = get_comments(array('post_id' => $id));

        $post = get_post($id, ARRAY_A);

        if (empty($post['ID'])) {
            return new WP_Error('json_post_invalid_id', __('Invalid post ID.'), array('status' => 404));
        }

        if (!$this->check_read_permission($post)) {
            return new WP_Error('json_user_cannot_read', __('Sorry, you cannot read this post.'), array('status' => 401));
        }

        $struct = array();

        foreach ($comments as $comment) {
            $struct[] = $this->prepare_comment($comment, array('comment', 'meta'), 'collection');
        }

        return $struct;
    }

    /**
     * Retrieve a single comment
     *
     * @param int $comment Comment ID
     * @return array Comment entity
     */
    public function get_comment($comment) {
        $comment = get_comment($comment);

        if (empty($comment)) {
            return new WP_Error('json_comment_invalid_id', __('Invalid comment ID.'), array('status' => 404));
        }

        $data = $this->prepare_comment($comment);

        return $data;
    }

    /**
     * Get all public post types
     *
     * @uses self::get_post_type()
     * @return array List of post type data
     */
    public function get_post_types() {

        $data = get_post_types(array(), 'objects');

        $types = array();

        foreach ($data as $name => $type) {
            $type = $this->get_post_type($type, true);
            if (is_wp_error($type)) {
                continue;
            }

            $types[$name] = $type;
        }

        return $types;
    }

    /**
     * Get a post type
     *
     * @param string|object $type Type name, or type object (internal use)
     * @param boolean $context What context are we in?
     * @return array Post type data
     */
    public function get_post_type($type, $context = 'view') {
        if (!is_object($type)) {
            $type = get_post_type_object($type);
        }

        if ($type->show_in_json === false) {
            return new WP_Error('json_cannot_read_type', __('Cannot view post type'), array('status' => 403));
        }

        if ($context === true) {
            $context = 'embed';
            _deprecated_argument(__CLASS__ . '::' . __FUNCTION__, 'WPAPI-1.1', '$context should be set to "embed" rather than true');
        }

        $data = array(
            'name' => $type->label,
            'slug' => $type->name,
            'description' => $type->description,
            'labels' => $type->labels,
            'queryable' => $type->publicly_queryable,
            'searchable' => !$type->exclude_from_search,
            'hierarchical' => $type->hierarchical,
            'meta' => array(
                'links' => array(
                    'self' => json_url('/posts/types/' . $type->name),
                    'collection' => json_url('/posts/types'),
                ),
            ),
        );

        // Add taxonomy link
        $relation = 'http://wp-api.org/1.1/collections/taxonomy/';
        $url = json_url('/taxonomies');
        $url = add_query_arg('type', $type->name, $url);
        $data['meta']['links'][$relation] = $url;

        if ($type->publicly_queryable) {
            if ($type->name === 'post') {
                $data['meta']['links']['archives'] = json_url('/posts');
            } else {
                $data['meta']['links']['archives'] = json_url(add_query_arg('type', $type->name, '/posts'));
            }
        }

        return apply_filters('json_post_type_data', $data, $type, $context);
    }

    /**
     * Get the registered post statuses
     *
     * @return array List of post status data
     */
    public function get_post_statuses() {
        $statuses = get_post_stati(array(), 'objects');

        $data = array();

        foreach ($statuses as $status) {
            if ($status->internal === true || !$status->show_in_admin_status_list) {
                continue;
            }

            $data[$status->name] = array(
                'name' => $status->label,
                'slug' => $status->name,
                'public' => $status->public,
                'protected' => $status->protected,
                'private' => $status->private,
                'queryable' => $status->publicly_queryable,
                'show_in_list' => $status->show_in_admin_all_list,
                'meta' => array(
                    'links' => array()
                ),
            );
            if ($status->publicly_queryable) {
                if ($status->name === 'publish') {
                    $data[$status->name]['meta']['links']['archives'] = json_url('/posts');
                } else {
                    $data[$status->name]['meta']['links']['archives'] = json_url(add_query_arg('status', $status->name, '/posts'));
                }
            }
        }

        return apply_filters('json_post_statuses', $data, $statuses);
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

        // holds the data for this post. built up based on $fields
        $_post = array('ID' => (int) $post['ID']);

        $post_type = get_post_type_object($post['post_type']);

        $previous_post = null;
        if (!empty($GLOBALS['post'])) {
            $previous_post = $GLOBALS['post'];
        }

        $post_obj = get_post($post['ID']);


        $GLOBALS['post'] = $post_obj;
        setup_postdata($post_obj);


        // Thumbnail
        $thumbnail_id = get_post_thumbnail_id($post['ID']);

        if ($thumbnail_id) {
            $featured = get_post($thumbnail_id, 'ARRAY_A')["guid"];
        }

        $post_fields = array(
            'title' => get_the_title($post['ID']), // $post['post_title'],
            'type' => $post['post_type'],
            'author' => (int) $post['post_author'],
            'content' => apply_filters('the_content', $post['post_content']),
            'description' => $post['post_excerpt'],
            #'post_mime_type' => $post['post_mime_type'],
            'link' => get_permalink($post['ID']),
            'custom_meta' => get_post_custom($post['ID']),
            'featured' => $featured
        );
//
//		$post_fields_extended = array(
//			'slug'           => $post['post_name'],
//			'guid'           => apply_filters( 'get_the_guid', $post['guid'] ),
//			'menu_order'     => (int) $post['menu_order'],
//			'comment_status' => $post['comment_status'],
//			'ping_status'    => $post['ping_status'],
//			'sticky'         => ( $post['post_type'] === 'post' && is_sticky( $post['ID'] ) ),
//		);
//
//		$post_fields_raw = array(
//			'title_raw'   => $post['post_title'],
//			'content_raw' => $post['post_content'],
//			'excerpt_raw' => $post['post_excerpt'],
//			'guid_raw'    => $post['guid']
//		);
        // Dates
        $timezone = json_get_timezone();


        if ($post['post_date_gmt'] === '0000-00-00 00:00:00') {
            $post_fields['date'] = null;
            $post_fields_extended['date_tz'] = null;
            $post_fields_extended['date_gmt'] = null;
        } else {
            $date = WP_JSON_DateTime::createFromFormat('Y-m-d H:i:s', $post['post_date'], $timezone);
            $post_fields['date'] = $date->format('c');
            $post_fields_extended['date_tz'] = $date->format('e');
            $post_fields_extended['date_gmt'] = date('c', strtotime($post['post_date_gmt']));
        }

        if ($post['post_modified_gmt'] === '0000-00-00 00:00:00') {
            $post_fields['modified'] = null;
            $post_fields_extended['modified_tz'] = null;
            $post_fields_extended['modified_gmt'] = null;
        } else {
            $modified = WP_JSON_DateTime::createFromFormat('Y-m-d H:i:s', $post['post_modified'], $timezone);
            $post_fields['modified'] = $modified->format('c');
            $post_fields_extended['modified_tz'] = $modified->format('e');
            $post_fields_extended['modified_gmt'] = date('c', strtotime($post['post_modified_gmt']));
        }

        // Authorized fields
        // TODO: Send `Vary: Authorization` to clarify that the data can be
        // changed by the user's auth status
        if (current_user_can($post_type->cap->edit_post, $post['ID'])) {
            $post_fields_extended['password'] = $post['post_password'];
        }

        // Consider future posts as published
        if ($post_fields['status'] === 'future') {
            $post_fields['status'] = 'publish';
        }

        // Fill in blank post format
        $post_fields['format'] = get_post_format($post['ID']);

        if (empty($post_fields['format'])) {
            $post_fields['format'] = 'standard';
        }

        if (( 'view' === $context || 'view-revision' == $context ) && 0 !== $post['post_parent']) {
            // Avoid nesting too deeply
            // This gives post + post-extended + meta for the main post,
            // post + meta for the parent and just meta for the grandparent
            $parent = get_post($post['post_parent'], ARRAY_A);
            $post_fields['parent'] = $this->prepare_post($parent, 'embed');
        }

        // Merge requested $post_fields fields into $_post
        $_post = array_merge($_post, $post_fields);

        // Include extended fields. We might come back to this.
        $_post = array_merge($_post, $post_fields_extended);

        if ('edit' === $context) {
            if (current_user_can($post_type->cap->edit_post, $post['ID'])) {
                if (is_wp_error($post_fields_raw['post_meta'])) {
                    $GLOBALS['post'] = $previous_post;
                    if ($previous_post) {
                        setup_postdata($previous_post);
                    }
                    return $post_fields_raw['post_meta'];
                }

                $_post = array_merge($_post, $post_fields_raw);
            } else {
                $GLOBALS['post'] = $previous_post;
                if ($previous_post) {
                    setup_postdata($previous_post);
                }
                return new WP_Error('json_cannot_edit', __('Sorry, you cannot edit this post'), array('status' => 403));
            }
        } elseif ('view-revision' == $context) {
            if (current_user_can($post_type->cap->edit_post, $post['ID'])) {
                $_post = array_merge($_post, $post_fields_raw);
            } else {
                $GLOBALS['post'] = $previous_post;
                if ($previous_post) {
                    setup_postdata($previous_post);
                }
                return new WP_Error('json_cannot_view', __('Sorry, you cannot view this revision'), array('status' => 403));
            }
        }

        // Entity meta
//		$links = array(
//			'self'       => json_url( '/posts/' . $post['ID'] ),
//			'author'     => json_url( '/users/' . $post['post_author'] ),
//			'collection' => json_url( '/posts' ),
//		);
//
//		if ( 'view-revision' != $context ) {
//			$links['replies'] = json_url( '/posts/' . $post['ID'] . '/comments' );
//			$links['version-history'] = json_url( '/posts/' . $post['ID'] . '/revisions' );
//		}
//		$_post['meta'] = array( 'links' => $links );
//		if ( ! empty( $post['post_parent'] ) ) {
//			$_post['meta']['links']['up'] = json_url( '/posts/' . (int) $post['post_parent'] );
//		}

        $GLOBALS['post'] = $previous_post;
        if ($previous_post) {
            setup_postdata($previous_post);
        }
        return apply_filters('json_prepare_zahra', $_post, $post, $context);
    }

    //remove custom metas that aren't needed and return the result
    public function filterMeta($meta, $exception) {

        $nmeta = array();

        foreach ($meta as $k => $v):

            //if the element is in the exception
            if (in_array($k, $exception)) {
                
                is_array($v)? $nmeta[$k] = $v[0]:$nmeta[$k] = $v;
                
            }

        endforeach;

        return $nmeta;
    }

}
