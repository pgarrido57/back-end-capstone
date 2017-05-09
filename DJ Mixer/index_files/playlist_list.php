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
 ?>
