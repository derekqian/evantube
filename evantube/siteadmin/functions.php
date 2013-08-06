<?php



//Get Total Folder Sizes
////////////////////////

function foldersize($path) {

	$total_size = 0;
    	$files = scandir($path);

    	if( !function_exists('scandir') ) {
    		function scandir($path, $sorting_order = 0) {
        		$dh  = opendir($path);
        		while( false !== ($filename = readdir($dh)) ) {
            		$files[] = $filename;
        		}
       	 	if( $sorting_order == 0 ) {
            		sort($files);
        		} else {
            		rsort($files);
        		}
        	return($files);
    		}
	}



    	foreach($files as $t) {
      	if (is_dir($t)) { // In case of folder
            	if ($t<>"." && $t<>"..") { // Exclude self and parent folder
                		$size = foldersize($path . "/" . $t);
                		// print("Dir - $path/$t = $size<br>\n");
                		$total_size += $size;
            	}
        	}
        	else { // In case of file
            	$size = filesize($path . "/" . $t);
            	// print("File - $path/$t = $size<br>\n");
            	$total_size += $size;
        	}
    	}
    	return $total_size;
}

?>