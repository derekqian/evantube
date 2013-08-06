<?php


///////////////////////////////////////////////////////////////////////////////////////
// PHPmotion                                                http://www.phpmotion.com //
///////////////////////////////////////////////////////////////////////////////////////
// License: You are not to sell or distribute this software without permission       //
// Help and support please visit http://www.phpmotion.com                            //
// Copyright reserved                                                                //
///////////////////////////////////////////////////////////////////////////////////////

//script will attempt to check paths of required modules etc
error_reporting(0);

$red = '#FF0000';
$green = '#009933';
$ok = 'OK';
$fail = 'FAIL';
$not_able = 'Error';

//HEADER SECTION

//check php version
$result = phpversion();
echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="607" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="15" bgcolor="#9FBFDF">&nbsp;</td>
			<td style="border: 1px solid #9FBFDF">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#FFFFFF" id="table2">
				<tr>
					<td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" height="45" style="border-bottom: 1px solid #9FBFDF">
					<p align="center"><b>
					<font face="Arial" color="#006699" size="3">PHP MOTION 
					SERVER CHECK</font></b></td>
				</tr>
				<tr>
					<td bgcolor="#F5F5F5" width="52%">&nbsp;</td>
					<td bgcolor="#F5F5F5" width="48%">&nbsp;</td>
				</tr>
				<tr>
					<td bgcolor="#F5F5F5" width="52%">
					<p align="right"><b>
					<font face="Arial" size="2" color="#006699">Your PHP Version</font></b></td>
					<td bgcolor="#F5F5F5" width="48%"><b>
					<font face="Arial" color="#006699">&nbsp;&nbsp; 
					' . $result . '</font></b></td>
				</tr>
				<tr>
					<td bgcolor="#F5F5F5" width="52%">&nbsp;</td>
					<td bgcolor="#F5F5F5" width="48%">&nbsp;</td>
				</tr>
			</table>
			</td>
			<td width="14" bgcolor="#9FBFDF">&nbsp;</td>
		</tr>
	</table>
</div><p align="center"></p><p align="center"></p>';

//warning about test limitations

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table6">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table7">
		<tr>
			<td bgcolor="#F5F5F5" height="50">
			<font face="Arial" size="2"><u>IMPORTANT</u>: This tool <b>does not</b> 
			test if your installed <b>ffmpeg</b> and <b>mencoder</b> with the
			<font color="#FF0000"><b>correct codecs. </b></font></font></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p><p align="center"></p>';

//check php path for use in config_inc.php--------------------------------------------------------------------------------
$cmd = "which php";
$cmd2 = "whereis php";
$ok_fail = $ok;
$font = $green;
exec("$cmd 2>&1", $output);
foreach ($output as $outputline) {
}
if (ereg('which: no', $outputline)) {//try using whereis
exec("$cmd2 2>&1", $output);
foreach ($output as $outputline) {
}
$outputline = str_replace('php: ', '', $outputline); //remove extra stuff added by whereis
}
if (ereg('which: no', $outputline)) {//check if output has the words "does not exist"
    $ok_fail = $fail;
    $font = $red;
}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">PHP TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $outputline .
    '" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
    '</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//check ffmpeg for use in admin area----------------------------------------------------------------------------------
$cmd = "which ffmpeg";
$cmd2 = "whereis ffmpeg";
$ok_fail = $ok;
$font = $green;

exec("$cmd 2>&1", $output);
foreach ($output as $outputline) {
}
if (ereg('which: no', $outputline)) {//try using whereis
exec("$cmd2 2>&1", $output);
foreach ($output as $outputline) {
}
$outputline = str_replace('ffmpeg: ', '', $outputline); //remove extra stuff added by whereis
}
if (ereg('which: no', $outputline)) {//check if output has the words "does not exist"
    $ok_fail = $fail;
    $font = $red;

}

if ($outputline == "") {//check if output is blank
    $ok_fail = $not_able;
    $font = $red;
    $outputline = "This value could not be checked . Please use SSH to check";
}
echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">FFMPEG TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $outputline .
    '" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
    '</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//check flvtool2 for use in config_inc.php-----------------------------------------------------------------------------------------
$cmd = "which flvtool2";
$cmd2 = "whereis flvtool2";

$ok_fail = $ok;
$font = $green;

exec("$cmd 2>&1", $output);
foreach ($output as $outputline) {
}

if (ereg('which: no', $outputline)) {//try using whereis
exec("$cmd2 2>&1", $output);
foreach ($output as $outputline) {
}
$outputline = str_replace('flvtool2: ', '', $outputline); //remove extra stuff added by whereis
}
if (ereg('which: no', $outputline)) {//check if output has the words "does not exist"
    $ok_fail = $fail;
    $font = $red;

}

if ($outputline == "") {//check if output is blank
    $ok_fail = $not_able;
    $font = $red;
    $outputline = "This value could not be checked . Please use SSH to check";
}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">FLVTOOL2 TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $outputline .
    '" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
    '</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//check mencoder for use in config_inc.php------------------------------------------------------------------------------------------------------
$cmd = "which mencoder";
$cmd2 = "whereis mencoder";

$ok_fail = $ok;
$font = $green;

exec("$cmd 2>&1", $output);
foreach ($output as $outputline) {
}

if (ereg('which: no', $outputline)) {//try using whereis
exec("$cmd2 2>&1", $output);
foreach ($output as $outputline) {
}
$outputline = str_replace('mencoder: ', '', $outputline); //remove extra stuff added by whereis
}
if (ereg('which: no', $outputline)) {//check if output has the words "does not exist"
    $ok_fail = $fail;
    $font = $red;
}

if ($outputline == "") {//check if output is blank
    $ok_fail = $not_able;
    $font = $red;
    $outputline = "This value could not be checked . Please use SSH to check";
}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">MENCODER TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $outputline .
    '" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
    '</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//checking enable_dl-----------------------------------------------------------------------------------------------------------------------------

if ((bool)ini_get('enable_dl')) {
    $ok_fail = $ok;
    $font = $green;
    $message = "enable_dl is set correctly";
}
else {
    $ok_fail = $not_able;
    $font = $red;
    $message = "This value could not be checked . Please use phpinfo file check";

}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">ENABLE_DL TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $message .
'" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
'</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//checking safe mode-----------------------------------------------------------------------------------------------------------------------------

if (!(bool)ini_get('safe_mode')) {
    $ok_fail = $ok;
    $font = $green;
    $message = "safe_mode is set correctly";
}
else {
    $ok_fail = $not_able;
    $font = $red;
    $message = "safe_mode must be turned OFF in php.ini";
}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">SAFE_MODE TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $message .
'" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
'</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>';

//checking Thread Safety => disabled-----------------------------------------------------------------------------------------------------------------------------

ob_start();
phpinfo();
$subject = ob_get_clean ();

if (preg_match('/Thread Safety.*">(.*?).</', $subject, $regs)) {
	$results = $regs[1];
} else {
	$results = "";
}

if ($results == 'disabled'){
$ok_fail = $ok;
$font = $green;
$message = "Thread Safety is set correctly";
}else{
$ok_fail = $fail;
$font = $red;
$message = "Thread Safety must be disbaled in php.ini";
}

echo '<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#FFFFFF" id="table3">
		<tr>
			<td style="border: 1px solid #CFDFEF" height="30">
			<p align="center"><div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="604" bgcolor="#FFFFFF" id="table1">
		<tr>
			<td width="122" bgcolor="#F5F5F5" height="50">
			<p align="right"><b><font face="Arial" style="font-size: 9pt">THREAD SAFTEY TEST</font></b></td>
			<td width="407" bgcolor="#F5F5F5" height="50">&nbsp;
			<input type="text" name="T2" size="59" value="' . $message .
'" style="color: #006699; border: 1px solid #9FBFDF; background-color: #D8E1F5"></td>
			<td bgcolor="#FFFFFF" height="50" style="border-left: 1px solid #CFDFEF; border-right-width: 1px; border-top-width: 1px; border-bottom-width: 1px" align="center">
			<p align="left"><b><font color="' . $font . '" face="Arial" size="2">' . $ok_fail .
'</font></b></td>
		</tr>
	</table></div></td>
		</tr>
		</table>
</div><p align="center"></p>

<!-- server check fail message-->
<div style="margin-top:30px; margin-left:auto; margin-right:auto; margin-bottom:30px;background-color: #FFFF66;border: 1px dashed #FF6600; width:600px; padding:5px; font-family:Arial, Helvetica, sans-serif; font-size:12px">
  <div align="center"><span style="font-weight:bold; color:#000000; font-size:16px">If any of the above are showing as </span><span style="font-weight:bold; color:#FF0000; font-size:16px">Failed</span><span style="font-weight:bold; color:#000000; font-size:16px">, You can do one the following:</span><br />
    <br />
    <ol style="padding:0px; margin:0px">
      <li style="margin-bottom:4px">Find a PHPmotion compatible Web Host - <a href="http://phpmotion.com/content/view/43/189/" target="_blank"> Find a Compatible Web Host</a></li>
      <li style="margin-bottom:4px">Is a dedicated/server or a VPS? You can use our <a href="http://phpmotion.com/content/view/12/30/" target="_blank">Installation Service</a></li>
      <li style="margin-bottom:4px">Free information and help from 100,000+ members in <a href="http://phpmotion.com/forum/index.php" target="_blank">our Forum</a></li>
    </ol>
  </div>
</div>
<!-- server check fail message end-->
';

?>