/* 
 * The Zapatec DHTML Calendar
 *
 * Copyright (c) 2004 by Zapatec, Inc.
 * http://www.zapatec.com
 * 1700 MLK Way, Berkeley, California,
 * 94709, U.S.A. 
 * All rights reserved.
 *
 * Original version written by Mihai Bazon,
 * http://www.bazon.net/mishoo/calendar.epl
 *
 * This file defines helper functions for setting up the calendar.  They are
 * intended to help non-programmers get a working calendar on their site
 * quickly.  This script should not be seen as part of the calendar.  It just
 * shows you what one can do with the calendar, while in the same time
 * providing a quick and simple method for setting it up.  If you need
 * exhaustive customization of the calendar creation process feel free to
 * modify this code to suit your needs (this is recommended and much better
 * than modifying calendar.js itself).
 */

// $Id: calendar-setup.js 1909 2006-02-08 16:59:53Z slip $

//test for the right path
Zapatec.Setup = function () {};
Zapatec.Setup.test = true;

/**
 *  This function "patches" an input field (or other element) to use a calendar
 *  widget for date selection.
 *
 *  Note that you can use the Zapatec DHTML Calendar Wizard and generate the code
 *  and modify the results.
 *  The "params" is a single object that can have the following properties:
 *
 * \code
 *    prop. name   | description
 *  -------------------------------------------------------------------------------------------------
 *   inputField    | the ID of an input field to store the date
 *   displayArea   | the ID of a DIV or other element to show the date
 *   button        | ID of a button or other element that will trigger the calendar
 *   eventName     | event that will trigger the calendar, without the "on" prefix (default: "click")
 *   closeEventName| event that will close the calendar (i.e. one can use "focus" for eventName and "blur" for closeEventName)
 *   ifFormat      | date format that will be stored in the input field
 *   daFormat      | the date format that will be used to display the date in displayArea
 *   singleClick   | (true/false) wether the calendar is in single click mode or not (default: true)
 *   firstDay      | numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
 *   align         | alignment (default: "Br"); if you don't know what's this see the calendar documentation
 *   range         | array with 2 elements.  Default: [1900.0, 2999.12] -- the range of years available
 *   weekNumbers   | (true/false) if it's true (default) the calendar will display week numbers
 *   flat          | null or element ID; if not null the calendar will be a flat calendar having the parent with the given ID
 *   flatCallback  | function that receives a JS Date object and returns an URL to point the browser to (for flat calendar)
 *   disableFunc   | function that receives a JS Date object and should return true if that date has to be disabled in the calendar
 *   onSelect      | function that gets called when a date is selected.  You don't _have_ to supply this (the default is generally okay)
 *   onClose       | function that gets called when the calendar is closed.  [default]
 *   onUpdate      | function that gets called after the date is updated in the input field.  Receives a reference to the calendar.
 *   date          | the date that the calendar will be initially displayed to
 *   showsTime     | default: false; if true the calendar will include a time selector
 *   timeFormat    | the time format; can be "12" or "24", default is "12"
 *   electric      | if true (default) then given fields/date areas are updated for each move; otherwise they're updated only on close
 *   sortOrder	   | ("asc"(ending)/"desc"(ending)/"none"). If "asc" (default), order of the multiple dates (when multiple dates is on) will be sorted in ascending order. Otherwise, it will be sorted in descending order. "none" means no sorting is needed.
 *   step          | configures the step of the years in drop-down boxes; default: 2
 *   position      | configures the calendar absolute position; default: null
 *   cache         | if "true" (but default: "false") it will reuse the same calendar object, where possible
 *   showOthers    | if "true" (but default: "false") it will show days from other months too
 *   saveDate      | if set (default unset) will save a cookie for this duration.
 *   numberMonths  | Have the calendar display multiple months
 *   controlMonth  | When displaying multiple months, this will be the control month. Default 1.
 *   vertical      | When displaying multiple months, months can progress in a vertical or horizontal way. Horizontal is the default.
 *   monthsInRow   | When displaying multiple months how many months in a horizontal row. Works both in vertical and horizontal mode. Default numberMonths
 *   fdowClick     | Allow click on Days of Week 1st day
 *   titleHtml     | Html you can put in title of calendar
 *   
 * \endcode
 *
 *  None of them is required, they all have default values.  However, if you
 *  pass none of "inputField", "displayArea" or "button" you'll get a warning
 *  saying "nothing to setup".
 */
Zapatec.Calendar.setup = function (params) {
	function param_default(pname, def) { if (typeof params[pname] == "undefined") { params[pname] = def; } };
	param_default("inputField",     null);
	param_default("displayArea",    null);
	param_default("button",         null);
	param_default("eventName",      "click");
	param_default("ifFormat",       "%Y/%m/%d");
	param_default("daFormat",       "%Y/%m/%d");
	param_default("singleClick",    true);
	param_default("disableFunc",    null);
	param_default("dateStatusFunc", params["disableFunc"]);	// takes precedence if both are defined
	param_default("dateText",       null);
	param_default("firstDay",       null);
	param_default("align",          "Br");
	param_default("range",          [1900, 2999]);
	param_default("weekNumbers",    true);
	param_default("flat",           null);
	param_default("flatCallback",   null);
	param_default("onSelect",       null);
	param_default("onClose",        null);
	param_default("onUpdate",       null);
	param_default("date",           null);
	param_default("showsTime",      false);
	param_default("sortOrder",      "asc");
	param_default("timeFormat",     "24");
	param_default("timeInterval",   null);
	param_default("electric",       true);
	param_default("step",           2);
	param_default("position",       null);
	param_default("cache",          false);
	param_default("showOthers",     false);
	param_default("multiple",       null);
	param_default("saveDate",       null);
	param_default("fdowClick",      false);
	param_default("titleHtml",      null);
	param_default("disableFdowChange", false);
	if ((params.numberMonths > 12) || (params.numberMonths < 1)) {
		params.numberMonths = 1;
	} else {
		param_default("numberMonths",   1);
	}
	if (params.numberMonths > 1) {
		params.showOthers = false;
	}
	params.numberMonths = parseInt(params.numberMonths, 10);
	if ((params.controlMonth > params.numberMonths) || (params.controlMonth < 1)) {
		params.controlMonth = 1;
	} else {
		param_default("controlMonth",   1);
	}
	params.controlMonth = parseInt(params.controlMonth, 10);
	param_default("vertical",       false);
	if (params.monthsInRow > params.numberMonths) {
		params.monthsInRow = params.numberMonths;
	}
	param_default("monthsInRow",    params.numberMonths);
	params.monthsInRow = parseInt(params.monthsInRow, 10);
	if (params.multiple) {
		params.singleClick = false;
	}
	
	var tmp = ["inputField", "displayArea", "button"];
	for (var i in tmp) {
		if (typeof params[tmp[i]] == "string") {
			params[tmp[i]] = document.getElementById(params[tmp[i]]);
		}
	}
	if (!(params.flat || params.multiple || params.inputField || params.displayArea || params.button)) {
		alert("Calendar.setup:\n  Nothing to setup (no fields found).  Please check your code");
		return false;
	}
	if (((params.timeInterval) && ((params.timeInterval !== Math.floor(params.timeInterval)) || ((60 % params.timeInterval !== 0) && (params.timeInterval % 60 !== 0)))) || (params.timeInterval > 360)) {
		alert("timeInterval option can only have the following number of minutes:\n1, 2, 3, 4, 5, 6, 10, 15, 30,  60, 120, 180, 240, 300, 360 ");
		params.timeInterval = null;
	}
	if (params.date && !Date.parse(params.date)) {
		alert("Start Date Invalid: " + params.date + ".\nSee date option.\nDefaulting to today.");
		params.date = null;
	}
	if (params.saveDate) { //If saveDate is on We're saving the date in a cookie
		param_default("cookiePrefix", window.location.href + "--" + params.button.id);
		//fetch the cookie
		var cookieName = params.cookiePrefix;
		var newdate = Zapatec.Utils.getCookie(cookieName);
		if (newdate != null) { //if there's a cookie
			//set the value of the text field
			document.getElementById(params.inputField.id).value = newdate;
		}
	}

	function onSelect(cal) {
		var p = cal.params;
		var update = (cal.dateClicked || p.electric);
		if (update && p.flat) {
			if (typeof p.flatCallback == "function")
			{
			   if (!p.multiple) //User can call function submitFlatDates directly in Calendar object to handle the submission of multiple dates.
				p.flatCallback(cal);
			} else
				alert("No flatCallback given -- doing nothing.");
			return false;
		}
		if (update && p.inputField) {
			p.inputField.value = cal.currentDate.print(p.ifFormat);
			if (typeof p.inputField.onchange == "function")
				p.inputField.onchange();
		}
		if (update && p.displayArea)
			p.displayArea.innerHTML = cal.currentDate.print(p.daFormat);
		if (update && p.singleClick && cal.dateClicked)
			cal.callCloseHandler();
		if (update && typeof p.onUpdate == "function")
			p.onUpdate(cal);
		if (p.saveDate) { //save date in cookie
			//unique name of the cookie is the name of the button  + href
			var cookieName = p.cookiePrefix;
			Zapatec.Utils.writeCookie(cookieName, p.inputField.value, null, '/', p.saveDate);
		} 
	};

	if (params.flat != null) {
		if (typeof params.flat == "string")
			params.flat = document.getElementById(params.flat);
		if (!params.flat) {
			alert("Calendar.setup:\n  Flat specified but can't find parent.");
			return false;
		}
		var cal = new Zapatec.Calendar(params.firstDay, params.date, params.onSelect || onSelect);
		cal.disableFdowClick = params.disableFdowChange;
		cal.showsOtherMonths = params.showOthers;
		cal.showsTime = params.showsTime;
		cal.time24 = (params.timeFormat == "24");
		cal.timeInterval = params.timeInterval;
		cal.params = params;
		cal.weekNumbers = params.weekNumbers;
		cal.sortOrder = params.sortOrder.toLowerCase();
		cal.setRange(params.range[0], params.range[1]);
		cal.setDateStatusHandler(params.dateStatusFunc);
		cal.getDateText = params.dateText;
		cal.numberMonths = params.numberMonths;
		cal.controlMonth = params.controlMonth;
		cal.vertical = params.vertical;
		cal.yearStep = params.step;
		cal.monthsInRow = params.monthsInRow;
		cal.helpButton = !params.noHelp;
		if (params.ifFormat) {
			cal.setDateFormat(params.ifFormat);
		}
		
		if (params.inputField && params.inputField.type == "text" && typeof params.inputField.value == "string") {
			cal.parseDate(params.inputField.value);
		}

		if (params.multiple) {
		   cal.setMultipleDates(params.multiple);
		}
		cal.create(params.flat);
		cal.show();
		return cal;
	}

	var triggerEl = params.button || params.displayArea || params.inputField;
	triggerEl["on" + params.eventName] = function() {
		var dateEl = params.inputField || params.displayArea;
		//FIX for Enter key!
		if (triggerEl.blur) {triggerEl.blur();}
		var dateFmt = params.inputField ? params.ifFormat : params.daFormat;
		var mustCreate = false;
		var cal = window.calendar;

		// Exit if calendar is NOT hidden and user tries to create another calendar (Click or SpaceBar)
		// Rev 1.9 - this needs to be integrated, it broke the multiple month feature
		//if (cal && !cal.hidden) return false;

		if (!(cal && params.cache)) {
			window.calendar = cal = new Zapatec.Calendar(params.firstDay,
							     params.date,
							     params.onSelect || onSelect,
							     params.onClose || function(cal) {
								     if (params.cache)
									     cal.hide();
								     else
									     cal.destroy();
							     });
			cal.disableFdowClick = params.disableFdowChange;
			cal.showsTime = params.showsTime;
			cal.time24 = (params.timeFormat == "24");
			cal.timeInterval = params.timeInterval;
			cal.weekNumbers = params.weekNumbers;
			cal.numberMonths = params.numberMonths;
			cal.controlMonth = params.controlMonth;
			cal.vertical = params.vertical;
			cal.monthsInRow = params.monthsInRow;			
			cal.historyDateFormat = params.ifFormat || params.daFormat;
			cal.helpButton = !params.noHelp;
			mustCreate = true;
		} else {
			if (params.date)
				cal.setDate(params.date);
			cal.hide();
		}

		if (params.multiple) {
		   cal.setMultipleDates(params.multiple);
		}
		
		cal.showsOtherMonths = params.showOthers;
		cal.yearStep = params.step;
		cal.setRange(params.range[0], params.range[1]);
		cal.params = params;
		cal.setDateStatusHandler(params.dateStatusFunc);
		cal.getDateText = params.dateText;
		cal.setDateFormat(dateFmt);
		if (mustCreate)
			cal.create();
		if (dateEl) {
			var dateValue;
			//figure out if the it's in value or innerHTML
			if (dateEl.value) {
				dateValue = dateEl.value;
			} else {
				dateValue = dateEl.innerHTML;
			}
			if (dateValue != "") { //if there is a date to initialize from
				var parsedDate = Date.parseDate(dateEl.value || dateEl.innerHTML, dateFmt);
				//This check for when webmaster initializes the box with something like
				//"check in"
				if (parsedDate != null) { //if it's parsable
				cal.setDate(parsedDate);
				}
			}
		}
		if (!params.position)
			cal.showAtElement(params.button || params.displayArea || params.inputField, params.align);
		else
			cal.showAt(params.position[0], params.position[1]);
		return false;
	};

	if (params.closeEventName) {
		triggerEl["on" + params.closeEventName] = function() {
			if (window.calendar)
				window.calendar.callCloseHandler();
		};
	}

	return cal;
};


