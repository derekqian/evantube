//******************************************************************************************************
//   ATTENTION: THIS FILE HEADER MUST REMAIN INTACT. DO NOT DELETE OR MODIFY THIS FILE HEADER.
//
//   Name: uploader_bar.js
//   Revision: 1.5
//   Date: 17/03/2007 6:27PM
//   Link: http://uber-uploader.sourceforge.net 
//   Initial Developer: Peter Schmandra  http://www.webdice.org
//
//   Licence:
//   The contents of this file are subject to the Mozilla Public
//   License Version 1.1 (the "License"); you may not use this file
//   except in compliance with the License. You may obtain a copy of
//   the License at http://www.mozilla.org/MPL/
// 
//   Software distributed under the License is distributed on an "AS
//   IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
//   implied. See the License for the specific language governing
//   rights and limitations under the License.
//
//***************************************************************************************************************

var upload_range = 1;                                    
var total_upload_size;
var get_status_speed;
var get_status_url;
var get_data_loop = true;
var seconds = 0;
var minutes = 0;
var hours = 0;
var info_width = 0;
var info_bytes = 0;
var info_time_width = 500;
var info_time_bytes = 15;
var cedric_hold = true;

// Check the file format before uploading
function checkFileNameFormat(){
	if(check_file_name_format == false){ return false; }
	
	for(var i = 0; i < upload_range; i++){
  		if(document.form_upload.elements['upfile_' + i].value != ""){
  			var string = document.form_upload.elements['upfile_' + i].value;
			var num_of_last_slash = string.lastIndexOf("\\");

			if(num_of_last_slash < 1){ num_of_last_slash = string.lastIndexOf("/"); }

			var file_name = string.slice(num_of_last_slash + 1, string.length);
			var re = /^[\w][\w\.\ \-]{1,64}$/i;   
				
			if(!re.test(file_name)){	
  				alert("Sorry, uploading files in this format is not allowed. Please ensure your file names follow this format. \n\n1. Entire file cannot exceed 64 characters\n2. Format should be filename.extension or filename\n3. Legal characters are 1-9, a-z, A-Z, '_', '-'\n");
  				return true;
  			}
  		}
  	}
	return false;
}

// Check for illegal file extentions
function checkDisallowFileExtensions(){
	if(check_disallow_extensions == false){ return false; }
  	
  	for(var i = 0; i < upload_range; i++){
  		if(document.form_upload.elements['upfile_' + i].value != ""){
  			if(document.form_upload.elements['upfile_' + i].value.match(disallow_extensions)){
  				var string = document.form_upload.elements['upfile_' + i].value;
				var num_of_last_slash = string.lastIndexOf("\\");

				if(num_of_last_slash < 1){ num_of_last_slash = string.lastIndexOf("/"); }

				var file_name = string.slice(num_of_last_slash + 1, string.length);
				var file_extension = file_name.slice(file_name.indexOf(".")).toLowerCase(); 
  				
  				alert('Sorry, uploading a file with the extension "' + file_extension + '" is not allowed.');
  				return true;
  			}
  		}
  	}
	return false;
}

// Check for legal file extentions
function checkAllowFileExtensions(){
	if(check_allow_extensions == false){ return false; }
	
  	for(var i = 0; i < upload_range; i++){
  		if(document.form_upload.elements['upfile_' + i].value != ""){
  			if(!document.form_upload.elements['upfile_' + i].value.match(allow_extensions)){
  				var string = document.form_upload.elements['upfile_' + i].value;
				var num_of_last_slash = string.lastIndexOf("\\");

				if(num_of_last_slash < 1){ num_of_last_slash = string.lastIndexOf("/"); }

				var file_name = string.slice(num_of_last_slash + 1, string.length);
				var file_extension = file_name.slice(file_name.indexOf(".")).toLowerCase(); 
  				
  				alert('Sorry, uploading a file with the extension "' + file_extension + '" is not allowed.');
  				return true;
  			}
  		}
  	}
	return false;
}

// Make sure the user selected at least one file
function checkNullFileCount(){
  	if(check_null_file_count == false){ return false; }
  	
  	var null_file_count = 0;
  	
  	for(var i = 0; i < upload_range; i++){
  		if(document.form_upload.elements['upfile_' + i].value == ""){ null_file_count++; }
  	}
  	
  	if(null_file_count == upload_range){
		alert("Please Choose A File To Upload.");
		return true;
  	}
  	else{ return false; }
}

// Make sure the user is not uploading duplicate files
function checkDuplicateFileCount(){
	if(check_duplicate_file_count == false){ return false; }
  	
	var duplicate_flag = false;
	var file_count = 0;
	var duplicate_msg = "Duplicate Upload Files Detected.\n\n";
	var file_name_array = new Array();
        
	for(var i = 0; i < upload_range; i++){
		if(document.form_upload.elements['upfile_' + i].value != ""){
  			var string = document.form_upload.elements['upfile_' + i].value;
			var num_of_last_slash = string.lastIndexOf("\\");

			if(num_of_last_slash < 1){ num_of_last_slash = string.lastIndexOf("/"); }

			var file_name = string.slice(num_of_last_slash + 1, string.length);
			            
			file_name_array[i] = file_name;
  		}
  	}
  	
  	var num_files = file_name_array.length;
       
	for(var i = 0; i < num_files; i++){
		for(var j = 0; j < num_files; j++){
			if(file_name_array[i] == file_name_array[j] && file_name_array[i] != null){ file_count++; }
		}
		if(file_count > 1){
			duplicate_msg += 'Duplicate file "' + file_name_array[i] + '" detected in slot ' + (i + 1) + ".\n";
			duplicate_flag = true;
		}
		file_count = 0;
	}
       
	if(duplicate_flag){ 
		alert(duplicate_msg);
		return true; 
	}
	else{ return false; }
}

// Submit the upload form
function uploadFiles(){
	if(checkFileNameFormat()){ return false; }
	if(checkDisallowFileExtensions()){ return false; }
	if(checkAllowFileExtensions()){ return false; }
	if(checkNullFileCount()){ return false; }
	if(checkDuplicateFileCount()){ return false; }
	
	var total_uploads = 0;
	
	for(var i = 0; i < upload_range; i++){
		if(document.form_upload.elements['upfile_' + i].value != ""){ total_uploads++; }
	}
	
	document.getElementById('total_uploads').innerHTML = total_uploads;
	document.form_upload.submit();
	document.getElementById('upload_button').disabled = true;
	
	iniProgressRequest();
	getElapsedTime();
	
	for(var i = 0; i < upload_range; i++){ document.form_upload.elements['upfile_' + i].disabled = true; }	
}

// Reset the file upload page 
function resetForm(){ location.href = self.location; }

// Hide the progress bar
function hideProgressBar(){ document.getElementById('progress_bar').style.display = "none"; }

// Initialize the file upload page
function iniFilePage(){
	resetProgressBar();
	
	for(var i = 0; i < upload_range; i++){ 
		document.form_upload.elements['upfile_' + i].disabled = false;
		document.form_upload.elements['upfile_' + i].value = ""; 
	}
	
	document.getElementById('progress_info').innerHTML = "";
	document.getElementById('upload_button').disabled = false;
	document.getElementById('progress_bar').style.display = "none";
	document.form_upload.reset();
}

// Reset the progress bar
function resetProgressBar(){
	get_data_loop = true;
        seconds = 0;
        minutes = 0;
        hours = 0;
        info_width = 0;
        info_bytes = 0;
        cedric_hold = true;
	
	document.getElementById('upload_status').style.width = '0px';
	document.getElementById('percent').innerHTML = '0%';
	document.getElementById('uploaded_files').innerHTML = 0;
	document.getElementById('total_uploads').innerHTML = '';
        document.getElementById('current').innerHTML = 0;
        document.getElementById('total_kbytes').innerHTML = '';
        document.getElementById('time').innerHTML = 0;
        document.getElementById('remain').innerHTML = 0;
        document.getElementById('speed').innerHTML = 0;
}

// Stop the upload
function stopUpload(){
	try{ window.stop(); }
	catch(e){
		try{ document.execCommand('Stop'); }
		catch(e){} 
	}
}

// Add one upload slot
function addUploadSlot(num){
	if(upload_range < max_upload_slots){
		if(num == upload_range){
			var up = document.getElementById('upload_slots');
			var dv = document.createElement("div");
			
			dv.innerHTML = '<input type="file" name="upfile_' + upload_range + '" size="90" onchange="addUploadSlot('+(upload_range + 1)+')">';
			up.appendChild(dv);
			upload_range++;
		}
	}
}

// Make the progress bar smooth
function smoothCedricStatus(){
	if(info_width < progress_bar_width && !cedric_hold){
		info_width = info_width + 1;
		document.getElementById('upload_status').style.width = info_width + 'px';
	}
	
	if(get_data_loop){ self.setTimeout("smoothCedricStatus()", info_time_width); }
}

// Make the bytes uploaded smooth
function smoothCedricBytes(){
	if(info_bytes < total_upload_size && !cedric_hold){
		info_bytes = info_bytes + 1;
		document.getElementById('current').innerHTML = info_bytes;
	}
	
	if(get_data_loop){ self.setTimeout("smoothCedricBytes()", info_time_bytes); }
}

// Update the Cedric progress bar values
function updateCedricStatus(stats, bytes){
	//var deviant_stat = stats + 20; //Add 5% deviation
	
	//if(deviant_stat < info_width){ cedric_hold = true; }
	//else{
	// 	cedric_hold = false;
	//	info_width = stats;
	//	info_bytes = bytes;
	//}

	cedric_hold = false;
	info_width = stats;
	info_bytes = bytes;	
}

// Get the progress of the upload
function getProgressStatus(){
	var jsel = document.createElement('SCRIPT');
	
	jsel.type = 'text/javascript';
	jsel.src = get_status_url + "&rnd_id=" + Math.random();
	
	document.body.appendChild(jsel);
	
	if(get_data_loop){ self.setTimeout("getProgressStatus()", get_status_speed); }
}

// Calculate the time spent uploading
function getElapsedTime(){
	seconds += 1;
    	
    	if(seconds == 60){
    		seconds = 0;
    		minutes += 1;
    	}
    	
    	if(minutes == 60){
    		minutes = 0;
    		hours += 1;
    	}
    	
    	var hr = "" + ((hours < 10) ? "0" : "") + hours;
    	var min = "" + ((minutes < 10) ? "0" : "") + minutes;
    	var sec = "" + ((seconds < 10) ? "0" : "") + seconds;
    	
    	document.getElementById('time').innerHTML = hr + ":" + min + ":" + sec;
    	
    	if(get_data_loop){ self.setTimeout("getElapsedTime()", 1000); }
}

// Create the AJAX request
function createRequestObject(){
	var req = false;
  	
	if(window.XMLHttpRequest){
		req = new XMLHttpRequest();
		
		if(req.overrideMimeType){ req.overrideMimeType('text/xml'); }
	} 
	else if(window.ActiveXObject){
		try{ req = new ActiveXObject("Msxml2.XMLHTTP"); }
		catch(e){
			try{ req = new ActiveXObject("Microsoft.XMLHTTP"); }
			catch(e){}
		}
	}
	
	if(!req){
		document.getElementById('progress_info').innerHTML = "Error: Your browser does not support AJAX";
		return false;
	}
	else{ return req; }	
}

// Initialize the progress bar
function iniProgressRequest(){
	var req = false;
	req = createRequestObject();
	
	if(req){
		document.getElementById('progress_info').innerHTML = "Initializing Progress Bar ...";
		req.open("GET", path_to_ini_status_script + "&rnd_id=" + Math.random(), true);
		req.onreadystatechange = function(){ iniProgressResponse(req); }; 
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
		req.send(null);
	}
}

// Initialize the progress bar
function iniProgressResponse(req){
	if(req.readyState == 4){
		if(req.status == 200){
			var xml = req.responseXML;
			
			if(xml.getElementsByTagName('error_status').item(0).firstChild.nodeValue == 1){
				document.getElementById('progress_info').innerHTML = xml.getElementsByTagName('error_msg').item(0).firstChild.nodeValue;
				if(xml.getElementsByTagName('stop_upload').item(0).firstChild.nodeValue == 1){ stopUpload(); }
			}
			else{
				get_status_speed = xml.getElementsByTagName('get_data_speed').item(0).firstChild.nodeValue;
				get_data_loop = true;
				
				if(document.form_upload.embedded_upload_results && document.form_upload.embedded_upload_results.value == 1){
					document.getElementById('upload_div').style.display = "none";
				}
				
				get_status_url = "uploader_status.php?temp_dir_sid=" + xml.getElementsByTagName('temp_dir_sid').item(0).firstChild.nodeValue + "&start_time=" + xml.getElementsByTagName('start_time').item(0).firstChild.nodeValue + "&total_upload_size=" + xml.getElementsByTagName('total_bytes').item(0).firstChild.nodeValue + "&cedric_progress_bar=" + xml.getElementsByTagName('cedric_progress_bar').item(0).firstChild.nodeValue;
					
				document.getElementById('progress_bar').style.display = "";
				document.getElementById('total_kbytes').innerHTML = Math.round(Number(xml.getElementsByTagName('total_bytes').item(0).firstChild.nodeValue / 1024)) + " ";
				document.getElementById('progress_info').innerHTML = "Upload In Progress";
				
				getProgressStatus();
				
				if(xml.getElementsByTagName('cedric_progress_bar').item(0).firstChild.nodeValue == 1){
					total_upload_size = xml.getElementsByTagName('total_bytes').item(0).firstChild.nodeValue;
					smoothCedricBytes();
					smoothCedricStatus();
				}
			}
		}
		else{

			//---Fix for Chrome and Safari---(load an animated gif instead of prgress bar)
			document.getElementById('progress_info_2').style.display="block"; //this div is found in inner_upload_video.htm


			//document.getElementById('progress_info').innerHTML = "Error: returned status code " + req.status + " " + req.statusText; 
			//stopUpload();
		}
	}
}