<!DOCTYPE html>
<html>
<head>
<?php
$index_path = $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
$path_parts = pathinfo($index_path); $directory_path = $path_parts['dirname'];
echo "<script>\nvar index_path = '$index_path'; // from php\nvar directory_path = '$directory_path'; // from php\n</script>\n";
if(isset($_GET['id'])) echo "<script>var mixIdToLoadOnStart = '".$_GET['id']."';</script>";
?>
<title>NSS DJ</title>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="index_files/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="index_files/styles.css"/>
<script type="text/javascript" src="index_files/jquery-1.6.min.js"></script>
<script type="text/javascript" src="index_files/app.js"></script>
<script type="text/javascript" src="djFiles/record.js"></script>
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
	 <tr>
		<td height="67" align="center" valign="bottom" bgcolor="#000000" background="index_files/pic_header.png" style="background-repeat: repeat-x;">
		  <table border="0" cellpadding="0" cellspacing="0" height="67" width="1000">
			 <tbody>
				<tr>
				  <td width="828" height="54" align="left" valign="top"><a href="index.php"><img src="index_files/pic_logo.png" width="211" height="57" border="0" /></a> </td>
				</tr>
			 </tbody>
		  </table>
		</td>
	 </tr>
	 <tr valign="top">
		<td align="center" valign="top">
		  <table border="0" cellpadding="0" cellspacing="0" width="1012">
			 <tbody>
				<tr>
				  <td align="left" valign="top">
					 <table width="1012" cellpadding="0" cellspacing="0">
						<tr>
						  <td width="1012" height="311" valign="top" align="center">
							 <div id="iWebDjEngine">
								<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" id="djPlayer" width="1012" height="304" align="middle">
								  <param name="allowScriptAccess" value="always" />
								  <param name="movie" value="preloader.swf" />
								  <param name="quality" value="low" />
								  <param name="wmode" value="opaque" />
								  <param name="bgcolor" value="#333335" />
								  <param name="flashvars" value="url=mixer_v4.swf" />
								  <embed src="preloader.swf" quality="low" wmode="opaque" bgcolor="#333335" width="1012" height="304" swLiveConnect="true" flashvars="url=mixer_v4.swf" id="djPlayer" name="djPlayer" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" /> </object>
							 </div>
						  </td>
						</tr>
						<tr>
						  <td width="1012" valign="top" align="center">
							 <table width="1012" cellpadding="2" cellspacing="3" border="0">
								<tr valign="top">
								  <td width="205">
									 <table class="folderList_boxStyle" width="205" cellpadding="0" cellspacing="0">
										<tr valign="top">
										  <td align="left" id="folderList" width="203" height="210">
											 <table cellpadding="0" cellspacing="0" border="0">
												<tr valign="middle" height="30">
												  <td width="203" bgcolor="#171717" style="border-bottom:1px solid #000;">&nbsp;&nbsp;<a id="menu_playlist" class="folderList_item" href="javascript:displaySongsInFolder(0);" style="font-size:16px"><b>Music folders</b></a></td>
												</tr>
												<tr valign="top">
												  <td width="198">
													 <div id="folderListContent">&nbsp;</div>
												  </td>
												</tr>
												<tr valign="middle" height="30">
												  <td width="203" bgcolor="#171717" style="border-top:1px solid #000;">&nbsp;&nbsp;<a id="menu_record" class="folderList_item" href="javascript:displayRecordMixes();" style="font-size:16px"><b>Recording</b></a> <a href="feature_record.php" target='_blank'><font size="2"></font></a></td>
												</tr>
												<tr valign="middle" height="30">
												  <td width="203" bgcolor="#171717" style="border-top:1px solid #000;">&nbsp;&nbsp;<a id="menu_sampler" class="folderList_item" href="javascript:displaySampler();" style="font-size:16px"><b>Sampler</b></a></td>
												</tr>
												<tr valign="middle" height="30">
												  <td width="203" bgcolor="#171717" style="border-top:1px solid #000; border-bottom:1px solid #000;">&nbsp;&nbsp;<a id="menu_config" class="folderList_item" href="javascript:djPlayer.sendCommand('config',0);" style="font-size:16px"><b>Settings</b></a></td>
												</tr>
											 </table>
										  </td>
										</tr>
									 </table>
								  </td>
								  <td width="800" align="left">
									 <table class="songList_boxStyle1" width="800" cellpadding="0" cellspacing="0">
										<tr valign="top">
										  <td id="songList" width="798" height="448">
											 <div id="songListContent">&nbsp;</div>
										  </td>
										</tr>
									 </table>
								  </td>
								</tr>
							 </table>
						  </td>
						</tr>
					 </table>
				  </td>
				</tr>
			 </tbody>
		  </table>
		</td>
	 </tr>
	 <tr valign="top">
		<td align="center" valign="bottom">
		  <table width="1012" height="100%" border="0" cellpadding="0" cellspacing="0" style="color:#666;">
			 <tr valign="middle">
				<td align="left" valign="bottom">Peter Garrido</td>
				<td align="right" valign="bottom"> &copy; Peter Garrido</td>
			 </tr>
		  </table>
		</td>
	 </tr>
  </tbody>
</table>
</body>
</html>
