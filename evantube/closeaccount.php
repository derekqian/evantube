<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////


include_once('classes/config.php');
include_once('classes/login_check.php');
include_once('classes/sessions.php');

$show_table = 1;

//check if this is admin account (cannot delete admin account)
if($user_group == 'admin'){
$admin_warning = $warning_admin_account;
$show_table = '';
}
    //check if delete users has been pressed
    if (isset($_POST['close'])) {
    
        // member profile
        $sql = "DELETE FROM member_profile WHERE user_id = $user_id";
        @mysql_query($sql);

        //videos
        $sql = "UPDATE videos SET approved ='pendingdelete' WHERE user_id = $user_id";
        @mysql_query($sql);

        //images
        $sql = "UPDATE images SET approved ='pendingdelete' WHERE user_id = $user_id";
        @mysql_query($sql);
        
        //blogs
        $sql = "UPDATE blogs SET approved ='pendingdelete' WHERE user_id = $user_id";
        @mysql_query($sql);
          
        //images galleries
        $sql = "UPDATE image_galleries SET approved ='pendingdelete' WHERE user_id = $user_id";
        @mysql_query($sql);
        
        //delete from blog_replys
        $sql = "DELETE FROM blog_replys WHERE by_id = $user_id";
        @mysql_query($sql);

        //delete from imagecomments
        $sql = "DELETE FROM imagecomments WHERE by_id = $user_id";
        @mysql_query($sql);
    
        //delete comment replies also
        $sql = "DELETE FROM imagecomments_replys WHERE by_id = $user_id";
        @mysql_query($sql);       
        
        //delete group videos by member (deleted)
        $sql = "DELETE FROM group_videos WHERE member_id = $user_id";
        @mysql_query($sql);

        //delete group membership
        $sql = "DELETE FROM group_membership WHERE member_id = $user_id";
        @mysql_query($sql);

        //delete from group_comments
        $sql = "DELETE FROM group_comments WHERE by_id = $user_id";
        @mysql_query($sql);

        //delete from pictures
        $sql = "DELETE FROM pictures WHERE user_id = $user_id";
        @mysql_query($sql);

        //delete from friends part 1
        $sql = "DELETE FROM friends WHERE user_id = $user_id";
        @mysql_query($sql);

        //delete from friends part 2
        $sql = "DELETE FROM friends WHERE friends_id = $user_id";
        @mysql_query($sql);

        //delete from flagging_comments
        $sql = "DELETE FROM flagging_comments WHERE user_id = $user_id";
        @mysql_query($sql);

        //delete from favorites
        $sql = "DELETE FROM favorites WHERE WHERE owner_id = $user_id";
        @mysql_query($sql);

        //delete from flagging
        $sql = "DELETE FROM flagging WHERE user_id = $user_id";
        @mysql_query($sql);

        //delete from privacy
        $sql = "DELETE FROM privacy WHERE user_id = $user_id";
        @mysql_query($sql);

        //delete from videocomments
        $sql = "DELETE FROM videocomments WHERE by_id = $user_id";
        @mysql_query($sql);

        //delete from videocomments_replys
        $sql = "DELETE FROM videocomments_replys WHERE by_id = $user_id";
        @mysql_query($sql);
        
        //delete from audiocomments
        $sql = "DELETE FROM audiocomments WHERE by_id = $user_id";
        @mysql_query($sql);

        //delete from audiocomments_replys
        $sql = "DELETE FROM audiocomments_replys WHERE by_id = $user_id";
        @mysql_query($sql);        
        	        
        //delete from profile comments
        $sql = "DELETE FROM profilecomments WHERE members_id = $user_id";
        @mysql_query($sql);
        
        //delete from profile comments replies
        $sql = "DELETE FROM profilecomments_replys WHERE by_id = $user_id";
        @mysql_query($sql);        
     
        //delete entire groups i manage
        $sql = "DELETE FROM group_profile WHERE admin_id = $user_id";
        @mysql_query($sql);       
        
        //delete video_playlist
        $sql = "DELETE FROM video_playlist WHERE user_id = $user_id";
        @mysql_query($sql);
		
        //delete video_playlist_lists
        $sql = "DELETE FROM video_playlist_lists WHERE user_id = $user_id";
        @mysql_query($sql);	
        
        //kill session
        session_destroy();
        
        //all done
        $show_table = 2;
        $admin_warning = '';
        
    }


////////////////////////////////
//display form with error message
////////////////////////////////
$template = "edit/templates/inner_close_account.htm";//middle of page
$TBS = new clsTinyButStrong;
$TBS->NoErr = true;// no more error message displayed.
$TBS->LoadTemplate("$template");
$TBS->Render = TBS_OUTPUT;
$TBS->tbs_show();
@mysql_close();
die();

?>