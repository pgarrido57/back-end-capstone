// Path to the record.php
var recordUri = 'djFiles/record.php';

// Record functions
var dj = "Guest"
var mixDataRequest = null;
var mixDataQueue = {};
var mixDataIndex = 0;

// Function is triggered when you start a new record session
function REQUEST_MIX_ID() {
	mixDataIndex = 0;
	mixDataQueue = {};

	dj = prompt("Please enter your DJ Name", "Guest");
	if(dj == null) dj = "Guest";
	dj = dj.slice(0,40);

	// Cancel all current requests
	if (mixDataRequest) mixDataRequest.abort();
	// First call: Starts the recording with Null ID
	djPlayer.sendMixID(null);
	// Retrieve Mongo ID
	GET_MIX_DATA({id:null});

	displayRecordMixes();
}
