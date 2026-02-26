<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CC_NXCode_REST_API {

	const CACHE_TTL = 3600; // 1 hour

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route( 'cc-nxcode/v1', '/rates', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_rates' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'base' => array(
					'required'          => false,
					'default'           => 'USD',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return preg_match( '/^[A-Z]{3}$/', strtoupper( $param ) );
					},
				),
			),
		) );
	}

	public function get_rates( $request ) {
		$base    = strtoupper( $request->get_param( 'base' ) );
		$api_key = get_option( 'cc_nxcode_api_key', '' );

		if ( empty( $api_key ) ) {
			return $this->get_mock_rates( $base );
		}

		$transient_key = 'cc_nxcode_rates_' . $base;
		$cached        = get_transient( $transient_key );

		if ( false !== $cached ) {
			return rest_ensure_response( $cached );
		}

		$url      = sprintf( 'https://v6.exchangerate-api.com/v6/%s/latest/%s', $api_key, $base );
		$response = wp_remote_get( $url, array( 'timeout' => 10 ) );

		if ( is_wp_error( $response ) ) {
			return $this->get_mock_rates( $base );
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || empty( $body['conversion_rates'] ) ) {
			return $this->get_mock_rates( $base );
		}

		$data = array(
			'base'         => $base,
			'rates'        => $body['conversion_rates'],
			'last_updated' => $body['time_last_update_utc'] ?? gmdate( 'Y-m-d H:i:s' ),
			'source'       => 'live',
		);

		set_transient( $transient_key, $data, self::CACHE_TTL );

		return rest_ensure_response( $data );
	}

	private function get_mock_rates( $base ) {
		$usd_rates = array(
			'USD' => 1,
			'EUR' => 0.9234,
			'GBP' => 0.7891,
			'JPY' => 149.52,
			'AUD' => 1.5342,
			'CAD' => 1.3578,
			'CHF' => 0.8821,
			'CNY' => 7.2456,
			'SEK' => 10.3721,
			'NZD' => 1.6234,
			'MXN' => 17.1456,
			'SGD' => 1.3412,
			'HKD' => 7.8234,
			'NOK' => 10.5678,
			'KRW' => 1312.45,
			'TRY' => 30.2145,
			'INR' => 83.1234,
			'BRL' => 4.9567,
			'ZAR' => 18.7654,
			'PLN' => 4.0123,
			'DKK' => 6.8912,
			'THB' => 35.2345,
			'ILS' => 3.6789,
			'PHP' => 56.1234,
			'AED' => 3.6725,
			'SAR' => 3.7500,
			'MYR' => 4.6789,
			'RON' => 4.5912,
			'CZK' => 22.3456,
			'HUF' => 355.67,
			'BGN' => 1.8067,
			'ISK' => 137.45,
			'HRK' => 6.9523,
			'RUB' => 91.2345,
			'TWD' => 31.2456,
			'COP' => 3945.67,
			'CLP' => 878.90,
		);

		if ( 'USD' === $base ) {
			$rates = $usd_rates;
		} else {
			$base_in_usd = isset( $usd_rates[ $base ] ) ? $usd_rates[ $base ] : 1;
			$rates       = array();
			foreach ( $usd_rates as $code => $rate ) {
				$rates[ $code ] = round( $rate / $base_in_usd, 6 );
			}
		}

		return rest_ensure_response( array(
			'base'         => $base,
			'rates'        => $rates,
			'last_updated' => gmdate( 'Y-m-d H:i:s' ),
			'source'       => 'mock',
		) );
	}
}
