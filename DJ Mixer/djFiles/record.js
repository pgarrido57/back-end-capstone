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

// Function is triggered when current recording is stopped
function STOP_MIX(mixId) {
	displayRecordMixes();
}

// Function is triggered whenever the mixer sends mix data that need to be saved in the DB
// Its called every every 5 seconds
function GET_MIX_DATA(mixData) {

	// Append mixdata in the queue
	if(mixData) mixDataQueue[mixDataIndex++] = mixData;

	// Send all mixdata in the queue to server all in one request
	mixDataRequest = $.post(recordUri, {data: JSON.stringify(mixDataQueue), name: dj}),success(function(response) {
		// On the server response
		if(response && !response.error && response.result) {
			// Clear queue
			mixDataQueue = {};
			// If the mix is new it will send new mixId
			if(response.id && response.id.length > 0) djPlayer.sendMixID(response.id);
		}
	});
}

// Function is triggered when the mixer needs mix data
// Called every 5 secods if the mix is live
function REQUEST_MIX_DATA(mixId) {
	return $.get(recordUri, {id: mixId}).success(function(response) {
		djPlayer.sendMixData(response);
	});
}
