<?php

///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

$pagesleft = $total_pages - $current_page;

if (($current_page - 5 >= 0) && ($current_page + 5 <= $total_pages)){
	$startcounter = $current_page -5;
	$stopcounter = $current_page + 5;
}

if (($current_page - 6 >= 0) && ($current_page + 4 == $total_pages)){
	$startcounter = $current_page -6;
	$stopcounter = $current_page + 4;
}

if (($current_page - 7 >= 0) && ($current_page + 3 == $total_pages)){
	$startcounter = $current_page -7;
	$stopcounter = $current_page + 3;
}

if (($current_page - 8 >= 0) && ($current_page + 2 == $total_pages)){
	$startcounter = $current_page -8;
	$stopcounter = $current_page + 2;
}

if (($current_page - 9 >= 0) && ($current_page + 1 == $total_pages)){
	$startcounter = $current_page -9;
	$stopcounter = $current_page + 1;
}

if (($current_page - 10 >= 0) && ($current_page == $total_pages)){
	$startcounter = $current_page -10;
	$stopcounter = $current_page;
}

if (($current_page - 5 <= 0)){
	$startcounter = 1;
	if ($total_pages >=11) {
		$stopcounter = 11;
	} else {
		$stopcounter = $total_pages;
	}
}

// Create page lists

//clear buffer for using multiple paginations on single page (e.g. mebers profile)
$pagination_html ='';

//added $ahah_pagination var for pagination where ajax is used (i.e. members profile)
for ($counter = $startcounter; $counter <= $stopcounter; $counter += 1) {

	if ( $counter == "" )
		$pagination_html = $pagination_html.'';
	else
		$pagination_html = $pagination_html.'<li><a href="'.$url.$additional_url_variable.$counter.$ahah_pagination.'">'.$counter.'</a></li>';
    	}

	//create << previous and >>next list
    	if ($pl == true) {
    		$pl = '<li><a href="'.$url.$additional_url_variable.$prev_page.$ahah_pagination.'">'.$config['pagination_previous'].'</a></li>';// <<  previous and next
		} else {
    		$pl = '';// <<  no previous and next
    	}

    	if ($nl == true) {
      	$nl = '<li><a href="'.$url.$additional_url_variable.$next_page.$ahah_pagination.'">'.$config['pagination_next'].'</a></li>';// >>  previous and next
    	} else {
      	$nl = '';// >>  no previous and next
    	}

	//__________________________________________________________________________________________________________________________________________
	//______Hide Pages OR show only <<Previous  Next>>__________________________________________________________________________________________

	if( $hide_numbering == true ) {

      	//strip out individual list to shopw in single <li></li>
    		$remove_list_tags	= array('<li>', '</li>');
    		$pl_strip 		= str_replace($remove_list_tags, '', $pl);
    		$nl_strip 		= str_replace($remove_list_tags, '', $nl);
    		$show_pages 	= "<li>$pl_strip&nbsp;&nbsp;&nbsp;$nl_strip</li>";

    	} else {

    		//Show full pagination lists
    		$show_pages = $pl.$pagination_html.$nl;
    		//note $total_pages is not same as one used in fucntion above, i have high jacked is because already in array
	}
?>