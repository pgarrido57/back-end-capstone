// Playlist Config

var djPlayer;

// Folder index to load on page load
var folderArrayIndex = 0;

// Song on start
var songsIndexOnStart = new Array();

// Path of Playlist
var playlistPath = require('./playlist_list.php');

// Path for record API
var recordPath = require('../djFiles/record.php');

// Path for sampler API
var samplerPath = require('../djFiles/sampler.php');


// Init
var bpmRangeMax = 147;
var folderArray = new Array();
var songArray = new Array();
var mixLoadedOnStart = false;

var lastIdx = -1;
var randIdx = 0;
var randList = new Array();

$(document).ready(function() {
	$.ajaxSetup({ cache: false });
	djPlayer = getMovieInstance("djPlayer");
	window.onresize = resizeWindow;
	resizeWindow();
	displayFolder();
});

function getMovieInstance(movieName) {
	return navigator.appName.indexOf("Microsoft") != -1 && !navigator.appVersion.indexOf('MSIE 9.0') ? window[movieName] : document[movieName;]
}

function setMenuSelection(selection) {
	for(var i = 0; i < folderArray.length; i++) document.getElementById('folder'+i).className = "folderList_item";
	document.getElementById('menu_record').className = "folderList_item";
	document.getElementById('menu_sampler').className = "folderList_item";
	document.getElementById('selection').className = "folderList_selected";
}

// Display folders

function displayFolders() {
	$('#folderListContent').html("");
	$.post(playlistPath).success(function(response) {
		eval("folderArray = " + response);
		var htmlString = "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";

		for(var i = 0; i < folderArray.length; i++) {
			var lineStyle = (i % 2 == 0 ? 'folderList_lineStyle1' : 'folderList_lineStyle2');
			htmlString += "<tr class="+lineStyle+">";
			htmlString += "<td width='37' align='right'><div class='folderList_icon_folders'>&nbsp;</div></td>";
			htmlString += "<td align='left'><a id='folder"+i+"' class='folderList_item' onclick=displaySongsInFolder("+i+")>"+folderArray[i].replace("_"," ");+"</a></td>";
			htmlString += "</tr>";
		}

		htmlString += "</table>";
		$('#folderListContent').html(htmlString);

		displaySongsInFolder(folderArrayIndex);
	});
}

// Display Song

function displaySongsInFolder(_folderArrayIndex) {

	midiSelectIndex = -1;
	folderArrayIndex = _folderArrayIndex;
	setMenuSelection('folder' + _folderArrayIndex);

	var folderName = folderArray[folderArrayIndex];
	// Allows folders to have '&'character in it
	folderName = folderName.replace("&", "%26");
	$('#songListContent').html("");

	$.post(playlistPath, {folder: folderName}).success(function(response) {
		eval("songArray = "+ response);
		constructRandList(songArray.length);

		var htmlString = "<table id='songList_table' class='songList_boxStyle2' width='100%' border='0' cellpadding='0' cellspacing='0'>";

		for(var i=0; i<songArray.length ; i++) {
			var lineStyle = (i%2 == 0 ? 'songList_lineStyle1 ' : 'songList_lineStyle2');
			htmlString += "<tr class="+lineStyle+">";
			htmlString += "<td width='35' align='right'><a onclick=loadSongFromSongArray('player1',"+i+")><div class='songList_icon_load_player1'></div></a></td>";
			htmlString += "<td width='50' align='left'> <a onclick=loadSongFromSongArray('player2',"+i+")><div class='songList_icon_load_player2'></div></a></td>";
			htmlString += "<td width='250' align='left'><p class='songList_textStyle1'>"+songArray[i].artist+"</p></td>";
			htmlString += "<td align='left'><p class='songList_textStyle2'>"+songArray[i].title+"</p></td>";
			htmlString += "</tr>";
		}

		htmlString += "</table>";
		$('#songListContent').html(htmlString);
	});
}

// Display recorded mixes

function displayRecordMixes() {

	setMenuSelection("menu_record");
	$.get(recordPath).success(function(res) {
		if (res.error)
		console.log(res.error);
		else if (res.result && res.result.length) {

			var mixNb = 0;
			var htmlString = "<table id='songList_table' class='songList_boxStyle2' width='100%' border='0' cellpadding='0' cellspacing='0'>";

			for (var i=0; i<res.result.length; i++) {

				var mix = res.result[i];
				var mixTime = mix.info.time * 1000;
				var mixOldness = new Date().getTime() - mixTime;
				var mixDuration = (mix.info.live ? mixOldness : mix.info.duration);

				var onclick = 'djPlayer.loadMix("' + mix._id.$id + '")';
				var lineStyle = (mixNb%2==0 ? ' songList_textStyle1' : 'songList_lineStyle2');
				var displayMix = (mixDuration > 60 * 1000 || mixOldness < 8 * 3600 * 1000);
				var urlDjPlayer = "http://"+index_path+"?id="+mix._id.$id;

				if(displayMix) {
						htmlString += "<tr class="+lineStyle+">";
						htmlString += "<td width='73' align='center'><a onclick='"+onclick+"'><div class='songList_icon_load_play'></div></a></td>";
						htmlString += "<td width='545' align='left'><a class='songList_mixLink' onclick='"+onclick+"'>"+mix.info.name+" Recorded Mix <font color='#999999'> - "+(new Date(mixTime).toString("MMMM dd, h:mm tt"))+"</font></div></a></td>";
						htmlString += "<td width='60' align='right'><a href='"+urlFullPlayer+"' target='_blank'><div class='songList_icon_share'></div></a></td>";
						htmlString += "<td width='40' align='right'><p class='songList_textStyle2'>"+(new Date(mixDuration).toString("m:ss"))+"</p></td>";
						htmlString += "<td align='center'>"+(mix.info.live ? "<div class='songList_icon_live'></div>" : "<p></p>")+"</td>";
						htmlString += "</tr>";
						mixNb++;
			}
		}

		htmlString += "</table>";
	       		$('#songListContent').html(htmlString);
		}
	});
}
