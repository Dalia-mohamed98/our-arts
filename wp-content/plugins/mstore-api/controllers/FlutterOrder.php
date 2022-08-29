<?php

class CUSTOM_WC_REST_Orders_Controller extends WC_REST_Orders_Controller
{

    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'api/flutter_order';

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function __construct()
    {
		add_action('rest_api_init', array($this, 'register_flutter_woo_routes'));
    }

    public function register_flutter_woo_routes()
    {
        register_rest_route( $this->namespace,  '/create', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_item' ),
                'permission_callback' => array( $this, 'custom_create_item_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ));

        register_rest_route(
			$this->namespace,
			'/update' . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'custom_create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
    }

    function custom_create_item_permissions_check($request){
        $cookie = $request->get_header("User-Cookie");
        if (isset($cookie) && $cookie != null) {
            $user_id = wp_validate_auth_cookie($cookie, 'logged_in');
            if (!$user_id) {
                return false;
            }
            $body = $request->get_body();
            $json = file_get_contents('php://input');
            $params = json_decode($json, TRUE);
            $params["customer_id"] = $user_id;
            $request->set_body_params($params);
            return true;
        }else{
            return false;
        }
        
    }
}
new CUSTOM_WC_REST_Orders_Controller();