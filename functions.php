<?php
/* 
 * Encolo el script del cliente
 */
add_action( 'wp_enqueue_scripts', 'wpis_enqueue' );
function wpis_enqueue() {
    global $wp_query;   
    if ( is_front_page() && is_home() ) {
        wp_enqueue_script( 
            'main_script', 
            get_stylesheet_directory_uri() . '/js/wpis-script.js', 
            array( 'jquery' ),
            filemtime( get_stylesheet_directory() . '/js/wpis-script.js' )
        );
        wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/css/loader-styles.css' );
        wp_localize_script(
            'main_script', 
            'ajaxObj', 
            array(
                'AjaxUrl' => admin_url( 'admin-ajax.php' ),
                'MaxNumPages' => $wp_query->max_num_pages,
                'nonce' => wp_create_nonce( 'wpis_nonce' )
            )
        );
    }
}

/*
 * Callback de la función Ajax
 */
add_action( 'wp_ajax_nopriv_wpis_infinite_scroll', 'wpis_ajax_callback' );
add_action( 'wp_ajax_wpis_infinite_scroll', 'wpis_ajax_callback' );
function wpis_ajax_callback() {
    $nonce = filter_input( INPUT_POST, 'nonce' );
    if( ! wp_verify_nonce( $nonce, 'wpis_nonce' ) ) {
        wp_die( 'Error' );
    } else {
        $page_number = filter_input( INPUT_POST, 'pageNumber' );
        query_posts(array(
            'paged' => $page_number
        ));
        ob_start();
        while(have_posts()) : the_post();
            include ( get_stylesheet_directory() . '/templates/ajax-post.php' );
        endwhile;
        $result = ob_get_clean();
        $response = array(
            'result' => $result
        );
        wp_send_json( $response );
    } 
}

/*
 * Desactivo la paginación por defecto del tema
 */
function ascent_content_nav() {
    return;
}

/* 
 * Incluyo los estilos del tema padre
 */
function my_theme_enqueue_styles() {
    $parent_style = 'parent-style'; // Estos son los estilos del tema padre recogidos por el tema hijo.
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );