// Your Client ID can be retrieved from your project in the Google
// Developer Console, https://console.developers.google.com
var CLIENT_ID = '566245696909-2iadilfimf1ddbjpcrori2neuig16u96.apps.googleusercontent.com';

var SCOPES = ["https://www.googleapis.com/auth/calendar"];

/**
 * Check if current user has authorized this application.
 */
 function checkAuth() {
  gapi.auth.authorize(
  {
    'client_id': CLIENT_ID,
    'scope': SCOPES.join(' '),
    'immediate': true
  }, handleAuthResult);
}

/**
 * Handle response from authorization server.
 *
 * @param {Object} authResult Authorization result.
 */
 function handleAuthResult(authResult) {
  var authorizeDiv = document.getElementById('authorize-div');
  if (authResult && !authResult.error) {
    // Hide auth UI, then load client library.
    authorizeDiv.style.display = 'none';
    loadCalendarApi();
  } else {
    // Show auth UI, allowing the user to initiate authorization by
    // clicking authorize button.
    authorizeDiv.style.display = 'inline';
  }
}

/**
 * Initiate auth flow in response to user clicking authorize button.
 *
 * @param {Event} event Button click event.
 */
 function handleAuthClick(event) {
  gapi.auth.authorize(
    {client_id: CLIENT_ID, scope: SCOPES, immediate: false},
    handleAuthResult);
  return false;
}

/**
 * Load Google Calendar client library. List upcoming events
 * once client library is loaded.
 */
 function loadCalendarApi() {
  gapi.client.load('calendar', 'v3', createEvents);
}

/**
 * Print the summary and start datetime/date of the next ten events in
 * the authorized user's calendar. If no events are found an
 * appropriate message is printed.
 */
 function createEvents() {
 // Refer to the JavaScript quickstart on how to setup the environment:
// https://developers.google.com/google-apps/calendar/quickstart/js
// Change the scope to 'https://www.googleapis.com/auth/calendar' and delete any
// stored credentials.

var batch = gapi.client.newBatch();

var calendar = {
  'summary' : 'FHP Vorlesungen',
  'timeZone': "Europe/Berlin"
};

var request = gapi.client.calendar.calendars.insert({resource: calendar});
request.execute(function(fhpCalendar){
  console.log(fhpCalendar.id);
  $.getJSON('vz-reader.php', function(data) {
    

    $.each(data, function(index, value){
      console.log(value["titel"]);

      var dateValue = 'dateTime';

      if (value['begin'].length <= 10) {
        dateValue = 'date';
      }

      var event = {
        'summary': value["modul"]+" | "+value["titel"],
        'location': value["raum"],
        'description': value["titel"],
        'colorId': value["colorId"],
        'start': {
          'timeZone': 'Europe/Berlin'
        },
        'end': {
          'timeZone': 'Europe/Berlin'
        },
        'recurrence': [
        'RRULE:FREQ=WEEKLY;COUNT=1'
        ],
        'reminders': {
          'useDefault': true
        }
      };

      event['start'][dateValue] = value['begin'];
      event['end'][dateValue] = value['end'];

      //console.log(event);

      //if (index == 2) return false;

      var request = gapi.client.calendar.events.insert({
        'calendarId': fhpCalendar.id,
        'resource': event
      });

      batch.add(request);

    });

    console.log("executing");

    batch.execute(function(responseMap,rawBatchResponse) {
      console.log(responseMap);
    });

  });
});
}