<?php


/**
 * Readme at end of this file
 */

class LoadPermissions
{

    //General
    var $user_id;
    var $content_id;
    var $content_type;
    var $error_exit;
    var $edit_on;
    var $delete_on;
    var $user_group;

    //Own Coments Edit
    var $edit_comments_video_own;
    var $edit_comments_blog_own;
    var $edit_comments_audio_own;
    var $edit_comments_pictures_own;
    var $edit_comments_group_own;
    var $edit_comments_profile_own;


    //Own Coments Delete
    var $del_comments_video_own;
    var $del_comments_blog_own;
    var $del_comments_audio_own;
    var $del_comments_pictures_own;
    var $del_comments_group_own;
    var $del_comments_profile_own;

    //Own Content Edit
    var $edit_details_member_own;
    var $edit_details_video_own;
    var $edit_details_audio_own;
    var $edit_details_album_own;
    var $edit_details_picture_own;
    var $edit_details_blog_own;

    //Own Content Delete
    var $del_blog_own;
    var $del_video_own;
    var $del_audio_own;
    var $del_picture_own;
    var $del_album_own;

    //Others Coments Edit
    var $edit_comments_video;
    var $edit_comments_blog;
    var $edit_comments_audio;
    var $edit_comments_pictures;
    var $edit_comments_group;
    var $edit_comments_profile;

    //Others Coments Delete
    var $del_comments_video;
    var $del_comments_blog;
    var $del_comments_audio;
    var $del_comments_pictures;
    var $del_comments_group;
    var $del_comments_profile;


    //Others Content Edit
    var $edit_details_member;
    var $edit_details_video;
    var $edit_details_audio;
    var $edit_details_album;
    var $edit_details_picture;
    var $edit_details_blog;

    //Others Content Delete
    var $del_blog;
    var $del_video;
    var $del_audio;
    var $del_picture;
    var $del_album;


    ////////////////////////////////////
    //Class Loader - sets all the var's
    ////////////////////////////////////
    function LoadPermissions($user_id, $content_id, $content_type)
    {

        if (@is_numeric($user_id))
        {
            $this->user_id = $user_id; //use the user_id specified in the setting of object
        } else
        {

            if (@is_numeric($_SESSION["user_id"]))
            {
                $this->user_id = $_SESSION["user_id"]; //use the user_id specified in the setting of object
            } else
            {
                $this->user_id = 0; //guest etc (return Zero permissions
                $this->edit_on = 0;
                $this->delete_on = 0;
                $permissions_array = array('edit_on' => 0, 'delete_on' => 0);
                return $permissions_array; //return zero permission and exit
            }

        }
        //continue
        $this->content_id = $content_id;
        $this->content_type = $content_type;

        //get users account type
        $sql = "SELECT * FROM member_profile WHERE user_id = $this->user_id";
        $query = @mysql_query($sql);

        //error control
        if (@mysql_num_rows($query) == 0)
        {
            $this->error_exit = true;

            $this->edit_on = 0;
            $this->delete_on = 0;
            $permissions_array = array('edit_on' => 0, 'delete_on' => 0);
            return $permissions_array; //return zero permission and exit
        }

        $result = @mysql_fetch_array($query);
        $user_group = $result['user_group'];
        $this->user_group = $user_group;

        //get user permissions
        $sql = "SELECT * FROM permissions WHERE user_group = '$user_group'";
        $query = @mysql_query($sql);
        $result = @mysql_fetch_array($query);
        foreach ($result as $key => $value)
        {
            //eval code, ensure to skip numerical keys etc that cause 500 error
            if (is_numeric($key) || $key == 'indexer')
            {
            } else
            {
                if ($key == 'user_group')
                {
                    $code = '$this->' . $key . '=\'' . $value . '\';';
                } else
                {
                    $code = '$this->' . $key . '=' . $value . ';';
                }
                eval($code);
            }
        }
        //debug outbut
        //print_r(get_defined_vars());
    }

    ///////////////////////////////////////////////
    // Check permissions for various content items
    ///////////////////////////////////////////////

    function CheckPermissions()
    {

        //error control
        if ($this->user_id == 0 || $this->error_exit == true || !is_numeric($this->
            content_id) || !is_numeric($this->user_id))
        {
            ; //guest etc
            $this->edit_on = 0;
            $this->delete_on = 0;
            $permissions_array = array('edit_on' => 0, 'delete_on' => 0);
            return $permissions_array; //return zero permission and exit
        }

        /////////////////////////
        //Each type of comment SQL
        /////////////////////////

        switch ($this->content_type)
        {

                //comments casing
                /////////////////
            case 'video_comments':
                $sql = "SELECT * FROM videocomments WHERE indexer = $this->content_id";
                $runcheck = 1; //just the particular check to run after this switch()
                $item_edit = 'edit_comments_video';
                $item_edit_own = 'edit_comments_video_own';
                $item_delete = 'del_comments_video';
                $item_delete_own = 'del_comments_video_own';
                break;

            case 'videocomments_replys':
                $sql = "SELECT * FROM videocomments_replys WHERE indexer = $this->content_id";
                $runcheck = 1; //just the particular check to run after this switch()
                $item_edit = 'edit_comments_video';
                $item_edit_own = 'edit_comments_video_own';
                $item_delete = 'del_comments_video';
                $item_delete_own = 'del_comments_video_own';
                break;


            case 'audio_comments':
                $sql = "SELECT * FROM audiocomments WHERE indexer = $this->content_id";
                $runcheck = 1;
                $item_edit = 'edit_comments_audio';
                $item_edit_own = 'edit_comments_audio_own';
                $item_delete = 'del_comments_audio';
                $item_delete_own = 'del_comments_audio_own';
                break;

            case 'audio_comments_replies':
                $sql = "SELECT * FROM audiocomments_replys WHERE indexer = $this->content_id";
                $runcheck = 1;
                $item_edit = 'edit_comments_audio';
                $item_edit_own = 'edit_comments_audio_own';
                $item_delete = 'del_comments_audio';
                $item_delete_own = 'del_comments_audio_own';
                break;

            case 'imagecomments':
                $sql = "SELECT * FROM imagecomments WHERE indexer = $this->content_id";
                $runcheck = 1;
                $item_edit = 'edit_comments_pictures';
                $item_edit_own = 'edit_comments_pictures_own';
                $item_delete = 'del_comments_pictures';
                $item_delete_own = 'del_comments_pictures_own';
                break;

            case 'imagecomments_replys':
                $sql = "SELECT * FROM imagecomments_replys WHERE indexer = $this->content_id";
                $runcheck = 1;
                $item_edit = 'edit_comments_pictures';
                $item_edit_own = 'edit_comments_pictures_own';
                $item_delete = 'del_comments_pictures';
                $item_delete_own = 'del_comments_pictures_own';
                break;


            case 'blog_comments': //which is blog_replys but calling it "comments" to avoid confusion
                $sql = "SELECT * FROM blog_replys WHERE indexer = $this->content_id";
                $item_edit = 'edit_comments_blog';
                $item_edit_own = 'edit_comments_blog_own';
                $item_delete = 'del_comments_blog';
                $item_delete_own = 'del_comments_blog_own';
                $runcheck = 1;
                break;

            case 'group_comments':
                $sql = "SELECT * FROM group_comments WHERE indexer = $this->content_id";
                $item_edit = 'edit_comments_group';
                $item_edit_own = 'edit_comments_group_own';
                $item_delete = 'del_comments_group';
                $item_delete_own = 'del_comments_group_own';
                $runcheck = 1;
                break;

            case 'profile_comments':
                $sql = "SELECT * FROM profilecomments WHERE indexer = $this->content_id";
                $item_edit = 'edit_comments_profile';
                $item_edit_own = 'edit_comments_profile_own';
                $item_delete = 'del_comments_profile';
                $item_delete_own = 'del_comments_profile_own';
                $runcheck = 1;
                break;


                //actual content casing
                ////////////////////////
            case 'videos': //tested working
                $sql = "SELECT * FROM videos WHERE indexer = $this->content_id";
                $runcheck = 2;
                $item_edit = 'edit_details_video';
                $item_edit_own = 'edit_details_video_own';
                $item_delete = 'del_video';
                $item_delete_own = 'del_video_own';
                break;

                // Audio
            case 'audios':
                $sql = "SELECT * FROM audios WHERE indexer = $this->content_id";
                $item_edit = 'edit_details_audio';
                $item_edit_own = 'edit_details_audio_own';
                $item_delete = 'del_audio';
                $item_delete_own = 'del_audio_own';
                $runcheck = 2;
                break;

                // Audio
            case 'blogs':
                $sql = "SELECT * FROM blogs WHERE indexer = $this->content_id";
                $item_edit = 'edit_details_blog';
                $item_edit_own = 'edit_details_blog_own';
                $item_delete = 'del_blog';
                $item_delete_own = 'del_blog_own';
                $runcheck = 2;
                break;


                // PICTURES
            case 'images':
                $sql = "SELECT * FROM images WHERE indexer = $this->content_id";
                $item_edit = 'edit_details_picture';
                $item_edit_own = 'edit_details_picture_own';
                $item_delete = 'del_picture';
                $item_delete_own = 'del_picture_own';
                $runcheck = 2;
                break;

                // PICTURES
            case 'image_album':
                $sql = "SELECT * FROM image_galleries WHERE gallery_id = $this->content_id";
                $item_edit = 'edit_details_album';
                $item_edit_own = 'edit_details_album_own';
                $item_delete = 'del_album';
                $item_delete_own = 'del_album_own';
                $runcheck = 2;
                break;

                //members profile
            case 'member_profile': //tested working
                $sql = "SELECT * FROM member_profile WHERE user_id = $this->content_id";
                $runcheck = 2;
                $item_edit = 'edit_details_member';
                $item_edit_own = 'edit_details_member_own';
                break;
        }

        /////////////////////////////////////////////////////////////////////////////////////////
        //Check Number 1
        // video_comments  - audio_comments - blog_comments - picture_comments - group_comments
        // profile_comments
        // video_comments_replys - blog_comments_replys - audio_comments_replys - videos - audios
        // pictures - blogs
        /////////////////////////////////////////////////////////////////////////////////////////

        if ($runcheck == 1 || $runcheck == 2)
        {

            //reset things
            $delete_content = 0;
            $edit_content = 0;
            $i_am_owner = false;
            $query = @mysql_query($sql);
            $result = @mysql_fetch_array($query);

            //error control
            if (@mysql_num_rows($query) == 0)
            {
                $this->error_exit = true;
                $this->edit_on = 0;
                $this->delete_on = 0;
                $permissions_array = array('edit_on' => $edit_content, 'delete_on' => $delete_content);
                return $permissions_array;
            }

            //get owners details from mysql results
            $owners_id = $result['by_id']; //as used in comments tables generally

            if ($runcheck == 2)
            {
                $owners_id = $result['user_id']; //as used in video, audio ... tables
                $owners_group = $result['user_group'];
            }

            //check if I OWN media
            if ($owners_id == $this->user_id)
            {
                $i_am_owner = true;
            }

            //_____________________________________________________________________________________________________________________________________
            //____Actual Checks____________________________________________________________________________________________________________________


            //(1)      //Check global EDIT permissions first (where I am owner or not does not matter)
            ///////////////////////////////////////////////////////////////////////////////
            if ($this->$item_edit == 1)
            {
                $edit_content = 1;
            }

            //(2)      //If I dont have global EDIT permissions, check if I have owners permissions
            ///////////////////////////////////////////////////////////////////////
            elseif ($this->$item_edit_own == 1 && $i_am_owner == true)
            {
                $edit_content = 1;
            }


            //(3)      //Skip Delete checks for Profile as these are only for siteadmin panel
            ///////////////////////////////////////////////////////////////////////
            if ($this->content_type == 'member_profile')
            {
                $delete_content = 0;
            } else
            {


                //(4)      //Checl global DELETE  first (where I am owner or not does not matter)
                ///////////////////////////////////////////////////////////////////////
                if ($this->$item_delete == 1)
                {
                    $delete_content = 1;
                }


                //(5)      //If I dont have global DELETE permissions, check if I have owners permissions
                //////////////////////////////////////////////////////////////////////////////
                elseif ($this->$item_delete_own == 1 && $i_am_owner == true)
                {
                    $delete_content = 1;
                }
            }


            //(6)      // Check if I am Admin as only Admin can EDIT "Admins Profile"
            //////////////////////////////////////////////////////////////////////////////
            if ($this->user_group != 'admin' && $this->content_type == 'member_profile' && $owners_group =='admin')
            { //i.e. logged in user is not admin, so cant edit admins profile
                $edit_content = 0;
            }

            //write to array
            ///////////////
            $permissions_array = array('edit_on' => $edit_content, 'delete_on' => $delete_content);
            $this->edit_on = $edit_content;
            $this->delete_on = $delete_content;
            //print_r(get_defined_vars());  //debug
            return $permissions_array;
        }
    }

    ////////////////////////////
    //SECURITY CHECK FOR ACTION
    ////////////////////////////

    function ActionsCheck($type)
    { //type can be ( edit, delete, both )
        global $lang_error, $lang_permissions_denied, $lang_an_error_has_occ;
        $error_m = '<div align="center">&nbsp;<p>&nbsp;</p><table width="556" id="table1" style="border: 1px solid #FF3300; " cellspacing="0" cellpadding="0" bgcolor="#FFDDDD"><tr><td bgcolor="#FFECEC"><table border="0" width="100%" id="table2" cellspacing="0" cellpadding="0"><tr><td width="30"><img border="0" src="images/icon_info.gif"></td><td height="20"><p align="center"><font face="Arial" size="2">' .
            $lang_error . '!! - ' . $lang_permissions_denied .
            '</font></td></tr></table></td></tr></table></div><p align="center"><font color="#FF0000"><a href="index.php"><font color="#000080" face="Arial">' .
            $lang_retry . '</font></a></font></p>';

        //GENERAL CHECK
        if ($this->edit_on == 0 && $this->delete_on == 0)
        {
            echo $error_m;
            die();
        }

        //check editing only permissions
        if ($this->edit_on == 0 && $type == 'edit')
        {
            echo $error_m;
            die();
        }

        //check deleting only permissions
        if ($this->delete_on == 0 && $type == 'delete')
        {
            echo $error_m;
            die();
        }

        //check both permissions
        if ($type == 'both')
        {
            if ($this->edit_on == 0 || $this->delete_on == 0)
            {
                echo $error_m;
                die();
            }
        }

        //for security, incase there is an error in the script calling this
        if ($type == 'both' || $type == 'edit' || $type == 'delete')
        {
            //do nothing
        } else
        {
            echo $lang_an_error_has_occ;
            die();
        }

    }

    ///////////////////
    // RETURN VARS
    ///////////////////

    function ReturnVars($var)
    {

        return $this->$var;

    }

}


//________________________________________________________________________________________________________________________________________
//___VALID CHECK TYPES_____________________________________________________________________________________________________________________
/*

//Commets
/////////
video_comments
audio_comments
imagecomments
blog_comments  (blog replies)
group_comments
profile_comments

//Comment Replies
////////////////
audio_comments_replies
imagecomments_replys
videocomments_replys


//Content
////////
videos
audios
blogs  //this is really a comment but will treat as "content" to avoind confusion
images

//Others
////////
image_album
member_profile


*/


//________________________________________________________________________________________________________________________________________
//___EXAMPLE USAGE________________________________________________________________________________________________________________________
/*

(1)
//Use this in loading page such as play.php
//$edit_on etc can be whatver you are using in hmtl to show/hide links
$usercheck = new LoadPermissions('',1,'video_comments');
$usercheck->CheckPermissions();
$edit_on = $usercheck->ReturnVars('edit_on');
$delete_on = $usercheck->ReturnVars('delete_on');



(2)
//Use this on actual action pade such as edit.php
//this returns error and dies if user does not have permissions
$usercheck = new LoadPermissions('',$id,'videos');  //($user_id, $content_id, $content_type);
$usercheck->CheckPermissions();
$usercheck->ActionsCheck('edit'); //Can be ( edit, delete, both )




EXAMPLE OUTPUT
///////////////

array ('edit_on'=> 0, 'del_on'=>1);


*/

?>