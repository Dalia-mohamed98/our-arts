<?php


class Dokan_Toprated_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct( 'dokan-top-rated', __( 'Dokan: Top Rated Product Widget', 'dokan-lite'), // Name
            array( 'description' => __( 'A Widget for displaying To rated products for dokan', 'dokan-lite' ), 'classname' => 'woocommerce widget_products dokan-top-rated' ) // Args
        );
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
        $title = apply_filters( 'widget_title', $instance['title'] );
        extract( $instance );

        $r = dokan_get_top_rated_products( $no_of_product );

        echo $args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
        }

        dokan_get_template_part( 'widgets/widget-content-product', '', array( 'r' => $r, 'show_rating' => $show_rating ) );

        echo $args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

        wp_reset_postdata();
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title         = esc_attr( $instance[ 'title' ] );
            $no_of_product = esc_attr( intval( $instance[ 'no_of_product' ] ) );
            $show_rating   = esc_attr( $instance['show_rating'] );
        }  else {
            $title         = __( 'أعلى تقييم لمنتج', 'dokan-lite' );
            $no_of_product = '8';
            $show_rating   = '0';
        }
        $no_of_product = $no_of_product == '-1' ? '' : $no_of_product;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'الإسم:', 'dokan-lite' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'no_of_product' ) ); ?>"><?php esc_html_e( 'عدد المنتجات:', 'dokan-lite' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'no_of_product' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_of_product' ) ); ?>" type="text" value="<?php echo esc_attr( $no_of_product ); ?>">
        </p>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'show_rating' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_rating' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $show_rating ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_rating' ) ); ?>"><?php esc_html_e( 'عرض تقييم المنتج', 'dokan-lite' ); ?></label>
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
        $instance                  = array();
        $instance['title']         = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['no_of_product'] = ( ! empty( $new_instance['no_of_product'] ) && is_numeric( $new_instance['no_of_product'] ) && $new_instance['no_of_product'] > 0 ) ? strip_tags( intval( $new_instance['no_of_product'] ) ) : '8';
        $instance['show_rating']   = ( ! empty( $new_instance['show_rating'] ) ) ? strip_tags( $new_instance['show_rating'] ) : '';
        return $instance;
    }

} // class Dokan best selling product widget
