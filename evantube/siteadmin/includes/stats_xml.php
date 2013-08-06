<?php

include_once ("../../classes/config.php");
include_once ("inc.stats.php");
include_once ("functions.php");
include_once ('login_check.php');
//show line selector or not

$feed = $_GET['feed'];


//Uplaoded/new items>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($feed == 1){

$xmlstats = $_SESSION['statsxml'];
//render XML output
//////////////////
echo "
<chart>
<chart_type>column</chart_type>
<chart_label position='outside' size='12' color='FF4400' alpha='100' />
<chart_grid_h thickness='1' color='FF0000' alpha='15' type='dashed' />   
<axis_value 
            font='arial' 
            bold='true' 
            size='12' 
            color='000000' 
            />

   <axis_category 
            font='arial' 
            bold='true' 
            size='14' 
            color='000000' 
            />

   <legend layout='vertical' 
           font='arial'
           bold='true'
           size='12'
           color='000000'
           alpha='90'
           /> 


   <chart_data>
      <row>
         <null/>
         <string>Jan</string>
         <string>Feb</string>
         <string>Mar</string>
         <string>Apr</string>
         <string>May</string>
         <string>Jun</string>
         <string>Jul</string>
         <string>Aug</string>
         <string>Sep</string>
         <string>Oct</string>
         <string>Nov</string>
         <string>Dec</string>

      </row>
$xmlstats
   </chart_data>
   
</chart>";
}


//Compare Stats>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($feed == 2){
$xmlstats = $_SESSION['statsxml_compare'];
//show line selector or not
$selector = "   <chart_guide horizontal='true'
                vertical='false'
                thickness='1' 
                color='ff4400' 
                alpha='75' 
                type='dashed' 
                
                 
                radius='8'
                fill_alpha='0'
                line_color='ff4400'
                line_alpha='75'
                line_thickness='2'
             
                size='20'
                text_color='ffffff'
                background_color='ff4400'
                text_h_alpha='90'
                text_v_alpha='90' 
                />";


//render XML output
//////////////////
echo "
<chart>

<chart_type>column</chart_type>

<chart_grid_h thickness='1' color='FF0000' alpha='15' type='dashed' />   

<axis_value 
            font='arial' 
            bold='true' 
            size='12' 
            color='000000' 
            />

   <axis_category 
            font='arial' 
            bold='true' 
            size='14' 
            color='000000' 
            />

   <legend layout='horizontal' 
           font='arial'
           bold='true'
           size='12'
           color='000000'
           alpha='90'
           /> 
		               
$selector

   <chart_data>
      <row>
         <null/>
         <string>Jan</string>
         <string>Feb</string>
         <string>Mar</string>
         <string>Apr</string>
         <string>May</string>
         <string>Jun</string>
         <string>Jul</string>
         <string>Aug</string>
         <string>Sep</string>
         <string>Oct</string>
         <string>Nov</string>
         <string>Dec</string>

      </row>
$xmlstats
   </chart_data>
   
</chart>";
}



//last Years Stats - Uloaded/new>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($feed == 3){
$xmlstats = $_SESSION['statsxml_previous_year'];
//show line selector or not
$selector = "   <chart_guide horizontal='true'
                vertical='false'
                thickness='1' 
                color='ff4400' 
                alpha='75' 
                type='dashed' 
                
                 
                radius='8'
                fill_alpha='0'
                line_color='ff4400'
                line_alpha='75'
                line_thickness='2'
             
                size='20'
                text_color='ffffff'
                background_color='ff4400'
                text_h_alpha='90'
                text_v_alpha='90' 
                />";

//render XML output
//////////////////
echo "
<chart>

<chart_type>column</chart_type>

<chart_label position='outside' size='8' color='FF4400' alpha='100' />

<chart_grid_h thickness='1' color='FF0000' alpha='15' type='dashed' />   

<axis_value 
            font='arial' 
            bold='false' 
            size='12' 
            color='000000' 
            />

   <axis_category 
            font='arial' 
            bold='false' 
            size='12' 
            color='000000' 
            />
   <series_color>
      <color>5ba0d2</color>
   </series_color>

$selector

   <chart_data>
      <row>
         <null/>
         <string>Jan</string>
         <string>Feb</string>
         <string>Mar</string>
         <string>Apr</string>
         <string>May</string>
         <string>Jun</string>
         <string>Jul</string>
         <string>Aug</string>
         <string>Sep</string>
         <string>Oct</string>
         <string>Nov</string>
         <string>Dec</string>

      </row>
$xmlstats
   </chart_data>
   
</chart>";
}


//Viewed Items>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($feed == 4){

$xmlstats_viewed = $_SESSION['statsxml_viewed'];
//render XML output
//////////////////
echo "
<chart>
<chart_type>column</chart_type>
<chart_label position='outside' size='12' color='FF4400' alpha='100' />
<chart_grid_h thickness='1' color='FF0000' alpha='15' type='dashed' />   
<axis_value 
            font='arial' 
            bold='true' 
            size='12' 
            color='000000' 
            />

   <axis_category 
            font='arial' 
            bold='true' 
            size='14' 
            color='000000' 
            />

   <legend layout='vertical' 
           font='arial'
           bold='true'
           size='12'
           color='000000'
           alpha='90'
           /> 

   <series_color>
      <color>d53434</color>
   </series_color>

   <chart_data>
      <row>
         <null/>
         <string>Jan</string>
         <string>Feb</string>
         <string>Mar</string>
         <string>Apr</string>
         <string>May</string>
         <string>Jun</string>
         <string>Jul</string>
         <string>Aug</string>
         <string>Sep</string>
         <string>Oct</string>
         <string>Nov</string>
         <string>Dec</string>

      </row>
$xmlstats_viewed
   </chart_data>
   
</chart>";
}


//last Years Stats - Viewed/watched>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if ($feed == 5){
$xmlstats_viewed = $_SESSION['statsxml_previous_year_viewed'];
//show line selector or not
$selector = "   <chart_guide horizontal='true'
                vertical='false'
                thickness='1' 
                color='ff4400' 
                alpha='75' 
                type='dashed' 
                
                 
                radius='8'
                fill_alpha='0'
                line_color='ff4400'
                line_alpha='75'
                line_thickness='2'
             
                size='20'
                text_color='ffffff'
                background_color='ff4400'
                text_h_alpha='90'
                text_v_alpha='90' 
                />";

//render XML output
//////////////////
echo "
<chart>

<chart_type>column</chart_type>

<chart_label position='outside' size='8' color='FF4400' alpha='100' />

<chart_grid_h thickness='1' color='FF0000' alpha='15' type='dashed' />   

<axis_value 
            font='arial' 
            bold='false' 
            size='12' 
            color='000000' 
            />

   <axis_category 
            font='arial' 
            bold='false' 
            size='12' 
            color='000000' 
            />
   <series_color>
      <color>c99b51</color>
   </series_color>

$selector

   <chart_data>
      <row>
         <null/>
         <string>Jan</string>
         <string>Feb</string>
         <string>Mar</string>
         <string>Apr</string>
         <string>May</string>
         <string>Jun</string>
         <string>Jul</string>
         <string>Aug</string>
         <string>Sep</string>
         <string>Oct</string>
         <string>Nov</string>
         <string>Dec</string>

      </row>
$xmlstats_viewed
   </chart_data>
   
</chart>";
}
?>