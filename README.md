# fhp-vorlesungsvz-reader

This script reads the FHP Website to get information about its courses. The info is then used to make an API call to Google Calendar in order to add the courses to the users calendar as events. This will make it easier for students to check which courses have time intersections.

###[Live Demo](http://base.kevinschiffer.de/Kevin/projects/fhp2gcal/vz-reader.php)###

_Note: This project is a rather quick draft which however does precisely what it promises._

___

###How it works###
It consists of three files which have the following function:

**index.html**:

Serves as entry point and provides a basic user interface to start the process

**vz_parser.php**:

This php script reads the raw html code of the FH Potsdam Website's course directory. It uses the [DomDocument Class](http://php.net/manual/de/class.domdocument.php) and the [DomXPath Class](http://php.net/manual/de/class.domxpath.php) to read and query the ingested html document. Using a query for specific class names and some regular expressions, the script will then crawl through the table rows and parse relevant info to a result object which is then outputed as JSON.

**inserter.js**:

This script will take the generated JSON and make the necessary Google Calendar API calls to in order to create a calendar event for each course, it will color code different modules and set the time and date along with the course description. In order to use this for yourself, you will need to create your own Google API Key.
The script will modify any calendar it is given permission on (which is validated beforehand).
