<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCWPCF7_Product')) {

  class OCWPCF7_Product {

    protected static $OCWPCF7_instance;
    /**
     * Registers Products Shortcode.
     */
    function wpcf7_cfpl_add_shortcode_products() {
    wpcf7_add_form_tag( array( 'products', 'products*' ),
            array($this, 'wpcf7_cfpl_products_shortcode_handler'), true );
    }

    function wpcf7_cfpl_products_shortcode_handler( $tag ) {
        $tag = new WPCF7_FormTag( $tag );
        if ( empty( $tag->name ) )
            return '';

        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error )
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['class'] = $tag->get_class_option( $class );
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

        if ( $tag->is_required() )
            $atts['aria-required'] = 'true';

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $multiple = $tag->has_option( 'multiple' );
        $include_blank = !$multiple ? $tag->has_option( 'include_blank' ) : false;
        $first_as_label = $tag->has_option( 'first_as_label' );
        $enable_search_box = $tag->has_option( 'enable_search_box' );
        $atts['allow-clear'] = 'false';
        $atts['search-box'] = 'false';
       
        // Get Filter Option Data
        $product_cat = $tag->get_option( 'category' )[0];
        $product_tags = $tag->get_option( 'tags' )[0];
        $product_featured = $tag->get_option( 'featured' )[0];
        $product_bestselling = $tag->get_option( 'bestselling' )[0];

        $image_width = 80;

        // Filter Options wise get Data
        $product_posts = get_posts( array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'numberposts' => -1,
            ) );

        
        // Display product options
        $product_data = array();
        $values = array();
        $selectedmvalue = array();
        $html = 'Use <strong>Pro Version</strong> for ';
        if(!empty($product_cat)){
            $html .= 'product category';
            if($multiple){
                $html .= ', Multiple selections';
            }
            if($enable_search_box){
                $html .= ', Search box';
            }
        } else if(!empty($product_tags)){
            $html .= 'product tags'; 
            if($multiple){
                $html .= ', Multiple selections,';
            }
            if($enable_search_box){
                $html .= ', Search box';
            }
        } else if(!empty($product_featured)){
            $html .= 'product featured'; 
            if($multiple){
                $html .= ', Multiple selections,';
            }
            if($enable_search_box){
                $html .= ', Search box';
            }
        } else if(!empty($product_bestselling)){
            $html .= 'product best selling'; 
            if($multiple){
                $html .= ', Multiple selections,';
            }
            if($enable_search_box){
                $html .= ', Search box';
            }
       } else if($multiple){
            $html .= 'Multiple selections';
            if($enable_search_box){
                $html .= ', Search box';
            }
        } else if($enable_search_box){
            $html .= 'Search box';
            if($multiple){
                $html .= ', Multiple selections';
            }
        } else {
            foreach ( $product_posts as $product ) {
                // Set `values` with SKU & product title
                $values[] = $product->post_title;
                $product_p = wc_get_product( $product->ID );
                if(!empty($product_p->get_price())) {
                        $pro_content = wc_price($product_p->get_price());
                } else {
                        $pro_content = '';
                }
                $product_imgdata = get_the_post_thumbnail_url($product->ID, 'thumbnail');
                $selectedmvalue = array('pro_title' => $product->post_title,
                                        'image_width' => $image_width,
                                        'pro_content' => $pro_content,
                                        'pro_post_id' => $product->ID);
                   if(!empty($product_imgdata)) {
                            $selectedmvalue['pro_image_url'] =  $product_imgdata;
                        }else {
                            $selectedmvalue['pro_image_url'] =  OCWPCF7_PLUGIN_DIR .'/images/placeholder.jpg';
                        }

                $product_data[] = $selectedmvalue;
                
            }

            $values = $values;
            $labels = array_values( $values );

            $shifted = false;

            $placeholder = apply_filters('wpcf7_'.$tag->name.'_placeholder', __('&mdash; Select &mdash;'), $tag->get_option('post-type', '', true), $tag);

            $html = '';
            $hangover = wpcf7_get_hangover( $tag->name );
            foreach ( $product_data as $key => $value ) {
                $selected = false;
                if ($include_blank && $shifted == false) {
                    $item_empty = array(
                    'value' => '',
                    'data-pro_image_url' => '',
                    'data-pro_content' => '',
                    'image_width'=> '',
                    'data-pro_post_id' => -1,
                    'selected' => $selected ? 'selected' : '' );
                    
                    $item_empty = wpcf7_format_atts( $item_empty );

                    $label = $placeholder;

                    $html .= sprintf( '<option %1$s>%2$s</option>',
                        $item_empty, esc_html( $label ) );
                    $shifted = true;

                }
                if ( $hangover ) {
                    if ( $multiple ) {
                        $selected = in_array( esc_sql( $value['pro_title'] ), (array) $hangover );
                    } else {
                        $selected = ( $hangover == esc_sql( $value['pro_title'] ) );
                    }
                } else {
                    $defaults = array();
                    if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
                        $selected = true;
                    } elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
                        $selected = true;
                    }
                }

                $item_atts = array(
                    'value' => $value['pro_title'],
                    'data-pro_post_id' => $value['pro_post_id'],
                    'data-width' => $value['image_width'],
                    'data-pro_image_url' =>  $value['pro_image_url'],
                    'data-pro_content' =>  $value['pro_content'],
                    'selected' => $selected ? 'selected' : '' ); 
          
                $item_atts = wpcf7_format_atts( $item_atts );

                $label = isset( $labels[$key] ) ? $labels[$key] : $value;

                $html .= sprintf( '<option %1$s>%2$s</option>',
                    $item_atts, esc_html( $label ) );
            }

            if ( $multiple )
                $atts['multiple'] = 'multiple';

            $atts['placeholder'] = $placeholder;
            $atts['name'] = $tag->name . ( $multiple ? '[]' : '' );
            $atts = wpcf7_format_atts( $atts );

            $html = sprintf(
                '<span class="wpcf7-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
                sanitize_html_class( $tag->name ), $atts, $html, $validation_error );   
        }
        return $html;
    }

    function wpcf7_cfpl_products_validation_filter( $result, $tag ) {
        $tag = new WPCF7_FormTag( $tag );

        $name = $tag->name;

        if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
            foreach ( $_POST[$name] as $key => $value ) {
                if ( '' === $value )
                    unset( $_POST[$name][$key] );
            }
        }

        $empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

        if ( $tag->is_required() && $empty ) {
            $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
        }

        return $result;
    }

    function init() {
        add_action( 'wpcf7_init',  array($this, 'wpcf7_cfpl_add_shortcode_products'));
        /* Validation filter */
        add_filter( 'wpcf7_validate_products', array($this, 'wpcf7_cfpl_products_validation_filter'), 10, 2 );
        add_filter( 'wpcf7_validate_products*', array($this, 'wpcf7_cfpl_products_validation_filter'), 10, 2 );
    }

    public static function OCWPCF7_instance() {
      if (!isset(self::$OCWPCF7_instance)) {
        self::$OCWPCF7_instance = new self();
        self::$OCWPCF7_instance->init();
      }
      return self::$OCWPCF7_instance;
    }

  }

  OCWPCF7_Product::OCWPCF7_instance();
}

