<?php

    class LH_Begm_screen_class extends BP_Group_Extension {

            function __construct() {
                $args = array(
                    'slug' => LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-screen',
                    'name' =>  __('Message Members',LH_Buddypress_email_group_members_plugin::return_plugin_namespace()),
                    'enable_nav_item' => false,
                    'nav_item_position' => 105,
                    'access' => apply_filters(LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_authority','admin'),
                    'screens' => array(
                        'edit' => array(
                            'name' => __('E-Mail Members',LH_Buddypress_email_group_members_plugin::return_plugin_namespace()),
                            // Changes the text of the Submit button
                            // on the Edit page
                            'submit_text' => __('Send',LH_Buddypress_email_group_members_plugin::return_plugin_namespace()),
                        ),
                        'create' => array(
                            'enabled' => false,
                        ),
                    ),
                );
                parent::init( $args );
            }
         
            
         
            function settings_screen( $group_id = NULL ) {
                
                $subject = LH_Buddypress_email_group_members_plugin::maybe_get_stored_subject( $group_id );
                $message = LH_Buddypress_email_group_members_plugin::maybe_get_stored_message( $group_id );
                
         
                ?>
                <p>
                <label><?php _e('Private Message:', LH_Buddypress_email_group_members_plugin::return_plugin_namespace()); ?> <input type="radio" name="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'; ?>" value="pm" required="required" /></label>
                <label><?php _e('Email:', LH_Buddypress_email_group_members_plugin::return_plugin_namespace()); ?> <input type="radio" name="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'; ?>" value="email" required="required" /></label>
                <label><?php _e('Both:', LH_Buddypress_email_group_members_plugin::return_plugin_namespace()); ?> <input type="radio" name="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'; ?>" value="both" required="required" /></label>
                </p>
                <?php _e('Compose your message to your group members here: ', LH_Buddypress_email_group_members_plugin::return_plugin_namespace()); ?>
                 
                <label for="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject'; ?>"><?php echo __('Subject',LH_Buddypress_email_group_members_plugin::return_plugin_namespace());?></label>
                <input type="text" id="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject'; ?>" name="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject'; ?>" value="<?php echo stripslashes($subject); ?>" required="required" />
                <label for="<?php echo LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_message'; ?>"><?php echo __('Message',LH_Buddypress_email_group_members_plugin::return_plugin_namespace()); ?></label>
                <?php wp_editor(stripslashes($message),LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_message', array( 'media_buttons' => true ));?>

                <?php
                
                wp_nonce_field( LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_email_nonce', LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_email_nonce' ); 
                
            }
         
            function settings_screen_save( $group_id = NULL ) {
                
                
                    //ensure the nonce is correct
                	if( empty($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_email_nonce' ]) or !wp_verify_nonce($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_email_nonce' ], LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_email_nonce' )) {

                        return;

                	}
                	
                	//ensure there is a subject
                	if( empty($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject' ]) or empty(trim($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject' ]))) {

                        return;

                	}
                	
                	$subject = sanitize_text_field($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject' ]);
                	$message = wpautop(wp_filter_post_kses($_POST[ LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_message' ]), true);
                	
    
                	
                	
                	if (($group_members = LH_Buddypress_email_group_members_plugin::return_users_for_group_by_id($group_id)) && !empty($_POST[LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'])){
                	
                
                	 foreach ($group_members as $member_object) {
                	     
                	     $title_loop = LH_Buddypress_email_group_members_plugin::populate_text_with_tokens($subject, $member_object, $group_id);
                	     
                	     $body_loop = LH_Buddypress_email_group_members_plugin::populate_text_with_tokens($message, $member_object, $group_id);
                	     
                        
                	         $email_bool = true;
                	         $pm_bool = true;
                	         
                	         if (($_POST[LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'] == 'email') or ($_POST[LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'] == 'both')){
                	     
                	     $doing_email = true;
                	     
                	     
                	     $headers = array('Content-Type: text/html; charset=UTF-8');
                	     $email_send_bool = wp_mail($member_object->user_email, $title_loop, $body_loop, $headers); 
                	     
                	     if (!$email_send_bool){
                	         
                	         $email_bool = false;
                	         
                	     }
                	     
                	     
                	         }
                	     
                	     
                	      if (($_POST[LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'] == 'pm')){
                	          
                	       $doing_pm = true;   
                	          
                	     
                        
                        $pm_send_bool = messages_new_message( 
                            array(
                         'sender_id'     => bp_loggedin_user_id(),
                         'recipients'    => array($member_object->ID),
                         'subject'       => $title_loop,
                         'content'       => $body_loop,
                           )
                           );
                           
                           if (!$pm_send_bool){
                	         
                	         $email_bool = false;
                	         
                	     }
                           
                	     }
                	     
                	       if (($_POST[LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'-send_mode'] == 'both')){
                	           
                	     $doing_pm = true;
                	     
                        
                        LH_Buddypress_email_group_members_plugin::send_message(bp_loggedin_user_id(), $member_object->ID, $title_loop, $$body_loop);
                           
                	     }
                	     
                	    
                    
              
                	     
                	 }
                	 
                	 
                	
                	groups_update_groupmeta( $group_id, LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject', $subject );
                	update_option( LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_subject', $subject, 'no' );
                    groups_update_groupmeta( $group_id, LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_message', $message );
                    update_option( LH_Buddypress_email_group_members_plugin::return_plugin_namespace().'_message', $message , 'no');
                    
                	}
                	
                	$add_message = '';
                	
                	if (!empty($doing_email)){
                	
                	if ($email_bool){
                	
                	$add_message .=  __('Your emails have been sent.',LH_Buddypress_email_group_members_plugin::return_plugin_namespace());
                	
                	} else {
                	    
                	$add_message .=  __('Some of your emails may not have been sent, check your outbox.',LH_Buddypress_email_group_members_plugin::return_plugin_namespace());
                	    
                	}
                	
                	}
                	
                	
                	if (!empty($doing_pm)){
                	
                	if ($pm_bool){
                	
                	$add_message .= __(' Your private messages have been sent.',LH_Buddypress_email_group_members_plugin::return_plugin_namespace());
                	
                	} else {
                	    
                	$add_message .= __(' Some of your private messages may not have been sent, check your sent items.',LH_Buddypress_email_group_members_plugin::return_plugin_namespace());
                	    
                	}
                	
                	}
                	
                	if (!empty($add_message)){
                	    
                	    
                	    bp_core_add_message( trim($add_message), 'update' );
                	    
                	}
  
            
       
            }



           
         
        }
        bp_register_group_extension( 'LH_Begm_screen_class' );
     

?>