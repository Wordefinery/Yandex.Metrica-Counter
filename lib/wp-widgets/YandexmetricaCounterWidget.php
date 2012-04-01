<?php

namespace wordefinery;

class YandexmetricaCounterWidget extends \WP_Widget {
    function __construct() {
        $this->plugin = \Wordefinery::Plugin('YandexmetricaCounter');
        parent::__construct(
            'wordefinery_yandexmetricacounter_widget',
            __('Yandex.Metrica Counter'),
            array(
                'description' => __('Yandex.Metrica Counter Widget')
            )
        );
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo $before_widget;
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
        echo $this->plugin->Counter();
        echo '<div class="textwidget">';
        echo $this->plugin->Informer(1);
        echo '</div>';
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = trim(strip_tags($new_instance['title']));
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', ) );
        $title = esc_attr( $instance['title'] );
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }

}