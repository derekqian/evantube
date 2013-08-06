<?php

//-----------------------------------------------------------------//
//
// PHPmotion Installation Wizard - Version 2
//
//-----------------------------------------------------------------//


class FormProcessor {

    public $obj_status = true;
    public $obj_error_message;

    public $result_required_fields = true;
    public $output_required_fields = array();

    public $result_required_checkboxes = true;
    public $output_required_checkboxes = array();

    public $result_format_email = true;
    public $output_format_email = array();

    public $result_field_match = true;
    public $output_field_match = array();


    //____Initialise all results___________________________
    function FormProcessor() {
        $this->SanityCheck(1);
    }


    //____Check required fields_____________________________
    function RequiredFields($required_fields) {
        $this->SanityCheck(1);
        $required = strtolower(trim($required_fields));

        //____All fields required check___
        if ($required == 'all') {
            //check each input is not blank
            foreach ($_POST as $key => $value) {
                //if blank write field name to array
                if ($value == '') {
                    $this->output_required_fields[] = $key;
                    $this->result_required_fields = false;
                }
            }

        } else {
        	
            //____Create array from comma separated____
            $fields = explode(',', $required_fields);
            //loop through each field
            foreach ($fields as $key) {
                $key = trim($key);
                if ($_POST[$key] == '' || !isset($_POST[$key])) {
                    $this->output_required_fields[] = $key;
                    $this->result_required_fields = false;
                }

            }
            
            //___if some error in using this object___
            if(empty($fields)){
                    $this->result_required_fields = false;
            }

        }

        //return
        if (!$this->result_required_fields) {
            $this->obj_status = false;
            return false;
        } else {
            return true;
        }


    }


    //____Check required Check Boxes_____________________________
    function RequiredCheckBox($required_check_box) {
        $this->SanityCheck(1);
        //____Create array from comma separated____
        $fields = explode(',', $required_check_box);
        //loop through each field
        foreach ($fields as $key) {
            $key = trim($key);
            if ($_POST[$key] == '' || !isset($_POST[$key])) {
                $this->output_required_checkboxes[] = $key;
                $this->result_required_checkboxes = false;
            }

        }
        //return
        if (!$this->result_required_checkboxes) {
            $this->obj_status = false;
            return false;
        } else {
            return true;
        }
    }


    //____Check Format of Email_____________________________
    function CheckFormatEmail($check_format_email) {
        $this->SanityCheck(1);
        //____Create array from comma separated____
        $fields = explode(',', $check_format_email);
        //loop through each field
        foreach ($fields as $key) {
            $key = trim($key);

            if (!eregi("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$",
                $_POST[$key])) {
                $this->output_format_email[] = $key;
                $this->result_format_email = false;
            }
        }
        //return
        if (!$this->result_format_email) {
            $this->obj_status = false;
            return false;
        } else {
            return true;
        }
    }


    //____Check Format of Email_____________________________
    function CheckFieldMatch($check_field_match) {
        $this->SanityCheck(1);
        //____slipt the two____
        list($key1, $key2) = split(',', $check_field_match);
        $key1 = trim($key1);
        $key2 = trim($key2);
        if ($_POST[$key1] != $_POST[$key2] || $_POST[$key1] == '' || $_POST[$key2] == '') {
            $this->output_field_match[] = "$key1 - $key2";
            $this->result_field_match = false;
            $this->obj_status = false;
            return false;
        } else {
            return true;
        }
    }


    //____Lets do a sanity check_____________________________
    private function SanityCheck($sanity_check) {
        //has a form been submitted
        if (!$_POST && $sanity_check = 1) {
            $this->obj_status = false;
            $this->obj_error_message = 'No form has been detected';
            return;
        }
    }

} //FormProcessor end


/*==================================================================================================================

DESCRIPTION:
------------
This class can be used to check a form that has been submitted.


DEPEDENCIES:
------------
none


USAGE:
-------------
//Instantiate the object
$process = new FormProcessor();

//Check required fields have been filled in (optional)
$process->RequiredFields('all'); true/false [output stored in $output_required_fields]
OR
$process->RequiredFields('surname, location, school'); true/false [output stored in $output_required_fields]

//Check if required checkboxes are ticked - comma separated (optional)
$process->RequiredCheckBox('agree, newsletter'); true/false [output stored in $output_required_checkboxes]

//Check if email is formatted in correctly (optional)
$process->CheckFormatEmail('emailaddress, anotheremail'); true/false [output stored in $output_required_checkboxes]


//Check if 2 fields match (only check 2 at a time) (optional)
$process->CheckFieldMatch('emailaddress, emailaddress_2'); true/false [output stored in $output_required_checkboxes]


EXAMPLE USAGE:
--------------
if(isset($_POST['submit'])){

$process = new FormProcessor();
//check form filled in
($process->RequiredFields('all'))? $proceed = true  : $display_notification = 'Fill in all required fields';	

//check 'checkbox' filled in
($process->RequiredCheckBox('agree'))? $proceed = true  : $display_notification = 'You must agree to our terms';

//check email format
($process->CheckFormatEmail('emailaddress'))? $proceed = true  : $display_notification = 'Inavalid email format';

//check terms
($process->CheckFieldMatch('emailaddress,emailaddress_2'))? $proceed = true : $display_notification = 'Email addresses do not match';


//Checking is a particular test passed
if($process->RequiredFields){ //the state of a method ( can be 'RequiredCheckBox', 'CheckFormatEmail' etc)
//do something here like add stuff to dabase
}

}


NOTES
-----
Always check forms in this order
1) Required fileds
2) Check boxes etc
3) Special formats
4) Field matches


DEBUG
-----
//Show general errors
echo $process->obj_error_message;

//Show fields that fail test
print_r($process->output_required_fields);

======================================================================================================================*/ ?>