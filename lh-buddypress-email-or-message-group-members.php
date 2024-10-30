<?php
/**
 * Plugin Name: LH Buddypress Email or Message Group Members
 * Plugin URI: https://lhero.org/portfolio/lh-buddypress-email-group-members/
 * Description: A simple WordPress plugin to send mails to all buddypress group members  
 * Version: 1.01
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com
 * Text Domain: lh_beomgm
 * Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if (!class_exists('LH_Buddypress_email_group_members_plugin')) {

class LH_Buddypress_email_group_members_plugin {
    
    private static $instance;
    
    
static function return_plugin_namespace(){
    
    return 'lh_beomgm';
    
    }
    
static function maybe_get_stored_subject( $group_id ) { 
    
    $subject_group_meta = groups_get_groupmeta( $group_id, self::return_plugin_namespace().'_subject' );
    
    $subject_option = get_option(self::return_plugin_namespace().'_subject');
    
    if (!empty($subject_group_meta)){
        
        return $subject_group_meta;
        
    } elseif (!empty($subject_option)){
        
        return $subject_option;
        
    } else {
        
        return false;
        
    }
    
}

static function maybe_get_stored_message( $group_id ) { 
    
    $message_group_meta = groups_get_groupmeta( $group_id, self::return_plugin_namespace().'_message' );
    
    $message_option = get_option(self::return_plugin_namespace().'_message');
    
    if (!empty($message_group_meta)){
        
        return $message_group_meta;
        
    } elseif (!empty($message_option)){
        
        return $message_option;
        
    } else {
        
        return false;
        
    }
    
}
    
static function replace_tokens_in_text( $text, $tokens ) { 
    $unescaped = array(); 
    $escaped = array(); 
 
    foreach ( $tokens as $token => $value ) { 
        if ( ! is_string( $value ) && is_callable( $value ) ) { 
            $value = call_user_func( $value ); 
        } 
 
        // Tokens could be objects or arrays. 
        if ( ! is_scalar( $value ) ) { 
            continue; 
        } 
 
        $unescaped[ '{{{' . $token . '}}}' ] = $value; 
        $escaped[ '{{' . $token . '}}' ]     = esc_html( $value ); 
    } 
 
    $text = strtr( $text, $unescaped );  // Do first. 
    $text = strtr( $text, $escaped ); 
 
    /** 
     * Filters text that has had tokens replaced. 
     * 
     * @since 2.5.0 
     * 
     * @param string $text 
     * @param array $tokens Token names and replacement values for the $text. 
     */ 
    return apply_filters( 'bp_core_replace_tokens_in_text', $text, $tokens ); 
} 


static function return_users_for_group_by_id($group_id){


$group_members = groups_get_group_members(array(
    'group_id' => $group_id, 
    'exclude_admins_mods'=> 1,
    'per_page' => 999,
    ));



$user_ids = array();

foreach ($group_members['members'] as $group_member){
    
$user_ids[] =  $group_member->user_id;  

    
    
}

if (empty($user_ids)){
  
return false;
    
} else {


$args = array( 'include' => $user_ids);

$user_query = new BP_User_Query( $args );

if (!empty($user_query->results)){
    
    
return $user_query->results;
    
} else {
    
return false;    
    
}

}
    
    
}

static function send_message($sender_id, $recipient_id, $title, $content){
    
if (!empty($recipient_id) && function_exists('messages_new_message')){

    
    $message = new BP_Messages_Message;
    $message->thread_id = 0;
    $message->sender_id = $sender_id;
    $message->subject   = $title;
    $message->message   = $content;
    $message->date_sent = bp_core_current_time();
    $recipient_ids = array($recipient_id);
    
            // Format this to match existing recipients.
        foreach ( (array) $recipient_ids as $i => $recipient_id ) {
            $message->recipients[ $i ]          = new stdClass;
            $message->recipients[ $i ]->user_id = $recipient_id;
        }
    
    
    $sent_id = $message->send();
    


if (!empty($sent_id) && !is_wp_error($sent_id )){
    
    bp_messages_message_sent_add_notification($message);



}
    
    
return $sent_id;    
    
}
    
    
    
}


static function populate_text_with_tokens($text, $user_object, $group_id){
    


$tokens = array();

$tokens['recipient.first_name'] = get_userdata($user_object->ID)->first_name;
$tokens['recipient.last_name'] = get_userdata($user_object->ID)->last_name;
$tokens['recipient.display_name'] = $user_object->display_name;


$group = groups_get_group( array( 
    'group_id' => $group_id
    )
    );
    
$tokens['group.name'] = $group->name;
$tokens['group.description'] = $group->description;
$tokens['group.url'] = bp_get_group_permalink($group);




$tokens['site.name'] = get_bloginfo('name');



$tokens = apply_filters(self::return_plugin_namespace().'_send_notification_tokens', $tokens, $user_object);


$return = self::replace_tokens_in_text($text, $tokens);
 
return $return;




}



    
public function plugin_init(){
    
    if ( bp_is_active( 'groups' ) && !class_exists('LH_Begm_screen_class') && !is_admin()) {
        
        include_once 'classes/lh_begm-screen-class.php';
        
           if( bp_is_group_admin_page() && (self::return_plugin_namespace().'-screen' == bp_get_group_current_admin_tab() ) ){

            wp_enqueue_script( 'tiny_mce' );
    }
        
        
    }
    

}

	 /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    
    public function __construct() {

//try to run everything on plugins loaded
add_action( 'bp_init', array($this,'plugin_init'));

}

}

$lh_buddypress_email_group_members_instance = LH_Buddypress_email_group_members_plugin::get_instance();

}

add_filter( 'groups_create_group_steps', function ( $steps ) {
    

    

unset( $steps['group-settings'] );
unset( $steps['group-invites'] );

	return $steps;
},PHP_INT_MAX,1 );