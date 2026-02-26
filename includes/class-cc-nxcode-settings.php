<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CC_NXCode_Settings {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . CC_NXCODE_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
	}

	public function add_settings_page() {
		add_options_page(
			__( 'Currency Converter NXCode', 'cc-nxcode' ),
			__( 'Currency Converter', 'cc-nxcode' ),
			'manage_options',
			'cc-nxcode-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'cc_nxcode_options', 'cc_nxcode_api_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		) );

		add_settings_section(
			'cc_nxcode_main',
			__( 'API Configuration', 'cc-nxcode' ),
			array( $this, 'render_section_description' ),
			'cc-nxcode-settings'
		);

		add_settings_field(
			'cc_nxcode_api_key',
			__( 'ExchangeRate-API Key', 'cc-nxcode' ),
			array( $this, 'render_api_key_field' ),
			'cc-nxcode-settings',
			'cc_nxcode_main'
		);
	}

	public function render_section_description() {
		$api_key = get_option( 'cc_nxcode_api_key', '' );
		$status  = empty( $api_key )
			? '<span style="color:#dc2626;font-weight:600;">' . esc_html__( 'Using mock data', 'cc-nxcode' ) . '</span>'
			: '<span style="color:#059669;font-weight:600;">' . esc_html__( 'Using live data', 'cc-nxcode' ) . '</span>';

		printf(
			'<p>%s</p><p><strong>%s:</strong> %s</p>',
			esc_html__( 'Enter your ExchangeRate-API key to fetch live exchange rates. Without an API key, the widget uses built-in mock data.', 'cc-nxcode' ),
			esc_html__( 'Status', 'cc-nxcode' ),
			$status
		);
	}

	public function render_api_key_field() {
		$value = get_option( 'cc_nxcode_api_key', '' );
		printf(
			'<input type="text" id="cc_nxcode_api_key" name="cc_nxcode_api_key" value="%s" class="regular-text" placeholder="your-api-key-here" />',
			esc_attr( $value )
		);
		printf(
			'<p class="description">%s <a href="https://www.exchangerate-api.com/" target="_blank" rel="noopener noreferrer">exchangerate-api.com</a></p>',
			esc_html__( 'Get a free API key at', 'cc-nxcode' )
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'cc_nxcode_options' );
				do_settings_sections( 'cc-nxcode-settings' );
				submit_button();
				?>
			</form>
			<hr />
			<h2><?php esc_html_e( 'Usage', 'cc-nxcode' ); ?></h2>
			<table class="widefat" style="max-width:600px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Method', 'cc-nxcode' ); ?></th>
						<th><?php esc_html_e( 'How to use', 'cc-nxcode' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Widget', 'cc-nxcode' ); ?></strong></td>
						<td><?php esc_html_e( 'Go to Appearance → Widgets and add "Currency Converter NXCode" to any sidebar.', 'cc-nxcode' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Shortcode', 'cc-nxcode' ); ?></strong></td>
						<td><code>[cc_nxcode]</code> <?php esc_html_e( 'or', 'cc-nxcode' ); ?> <code>[cc_nxcode from="GBP" to="JPY"]</code></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=cc-nxcode-settings' ),
			__( 'Settings', 'cc-nxcode' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}
}
