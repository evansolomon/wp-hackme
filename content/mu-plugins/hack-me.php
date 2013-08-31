<?php

/*
Plugin Name: Hack Me
Author: Evan Solomon
*/

class ES_Hack_Me {
    private static $instance;
    const USER_ROLE = 'administrator';

    static function init() {
        if ( ! self::$instance )
            self::$instance = new self;

        return self::$instance;
    }

    private function __construct() {
        add_filter( 'pre_option_comment_moderation', '__return_true' );
        add_action( 'wp_set_comment_status', array( 'ES_Hack_Me', 'set_comment_status' ), 10, 2 );
    }

    static function set_comment_status( $comment_id, $comment_status ) {
        if ( $comment_status != 'approve' )
            return;

        $comment = get_comment( $comment_id );
        return self::create_user( $comment->comment_author_email );
    }

    private static function create_user( $email ) {
        $password = wp_generate_password( 50 );
        $user_id = wp_create_user( $email, $password, $email );

        if ( is_wp_error( $user_id ) )
            return;

        $user = get_user_by( 'id', $user_id );
        $user->set_role( self::USER_ROLE );
        return self::alert_new_user( $user_id, $password );
    }

    private static function alert_new_user( $user_id, $password ) {
        $user = get_user_by( 'id', $user_id );
        $subject = sprintf( "Congratulations, you're an admin on %s", get_home_url() );

        $body = sprintf( "Login to %s with these credentials and do something creative.\n\n", admin_url() );
        $body .= sprintf( "Username: %s\n", $user->user_login );
        $body .= sprintf( "Password: %s\n", $password );

        return wp_mail( $user->user_email, $subject, $body );
    }
}

ES_Hack_Me::init();
