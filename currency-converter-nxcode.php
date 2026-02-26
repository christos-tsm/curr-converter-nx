<?php

/**
 * Plugin Name: Currency Converter for NXCode
 * Plugin URI:  https://nxcode.gr
 * Description: A React-powered currency converter widget for WordPress.
 * Version:     1.0.0
 * Author:      NXCode
 * Author URI:  https://nxcode.gr
 * Text Domain: cc-nxcode
 * Domain Path: /languages
 */

if (! defined('ABSPATH')) {
	exit;
}

define('CC_NXCODE_VERSION', '1.0.0');
define('CC_NXCODE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CC_NXCODE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CC_NXCODE_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once CC_NXCODE_PLUGIN_DIR . 'includes/class-cc-nxcode-settings.php';
require_once CC_NXCODE_PLUGIN_DIR . 'includes/class-cc-nxcode-rest-api.php';
require_once CC_NXCODE_PLUGIN_DIR . 'includes/class-cc-nxcode-widget.php';

final class CC_NXCode {

	private static $instance = null;
	private $assets_enqueued = false;

	public static function instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('init', array($this, 'load_textdomain'));
		add_action('widgets_init', array($this, 'register_widget'));
		add_action('wp_enqueue_scripts', array($this, 'register_assets'));
		add_shortcode('cc_nxcode', array($this, 'render_shortcode'));


		new CC_NXCode_REST_API();

		if (is_admin()) {
			new CC_NXCode_Settings();
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain(
			'cc-nxcode',
			false,
			dirname(CC_NXCODE_PLUGIN_BASENAME) . '/languages'
		);
	}

	public function register_widget() {
		register_widget('CC_NXCode_Widget');
	}

	public function register_assets() {
		$build_url = CC_NXCODE_PLUGIN_URL . 'assets/build/';
		$build_dir = CC_NXCODE_PLUGIN_DIR . 'assets/build/';

		if (! file_exists($build_dir . 'currency-converter.js')) {
			return;
		}

		wp_register_style(
			'cc-nxcode-style',
			$build_url . 'currency-converter.css',
			array(),
			CC_NXCODE_VERSION
		);

		wp_register_script(
			'cc-nxcode-script',
			$build_url . 'currency-converter.js',
			array(),
			CC_NXCODE_VERSION,
			true
		);

		if (is_active_widget(false, false, 'cc_nxcode_widget')) {
			$this->enqueue_assets();
		}
	}

	public function enqueue_assets() {
		if ($this->assets_enqueued) {
			return;
		}
		$this->assets_enqueued = true;

		wp_enqueue_style('cc-nxcode-style');
		wp_enqueue_script('cc-nxcode-script');

		$api_key = get_option('cc_nxcode_api_key', '');

		wp_localize_script('cc-nxcode-script', 'ccNxcodeData', array(
			'restUrl' => esc_url_raw(rest_url('cc-nxcode/v1/')),
			'nonce'   => wp_create_nonce('wp_rest'),
			'useMock' => empty($api_key),
		));
	}

	public function render_shortcode($atts) {
		$atts = shortcode_atts(
			array(
				'from' => 'USD',
				'to'   => 'EUR',
			),
			$atts,
			'cc_nxcode'
		);

		$this->enqueue_assets();

		return sprintf(
			'<div class="cc-nxcode-root" data-default-from="%s" data-default-to="%s"></div>',
			esc_attr(strtoupper($atts['from'])),
			esc_attr(strtoupper($atts['to']))
		);
	}

	private function __clone() {
	}

	public function __wakeup() {
		throw new \Exception('Cannot unserialize singleton');
	}
}

function cc_nxcode() {
	return CC_NXCode::instance();
}

cc_nxcode();
