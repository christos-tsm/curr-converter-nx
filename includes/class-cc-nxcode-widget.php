<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CC_NXCode_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'cc_nxcode_widget',
			__( 'Currency Converter NXCode', 'cc-nxcode' ),
			array(
				'classname'   => 'cc-nxcode-widget',
				'description' => __( 'A modern currency converter powered by React.', 'cc-nxcode' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$default_from = ! empty( $instance['default_from'] ) ? strtoupper( $instance['default_from'] ) : 'USD';
		$default_to   = ! empty( $instance['default_to'] ) ? strtoupper( $instance['default_to'] ) : 'EUR';

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $title ) ) . $args['after_title'];
		}

		printf(
			'<div class="cc-nxcode-root" data-default-from="%s" data-default-to="%s"></div>',
			esc_attr( $default_from ),
			esc_attr( $default_to )
		);

		echo $args['after_widget'];

		cc_nxcode()->enqueue_assets();
	}

	public function form( $instance ) {
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Currency Converter', 'cc-nxcode' );
		$default_from = ! empty( $instance['default_from'] ) ? $instance['default_from'] : 'USD';
		$default_to   = ! empty( $instance['default_to'] ) ? $instance['default_to'] : 'EUR';

		$currencies = array(
			'USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD',
			'MXN', 'SGD', 'HKD', 'NOK', 'KRW', 'TRY', 'INR', 'BRL', 'ZAR', 'PLN',
			'DKK', 'THB', 'ILS', 'PHP', 'AED', 'SAR', 'MYR', 'RON', 'CZK', 'HUF',
		);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'cc-nxcode' ); ?>
			</label>
			<input
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="text"
				value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'default_from' ) ); ?>">
				<?php esc_html_e( 'Default "From" currency:', 'cc-nxcode' ); ?>
			</label>
			<select
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'default_from' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'default_from' ) ); ?>"
			>
				<?php foreach ( $currencies as $code ) : ?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $default_from, $code ); ?>>
						<?php echo esc_html( $code ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'default_to' ) ); ?>">
				<?php esc_html_e( 'Default "To" currency:', 'cc-nxcode' ); ?>
			</label>
			<select
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'default_to' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'default_to' ) ); ?>"
			>
				<?php foreach ( $currencies as $code ) : ?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $default_to, $code ); ?>>
						<?php echo esc_html( $code ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                 = array();
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['default_from'] = sanitize_text_field( $new_instance['default_from'] );
		$instance['default_to']   = sanitize_text_field( $new_instance['default_to'] );
		return $instance;
	}
}
