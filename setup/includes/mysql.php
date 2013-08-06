<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//

class QuickMysql {

    var $last_error;
    var $last_query;
    public $obj_status = true;
    var $result_status = false;//tells us if sql produced any result
    var $obj_debug_mode = 0;
    var $global_debug_mode = 0;
    public $pagination_html = '';
    public $pagination_simple_html = '';

    var $sql_rows = 0;
    var $sql_fetched_array = array();

    var $lang_array = array('incomplete_mysq' =>
        'Incomplete MysQL database connection information');

    //____lets start - Connect to dbase___________________________

    function QuickMysql($db_name ='',$db_username='',$db_password='',$db_host='') {//debug is optional

        //are we in debug mode
        global $quickmysql_debug;
        global $config;// >>STAND ALONE USAGE <<, set this in calling file

        $this->obj_debug_mode = $quickmysql_debug;
        $this->global_debug_mode = $config['debug_mode'];// >>STAND ALONE USAGE <<, set this in calling file
        //chcek all needed vars
        if($db_name == '' || $db_username == '' || $db_host == '') {
            $this->last_error = 'Incomplete Mysql Information';//incomplete mysl information
            $this->obj_status = false;
            $this->PrintDebug();
            return false;
        }

        //close any mysql connection
        @mysql_close();

        //open new connection
        $new_connection = @mysql_connect($db_host,$db_username,$db_password);
        if(!$new_connection) {
            $this->last_query = 'mysql_connect';
            $this->last_error = @mysql_error();
            $this->obj_status = false;
            $this->PrintDebug();
            return false;
        }
        else {
            @mysql_select_db($db_name,$new_connection);
        }

        //checks on errors
        if(@mysql_error()) {
            $this->last_error = @mysql_error();
            $this->obj_status = false;
            $this->PrintDebug();
            return false;
        }
        else {
            return true;
        }

    }

    //____MYSQ INSERT________________________________
    /* Insert rows into mysql */
    /* returns true on success */

    public function InsertRecord($sql_query) {

        $this->obj_status = true;//reset

        $this->last_query = $sql_query;
        //check if any sql has been sent
        @mysql_query($sql_query);

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
        else {
            return true;
        }
    }

    //____MYSQ SELECT ARRAY________________________________
    /* Select rows from mysql */
    /* returns an array on success */

    public function SelectRecord($sql_query) {

        $this->last_query = $sql_query;
        $this->obj_status = true;
        $this->result_status = false;//assume results are false. check later

        //execute sql
        $result = @mysql_fetch_array(@mysql_query($sql_query));

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }

        //if array is returned
        if(!empty($result)) {
            $this->result_status = true;
            $this->sql_fetched_array = $result;
            return $this->sql_fetched_array;
        }

    }

    //____MYSQ SELECT ARRAY________________________________
    /* Select rows from mysql */
    /* returns an array on success */

    public function SelectRecordLoop($sql_query) {

        $this->last_query = $sql_query;
        $this->obj_status = true;
        $this->result_status = false;//assume results are false. check later

        //execute sql
        $query = @mysql_query($sql_query);
        $loop = array();
        while($result = @mysql_fetch_array($query)) {
            $loop[] = $result;
        }

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }

        //if array is returned
        if(!empty($loop)) {
            $this->result_status = true;
            $this->sql_fetched_array = $loop;
            return $this->sql_fetched_array;
        }else{
            return array(); //an empty array (needed for TBS block merge etc)
        }

    }




    //____PAGINATED MYSQ SELECT ARRAY________________________________
    public function PaginatedRecordLoop($sql_query) {

        global $paginate, $pagi_addurl_pre, $pagi_addurl_post;//set in calling page as var

//take all the vars from $paginate array()
$pagi_limit = $paginate[0];
$pagi_current_page = $paginate[1];
$pagi_lang_next = $paginate[2];
$pagi_lang_previous = $paginate[3];


        //get pagination limits. if not default to 10.
        if($pagi_limit == '' || $pagi_limit <= 0 || !is_numeric($pagi_limit)) {
            $pagi_limit = 10;
        }

        //get current page. if not default to page 1.
        if(!is_numeric($pagi_current_page)) {
            $pagi_current_page = 1;
        }

        //Start by counting rows returned by sql
        $rows = @mysql_num_rows(mysql_query($sql_query));

        //Total page sets
        $pagi_total = ceil($rows / $pagi_limit);

        //error control
        if($pagi_current_page > $pagi_total) {
            $pagi_current_page = 1;//default
        }

        //Sql starting page
        $pagi_sql_starting = $pagi_current_page * $pagi_limit - ($pagi_limit);


        //Previous pages html
        $prev_page = $pagi_current_page - 1;
        $html_previous = '<li><a href="'.$pagi_addurl_pre.'page='.$prev_page.$pagi_addurl_post.'">'.$pagi_lang_previous.'</a></li>';
        if($prev_page <= 0 || $prev_page == $pagi_total) {
            $html_previous = '';
        }

        //Next pages html
        $next_page = $pagi_current_page + 1;
        $html_next = '<li><a href="'.$pagi_addurl_pre.'page='.$next_page.$pagi_addurl_post.'">'.$pagi_lang_next.'</a></li>';
        if($next_page > $pagi_total) {
            $html_next = '';
        }

        //lets create the rest of the paginatation links
        for($count = 1; $count <= $pagi_total; $count += 1) {
            $html_pages .= '<li><a href="'.$pagi_addurl_pre.'page='.$count.$pagi_addurl_post.'"> '.$count.' </a></li>';
        }

        //_____Final build (pagination full)______________
        $html_output = $html_previous.$html_pages.$html_next;
        $this->pagination_html = $html_output;
    
        //_____Final build (pagination simple)______________
        $this->pagination_simple_html = $html_previous.$html_next;

        //_____Run actual SQL for results_____________       
        $sql_query = $sql_query." LIMIT $pagi_sql_starting, $pagi_limit"; //add the pagination stuff to sql
	    $query = @mysql_query($sql_query);
        $loop = array();
        while($result = @mysql_fetch_array($query)) {
            $loop[] = $result;
        }
        
        //debug
        $this->last_error = @mysql_error().'<br /><b>Additional OutPut</b> <br /> current page: '.$pagi_current_page.'<br />total rows: '.$rows.'<br />page limits: '.$pagi_limit.'<br />total pages: '.$pagi_total;
        
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }

        //if array is returned
        if(!empty($loop)) {
            $this->result_status = true;
            $this->sql_fetched_array = $loop;
            return $this->sql_fetched_array;
        }else{
            return array(); //an empty array (needed for TBS block merge etc)
        }


    }

    //____MYSQ UPDATE________________________________
    /* Insert rows into mysql */
    /* returns true on success */

    public function UpdateRecord($sql_query) {

        $this->obj_status = true;//reset

        $this->last_query = $sql_query;

        //execute sql
        @mysql_query($sql_query);

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
        else {
            return true;
        }
    }

    //____MYSQ DELETE________________________________
    /* Insert rows into mysql */
    /* returns true on success */

    public function DeleteRecord($sql_query) {

        $this->obj_status = true;//reset

        $this->last_query = $sql_query;

        //execute sql
        @mysql_query($sql_query);

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
        else {
            return true;
        }
    }

    //____MYSQ Count Rows_________________________
    /* count rows in sql*/
    /* returns rows on success / false on fail*/
    public function CountRows($sql_query) {

        $this->obj_status = true;//reset

        //reset rows
        $this->sql_rows = 0;

        $this->last_query = $sql_query;
        $rows = @mysql_num_rows(@mysql_query($sql_query));

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
        else {
            $this->sql_rows = $rows;
            return $this->sql_rows;
        }
    }
    
        //____MYSQ GENERAL________________________________
    /* general sql query */
    /* returns true on success */

    public function SqlQuery($sql_query) {

        $this->obj_status = true;//reset

        $this->last_query = $sql_query;

        //execute sql
        @mysql_query($sql_query);

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
        else {
            return true;
        }
    }

    

    //____MYSQ Record check__________________________________
    /* same as count but returns true/false if record found*/
    public function RecordExists($sql_query) {

        $this->obj_status = true;//reset

        //reset rows

        $this->last_query = $sql_query;
        $rows = @mysql_num_rows(@mysql_query($sql_query));

        //debug
        $this->last_error = @mysql_error();
        $this->PrintDebug();

        //retun true or flase
        if($rows > 0 && !@mysql_error()) {
            return true;
        }

        if($rows <= 0 && !@mysql_error()) {
            return false;
        }

        //checks on errors
        if(@mysql_error()) {
            $this->obj_status = false;
            return false;
        }
    }

    //Debug function
    private function PrintDebug() {
        if($this->obj_debug_mode == 1 || $this->global_debug_mode == 1) {

            echo 'SQL QUERY: '.$this->last_query.'</br>';
            echo 'DEBUG: '.$this->last_error.'</br>';

        }//debug mode
    }

}//QuickMysql end

/*==================================================================================================================

USAGE:
-------------

(0) //General Sql Query (for any kind of sql query)
$dbase->SqlQuery("SELECT *
                  FROM table
                  WHERE foo = '$bar'");
                  
(1) //Connect to a dbase
$quickmysql_debug = 1; (optional)
$dbase = new QuickMysql($config['db_name'], $config['db_user'], $config['db_pass'], $config['db_host']);


(2) //Selection a record
$result = $dbase->SelectRecord("SELECT *
                                FROM table
                                WHERE foo = '$bar'");
<++> result_status = TRUE when record found
<++> array is 2d - $result[0]['username'];

(3) //Select an array of records
$result = $dbase->SelectRecordLoop("SELECT * 
                                    FROM table
                                    WHERE foo = '$bar'
                                    ORDER BY foo DESC");
<++> result_status = TRUE when records found
<++> array is 2d - $result[0]['username'];


(4) //Adding a record
$dbase->InsertRecord("INSERT INTO bar (
                      foo, fooz
                      ) values (
                      '$bar', '$barz')");


(5) //counting rows
$rows = $dbase->CountRows("SELECT *
                           FROM table
                           WHERE foo = '$bar");
($row == 0)? NULL : $notification = 'A user with that email already exists'; //use it as a check



(6) //checking if a record exists
if(!$dbase->RecordExists("SELECT * FROM table WHERE foo = '$bar'")){
//do something
}
WARNING: It may be safe to use $rows above and check for a "posetive" result..as the above will work even if mysql is down


(7) Updating Record
$dbase->UpdateRecord("UPDATE table SET foo = '$bar' WHERE code = 'xyz'");
<++> result_status = TRUE (but this is just a mysql_error check - not a confirmation of update)


(8) Delete Record
$dbase->DeleteRecord("DELETE FROM table WHERE foo = '$bar'");
<++> result_status = TRUE when record updated


(9) Paginated Result
$current_page = (($_GET['page'])? $_GET['page'] : $_POST['page']);
$paginate = array(30, Sanatize($current_page), ' >>', '<< '); //(limit, current page, next, previous)
$result = $dbase->PaginatedRecordLoop("SELECT *
        							   FROM table
        							   WHERE foo=bar");
        							   
//$pagination = $dbase->pagination_html;      //Full pagination output - This is <li>'s for used in html with htmlconv=no
$pagination = $dbase->pagination_simple_html; //Simple pagination (<<previous:next>>) - This is <li>'s for used in html with htmlconv=no



//These methods all return true, so if you want to do an action on condition that no mysql errors
//example
if(!$dbase->RecordExists("SELECT user_id FROM member_profile WHERE user_name = '$user_name' OR email_address = '$email_address'")){
//do something if it returns true
}
================================================================================================================*/
?>