<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Caaps_Widgets extends WP_Widget {
	public static $initiated = false;
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {		
		parent::__construct(
			'caaps_categories_widget',
			esc_html__( 'Amazon Shop Categories', 'codeshop-amazon-affiliate' ),
			array( 'description' => esc_html__( 'Display AmazonShop all categories', 'codeshop-amazon-affiliate' ), )
		);				
		if ( ! self::$initiated ) { self::initiate_hooks(); }		
	}

     public static function initiate_hooks() {
		 add_action( 'widgets_init', array( __CLASS__, 'caaps_register_widget' ) );
		 self::$initiated = true;
	 }
	 
	 public static function caaps_register_widget() {
		 register_widget( 'Caaps_Widgets' );
	 }
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$allterms = get_terms( array( 'taxonomoy' => 'amazonshop_products_cat' ) );
		if ( isset( $allterms) && count( $allterms) > 0 ) {
			echo '<ul class="caaps-categories-list">';
			foreach ( $allterms as $term ) {
				echo '<li><a href="'.get_term_link($term->term_id, 'amazonshop_products_cat').'">'.ucfirst( $term->name ).'</a></li>';
			}
			echo '</ul>';
		}				
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'AmazonShop Categories', 'codeshop-amazon-affiliate' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_attr_e( 'Title:', 'codeshop-amazon-affiliate' ); ?>
        </label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
} // End class
?>