<?php



//Additional Things
///////////////////
$side_menu2 = 'enabled'; //as shown on left menu
$display_date = date("F j, Y, g:i a"); 






/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Videos_______________________________________________________________________________________________________________________________*/

function managevideo($vid,$manage_type) {
    $video_id = @mysql_real_escape_string($vid);

// Option (1) Delete video
//////////////////////////
if($manage_type == 'delete'){
    //update videos by member (deleted) [NOTE: actual videos will be deleted on "server maintanance", i.e. all marked as pendingdelete]
    $sql = "UPDATE videos SET approved ='pendingdelete' WHERE indexer = $video_id";
    @mysql_query($sql);

    //delete group videos by $video_id (deleted)
    $sql = "DELETE FROM group_videos WHERE video_id = $video_id";
    @mysql_query($sql);

    //update all users favorite videos by $video_id (deleted)
    $sql = "UPDATE favorites SET video_status ='deleted' WHERE video_id = $video_id";
    @mysql_query($sql);

    //delete from favorites
    $sql = "DELETE FROM favorites WHERE WHERE video_id = $video_id";
    @mysql_query($sql);

    //delete from flagging
    $sql = "DELETE FROM flagging WHERE content_id = $video_id AND flag_type = 'videos'";
    @mysql_query($sql);

    //delete from videocomments
    $sql = "DELETE FROM videocomments WHERE video_id = $video_id";
    @mysql_query($sql);
}

// Option (2) Feature video
//////////////////////////
if($manage_type == 'feature'){
    $sql = "UPDATE videos SET featured ='yes' WHERE indexer = $video_id";
    @mysql_query($sql);
}


// Option (3) UnFeature video
//////////////////////////
if($manage_type == 'unfeature'){
    $sql = "UPDATE videos SET featured ='no' WHERE indexer = $video_id";
    @mysql_query($sql);
}


// Option (4) Unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE videos SET flag_counter = 0 WHERE indexer = $video_id";
    @mysql_query($sql);
}


// Option (5) Approve
//////////////////////////
if($manage_type == 'approve'){
    //Approve
    $sql = "UPDATE videos SET approved ='yes' WHERE indexer = $video_id";
    @mysql_query($sql);
}


// Option (6) Promote video
//////////////////////////
if($manage_type == 'promote'){
    $sql = "UPDATE videos SET promoted ='yes' WHERE indexer = $video_id";
    @mysql_query($sql);
}

// Option (7) Un-Promote video
//////////////////////////
if($manage_type == 'unpromote'){
    $sql = "UPDATE videos SET promoted ='no' WHERE indexer = $video_id";
    @mysql_query($sql);
}




}

/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Audio________________________________________________________________________________________________________________________________*/

function manageaudio($aid,$manage_type) {
    $audio_id = @mysql_real_escape_string($aid);

// Option (1) Delete Audio
//////////////////////////
if($manage_type == 'delete'){
    //Approve
    $sql = "UPDATE audios SET approved ='pendingdelete' WHERE indexer = $audio_id";
    @mysql_query($sql);
    
    //delete from flagging
    $sql = "DELETE FROM flagging WHERE content_id = $video_id AND flag_type = 'audios'";
    @mysql_query($sql);

    //delete
    $sql = "DELETE FROM audio_favorites WHERE WHERE audio_id = $audio_id";
    @mysql_query($sql);

    //delete from comments
    $sql = "DELETE FROM audiocomments WHERE audio_id = $audio_id";
    @mysql_query($sql);
}

// Option (2) Feature
//////////////////////////
if($manage_type == 'feature'){
    $sql = "UPDATE audios SET featured ='yes' WHERE indexer = $audio_id";
    @mysql_query($sql);
}


// Option (3) Un-Feature
//////////////////////////
if($manage_type == 'unfeature'){
    $sql = "UPDATE audios SET featured ='no' WHERE indexer = $audio_id";
    @mysql_query($sql);
}

// Option (4) unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE audios SET flag_counter = 0 WHERE indexer = $audio_id";
    @mysql_query($sql);
}


// Option (5) Approve
//////////////////////////
if($manage_type == 'approve'){
	
    //Approve
    $sql = "UPDATE audios SET approved ='yes' WHERE indexer = $audio_id";
    @mysql_query($sql);
}

}


/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Blogs________________________________________________________________________________________________________________________________*/

function manageblogs($id,$manage_type) {
    $blog_id = @mysql_real_escape_string($id);

// Option (1) Delete 
//////////////////////////
if($manage_type == 'delete'){
    //update videos by member (deleted) [NOTE: actual videos will be deleted on "server maintanance", i.e. all marked as pendingdelete]
    $sql = "UPDATE blogs SET approved ='pendingdelete' WHERE indexer = $blog_id";
    @mysql_query($sql);
    
    //delete from flagging
    $sql = "DELETE FROM flagging WHERE content_id = $blog_id AND flag_type = 'blogs'";
    @mysql_query($sql);


    //delete from ocomments/replys
    $sql = "DELETE FROM blog_replys WHERE blog_id = $blog_id";
    @mysql_query($sql);
}

// Option (2) Feature 
//////////////////////////
if($manage_type == 'feature'){
    $sql = "UPDATE blogs SET featured ='yes' WHERE indexer = $blog_id";
    @mysql_query($sql);
}


// Option (3) Un-Feature
//////////////////////////
if($manage_type == 'unfeature'){
    $sql = "UPDATE blogs SET featured ='no' WHERE indexer = $blog_id";
    @mysql_query($sql);
}

// Option (4) unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE blogs SET flag_counter = 0 WHERE indexer = $blog_id";
    @mysql_query($sql);
}


// Option (5) Approve
//////////////////////////
if($manage_type == 'approve'){
    //Approve
    $sql = "UPDATE blogs SET approved ='yes' WHERE indexer = $blog_id";
    @mysql_query($sql);

}

}



/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Flagged Comments_____________________________________________________________________________________________________________________*/

function manage_flagged_comments($id,$manage_type,$comments) {
    $id = @mysql_real_escape_string($id);

// Option (1) Delete 
//////////////////////////
if($manage_type == 'delete'){
    //delete main comment
    $sql = "DELETE FROM $comments WHERE indexer = $id";
    @mysql_query($sql);


//Delete all the replys also 
 switch ($comments){
 	
 	case 'videocomments':
    $sql = "DELETE FROM videocomments_replys WHERE videocomment_id = $id";
    @mysql_query($sql); 
    break;
    
    case 'audiocomments':
    $sql = "DELETE FROM audiocomments_replys WHERE audiocomment_id = $id";
    @mysql_query($sql); 
    break;
    
    
    case 'imagecomments':
    $sql = "DELETE FROM imagecomments_replys WHERE imagecomment_id = $id";
    @mysql_query($sql); 
    break;
    
    case 'profilecomments':
    $sql = "DELETE FROM profilecomments_replys WHERE profilecomment_id = $id";
    @mysql_query($sql); 
    break;   

    case 'blogs':
    $sql = "DELETE FROM blog_replys WHERE blog_id = $id";
    @mysql_query($sql); 
    break; 
}
 
}

// Option (2) unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE $comments SET flag_counter = 0 WHERE indexer = $id";
    @mysql_query($sql);
}

}


/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Images/Albums________________________________________________________________________________________________________________________*/

function manageimages($id,$manage_type) {
    $id = @mysql_real_escape_string($id);
    $base_path    = installation_paths();
    
// Option (1) Delete Image
//////////////////////////
if($manage_type == 'delete'){

    //delete actual image from server
    $sql = "SELECT image_id, gallery_id from images WHERE indexer = $id";
    $result = @mysql_fetch_array(@mysql_query($sql));
    $image_file_name = $result['image_id'];
    $gallery_id = $result['gallery_id'];
    @unlink($base_path.'/addons/albums/images/'.$image_file_name);
    @unlink($base_path.'/addons/albums/thumbs/'.$image_file_name);
	    
    //delete image from table
    $sql = "DELETE FROM images WHERE indexer = $id";
    @mysql_query($sql);


    //delete from flagging
    $sql = "DELETE FROM flagging WHERE content_id = $id AND flag_type = 'images'";
    @mysql_query($sql);

    //delete from videocomments
    $sql = "DELETE FROM imagecomments WHERE image_id = $id";
    @mysql_query($sql);
    
    //delete comment replies also
    $sql = "DELETE FROM imagecomments_replys WHERE imagecomment_id = $id";
    @mysql_query($sql);
    
    //if this was last image left in gallery -- then delete the gallery also
    $sql = "SELECT image_id from images WHERE gallery_id = $gallery_id";
    $count = @mysql_num_rows(@mysql_query($sql));
    if ($count == 0){
    $sql = "DELETE FROM image_galleries WHERE gallery_id = $gallery_id";
    @mysql_query($sql);
    }
	    
}

// Option (2) Feature video
//////////////////////////
if($manage_type == 'feature'){
    $sql = "UPDATE images SET featured ='yes' WHERE indexer = $id";
    @mysql_query($sql);
}


// Option (3) UnFeature video
//////////////////////////
if($manage_type == 'unfeature'){
    $sql = "UPDATE images SET featured ='no' WHERE indexer = $id";
    @mysql_query($sql);
}


// Option (4) Unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE images SET flag_counter = 0 WHERE indexer = $id";
    @mysql_query($sql);
}


// Option (5) Approve
//////////////////////////
if($manage_type == 'approve'){
    //Approve
    $sql = "UPDATE images SET approved ='yes' WHERE indexer = $id";
    @mysql_query($sql);
}
}


/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Images/Albums________________________________________________________________________________________________________________________*/

function managegroups($id,$manage_type) {
    $id = @mysql_real_escape_string($id);
    $base_path    = installation_paths();
    
// Option (1) Delete Group
//////////////////////////
if($manage_type == 'delete'){
	    
    //delete actual group from table
    $sql = "DELETE FROM group_profile WHERE indexer = $id";
    @mysql_query($sql);


    //delete from flagging
    $sql = "DELETE FROM flagging WHERE content_id = $id AND flag_type = 'group_profile'";
    @mysql_query($sql);

    //delete from groupcomments
    $sql = "DELETE FROM group_comments WHERE group_id = $id";
    @mysql_query($sql);
    
    //delete group membership
    $sql = "DELETE FROM group_membership WHERE group_id = $id";
    @mysql_query($sql);
    
    //delete group videos
    $sql = "DELETE FROM group_videos WHERE group_id = $id";
    @mysql_query($sql);
    
    //delete group topics
    $sql = "DELETE FROM group_topics WHERE group_id = $id";
    @mysql_query($sql);
    
	    
}

// Option (2) Feature Group
//////////////////////////
if($manage_type == 'feature'){
    $sql = "UPDATE group_profile SET featured ='yes' WHERE indexer = $id";
    @mysql_query($sql);
}


// Option (3) UnFeature Group
//////////////////////////
if($manage_type == 'unfeature'){
    $sql = "UPDATE group_profile SET featured ='no' WHERE indexer = $id";
    @mysql_query($sql);
}


// Option (4) Unflag
//////////////////////////
if($manage_type == 'reset_flags'){
    $sql = "UPDATE group_profile SET flag_counter = 0 WHERE indexer = $id";
    @mysql_query($sql);
}

}



/*_______________________________________________________________________________________________________________________________________________*/

/*___Manage Members______________________________________________________________________________________________________________________________*/

function managemember($user_id,$manage_type) {//manage type can be delete, suspend etc

    global $config;
    $base_path = installation_paths();
    $user_id = @mysql_real_escape_string($user_id);
    
    //check if This is the admin account
$sql ="SELECT * FROM member_profile WHERE user_id = $user_id AND user_group = 'admin'";   
$query = @mysql_query($sql);
if (@mysql_num_rows($query) < 0) {
$manage_type = '';
}

    //////////////////////////////
    //decide what to do with member
    ///////////////////////////////

    // Option (1) delete user (this also deletes all user content
    ////////////////////////////////////////////////////////////

    if ($manage_type == 'delete') {

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

    }

    // Option (2) suspend user (content is preserved)
    /////////////////////////////////////////////////
    if ($manage_type == 'suspend') {
        $sql = "UPDATE member_profile SET account_status ='suspended' WHERE user_id = $user_id";
        @mysql_query($sql);
		//email customer that activation has been down
		$send_email = true;
        $sql = "SELECT * FROM member_profile WHERE user_id = $user_id";
        $result = mysql_fetch_array(mysql_query($sql));
        $to = $result['email_address'];
        $email_template = $base_path.'/email_templates/accountsuspended.htm';
        $subject = $config["email_subject_suspended"];    
        
    }

    // Option (3) aprove user (content is preserved)
    /////////////////////////////////////////////////
    if ($manage_type == 'activate') {
        $sql = "UPDATE member_profile SET account_status ='active' WHERE user_id = $user_id";
        @mysql_query($sql);

		//email customer that activation has been down
		$send_email = true;
        $sql = "SELECT * FROM member_profile WHERE user_id = $user_id";
        $result = mysql_fetch_array(mysql_query($sql));
        $to = $result['email_address'];
        $email_template = $base_path.'/email_templates/accountapproved.htm';
        $subject = $config["email_subject_approved"];    
   }
   
    // Option (4) Make moderator
    /////////////////////////////////////////////////
    if ($manage_type == 'moderator') {
    	$moderator_group = $_POST['moderator_level']; //posted level
   		if($moderator_group ==''){ //error control
    		$moderator_group = 'member';
    		}
        $sql = "UPDATE member_profile SET user_group = '$moderator_group' WHERE user_id = $user_id";
        @mysql_query($sql);
   }   
   

    // Send any email to member
    ////////////////////////////
    if ($send_email == true && $config['email_member_admin_actions'] == 'yes') {
        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
        $from = $config['notifications_from_email'];
        //send email template to TBS for rendering of variable inside
        $TBS = new clsTinyButStrong;
        $TBS->NoErr = true;// no more error message displayed.
        $TBS->LoadTemplate("$email_template");
        $TBS->tbs_show(TBS_NOTHING);
        $message = $TBS->Source;
        //load postage.php
        include ($base_path.'/postage.php');
        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    }

}


/*_______________________________________________________________________________________________________________________________________________*/

/*___General Functions___________________________________________________________________________________________________________________________*/


//sort an array (usefull where mysql results are being merged but still need sorting
//example usage
// $result = arr_keys_multisort($result, 'videos_count', 'ASC');

function arr_keys_multisort($arr,$my_key,$sort_type) {

    foreach ($arr as $key => $row) {
        $arr_tmp[$key] = $row["$my_key"];

    }

    if ($sort_type == 'DESC')
        @array_multisort($arr_tmp,SORT_DESC,$arr);
    else
        @array_multisort($arr_tmp,SORT_ASC,$arr);

    return $arr;
}


//Notifications and Warnings
/////////////////////////////

function notifications($notification){
global $config;

// general success or fail on mysql
if ($notification ==1){
if(@mysql_error()){
$message = $config["error_26"]; //'An error has occurred'
$message = $config["error_26"].' SQL Error = '.mysql_error().' SQL = '.$sql; //DEBUG MODE
}else{
$message = $config["error_25"]; //'Your request has been completed'
}
}
return $message;
}

?>