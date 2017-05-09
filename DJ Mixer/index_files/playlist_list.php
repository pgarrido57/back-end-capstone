<?php

// Build Song Array
$databaseFolder = "../music";

  if(!isset($_POST['folder']))
    listAllFolders();
  else
    listAllSongsInFolder(in($_POST['folder']));

// List All Folders
function listAllFolders()
  {
	global $databaseFolder;

	if(is_dir($databaseFolder))
	{
	  $dh = opendir($databaseFolder);

			// List file in $databaseFolder
			while(($folderName = readdir($dh)) !== false)

				// If file is a folder
				if(is_dir($databaseFolder.'/'.$folderName) && $folderName!="." && $folderName!="..")
          $folderNameList[] = $folderName;

      if(!isset($folderNameList))
	    $folderNameList[] = "OOOPS! No folder found into the $databaseFolder folder";

			// If natcasesort not supported
      sort($folderNameList);
		// Sort folders list
	  natcasesort($folderNameList);

	  closedir($dh);
	}
	else
	{
	  $folderNameList[] = "OOOPS! $databaseFolder folder not found";
	}

		// Output the folders list in a JavaScript format
    for($i=0; $i<count($folderNameList); $i++)
    {
      echo ($i == 0 ? "[" : ",");
	  echo "'".out($folderNameList[$i])."'";
      echo ($i == count($folderNameList) - 1 ? "]" : "");
    }
  }

	// List all songs in folders

	function listAllSongsInFolder($selectedFolder)
	// List MP3's in selected folder
	{
    global $databaseFolder;

	if(is_dir($databaseFolder.'/'.$selectedFolder))
	{
	  $dh = opendir($databaseFolder.'/'.$selectedFolder);

			// List file in $selectedFolder
			while (($fileName = readdir($dh)) !== false)
				// If file is a MP3
				if(strpos($fileName,'.mp3'))
					$fileNameList[] = $fileName;

			if(!isset($fileNameList))
			$fileNameList[] = "OOOPS! no mp3 file found in this folder";

		// If natcasesort is not supported
		sort($fileNameList);
		// Sort filenames list
		natcasesort($fileNameList);

		closedir($dh);
	}

	// Output the file list in a JS format
	for($i=0; $i<count($fileNameList); $i++)
		{
			// Extract songs info from filename
			$songInfo = getSongInfo($fileNameList[$i]);
			echo ($i == 0 ? "[" : ",");
		  echo "{filename:'".out($selectedFolder.'/'.$fileNameList[$i])."',artist:'".out($songInfo["artist"])."',title:'".out($songInfo["title"])."'}";
	      echo ($i == count($fileNameList) - 1 ? "]" : "");
		}
	  }

 ?>
