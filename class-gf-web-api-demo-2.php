<?php

GFForms::include_addon_framework();

class GF_Web_Api_Demo_2 extends GFAddOn {

	protected $_version = '1.0';
	protected $_min_gravityforms_version = '1.9';
	protected $_slug = 'gravityformswebapidemo2';
	protected $_full_path = __FILE__;
	protected $_url = 'http://www.gravityforms.com';
	protected $_title = 'Gravity Forms Web API Demo';
	protected $_short_title = 'Web API Demo 2';

	private static $_instance = null;

	private $_form_id = null;

	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GF_Web_Api_Demo_2();
		}

		return self::$_instance;
	}

	function scripts() {

		$all_scripts = parent::scripts();

		$settings = $this->get_plugin_settings();
		if ( empty( $settings ) ){
			return $all_scripts;
		}

		$this->_form_id = get_option( 'gf_api_demo_2_form_id' );


		if ( $this->_form_id ) {
			$scripts = array(
				array(
					'handle'  => 'gf_web_api_demo_2',
					'src'     => $this->get_base_url() . '/js/gf-web-api-demo-2.js',
					'deps'    => array( 'jquery' ),
					'version' => $this->_version,
					'enqueue' => array(
						array( 'query' => 'page=gravityformswebapidemo2' ),
					),
					'strings' => array(
						'root_url'    => $this->get_api_url(),
						'form_id'     => $this->_form_id,
						'signed_urls' => array(
							'get_entries' => $this->get_signed_url( 'GET', "forms/{$this->_form_id}/entries" ),
							'get_results' => $this->get_signed_url( 'GET', "forms/{$this->_form_id}/results" ),
						)
					)
				),
			);
			$all_scripts = array_merge( $all_scripts, $scripts );
		}

		return $all_scripts;
	}

	private function get_api_url(){
		return trailingslashit( $this->get_plugin_setting( 'api_url' ) ) . '/gravityformsapi/';
	}

	private function get_signed_url( $method, $route, $expires = '+12 hours' ) {

		$args_array['expires'] = strtotime( $expires );
		$args_array['api_key'] = $this->get_plugin_setting( 'public_key' );

		$args_array['signature'] = $this->sign( $method, $route, $expires );

		$api_url = $this->get_api_url();

		$url = add_query_arg( $args_array, $api_url . trailingslashit( $route ) );

		return $url;
	}

	function sign( $method, $route, $expires = '+12 hours' ) {
		$expires        = strtotime( $expires );
		$string_to_sign = sprintf( '%s:%s:%s:%s', $this->get_plugin_setting( 'public_key' ), strtoupper( $method ), $route, $expires );
		$hash           = hash_hmac( 'sha1', $string_to_sign, $this->get_plugin_setting( 'private_key' ), true );
		$sig            = rawurlencode( base64_encode( $hash ) );

		return $sig;
	}

	public function plugin_page() {

		$api_url = $this->get_api_url();

		if ( empty( $api_url ) ) {
			$settings_page = admin_url( 'admin.php?page=gf_settings&subview=gravityformswebapidemo2' );
			echo "Configure the api url, and keys on the <a href='{$settings_page}''>settings page</a>";
			return;
		}

		$sample_form_exists = ! empty( $this->_form_id );

		if ( isset( $_POST['_gf_create_form'] ) ) {
			check_admin_referer( 'gf_create_form', '_gf_create_form' );
			$post_forms_url = $this->get_signed_url( 'POST', 'forms' );
			$sample_form_json = file_get_contents( $this->get_base_path() . '/sample-form.json' );
			$options = array(
				'method'  => 'POST',
				'body'    => $sample_form_json,
			);

			$response = wp_remote_request( $post_forms_url, $options );
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$body_json = wp_remote_retrieve_body( $response );
				$body = json_decode( $body_json );
				$form_ids = $body->response;
				$form_id = $form_ids[0];
				if ( $form_id > 1 ){
					update_option( 'gf_api_demo_2_form_id' , $form_id );
					$this->_form_id = $form_id;
				}
			}
		} else {
			?>
			<form id="demo_step_1" method="POST">
				<?php wp_nonce_field( 'gf_create_form', '_gf_create_form' ); ?>
				<button id="create_form_button" class="button button-primary button-large">Create New Form</button>
			</form>
			<?php
			if ( ! $sample_form_exists ) {
				return;
			}
		}

		?>
		<div id="demo_step_2" >
			<div>
				Form ID: <input id="form_id" type="text" value="<?php echo $this->_form_id ?>"/>
			</div>

			Submit Form
			<form id="gf_web_api_demo_form">
				<input id="input_1" name="input_1" type="text" placeholder="Name"/><br/>
				<input id="input_2" name="input_2" type="text" placeholder="Email"/><br/>
				<input id="input_3_1" type="radio" name="input_3" value="Information request"/>
				<label for="input_3_1">I'd like further information about a product</label><br/>
				<input id="input_3_2" type="radio" class="input_3" name="input_3" value="Complaint"/>
				<label for="input_3_2">I wish to make a complaint</label><br/>
				<input id="input_3_3" type="radio" class="input_3" name="input_3" value="Commercial offer"/>
				<label for="input_3_3">I'm going to try to sell you something</label><br/>
				<input id="input_3_4" type="radio" class="input_3" name="input_3" value="Just saying hello"/>
				<label for="input_3_4">I'm an old friend</label><br/>
				<label for="input_4">Message</label><br/>
				<textarea id="input_4" name="input_4"></textarea>
			</form>
			<div>

				<button id="submit_button" class="button button-primary button-large">Submit Form</button>

				<button id="get_entries_button" class="button button-primary button-large">Show Latest Entries
				</button>

				<button id="filter_entries_button" class="button button-primary button-large">Show complaints
				</button>

				<button id="get_results_button" class="button button-primary button-large">Get Results
				</button>

			</div>

			&nbsp;
			<span id="sending" style="display:none;">
				Sending...
			</span>
			<br/><br/>

			<textarea id="response" rows="30" cols="100"></textarea>
		</div>
	<?php
	}

	function plugin_settings_fields(){
		return array(
			array(
				'title' => 'Authentication',
				'description' => 'Enter the settings for the remote server.',
				'fields' => array(
					array(
						'name'              => 'api_url',
						'label'             => 'API Url',
						'type'              => 'text',
						'class'             => 'medium',
					),
					array(
						'name'              => 'public_key',
						'label'             => 'Public API Key',
						'type'              => 'text',
						'class'             => 'medium',
					),
					array(
						'name'              => 'private_key',
						'label'             => 'Private API Key',
						'type'              => 'text',
						'class'             => 'medium',
					),
				)
			),
		);
	}

	function uninstall(){
		delete_option( 'gf_api_demo_2_form_id' );
	}

} // end class