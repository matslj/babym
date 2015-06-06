<?php
// ===========================================================================================
//
// PIndex.php
//
// Startsida för turneringen.
//
// Author: Mats Ljungquist
//


// -------------------------------------------------------------------------------------------
//
// Get pagecontroller helpers. Useful methods to use in most pagecontrollers
//
$pc = CPageController::getInstance();
//$pc->LoadLanguage(__FILE__);


// -------------------------------------------------------------------------------------------
//
// Interception Filter, controlling access, authorithy and other checks.
//
$intFilter = new CInterceptionFilter();
$intFilter->FrontControllerIsVisitedOrDie();

$img = WS_IMAGES;
$action = "?p=filterp";

// -------------------------------------------------------------------------------------------
//
// Create a new database object, connect to the database, get the query and execute it.
// Relates to files in directory TP_SQLPATH.
//

// Connect
$db 	= new CDatabaseController();
$mysqli = $db->Connect();



$userId = 2;

$tempBData = CBabyDataDAO::newDatesInitializedInstance($db, $userId);
$jsMarkedDatesArray = $tempBData -> getDatesAsJavascriptArray();

//$tempBData = new CBabyDataDAO($db, $userId);
//$babyDataHtml = $tempBData -> getBabyDataAsHtml();

$mysqli->close();

// -------------------------------------------------------------------------------------------
//
// Prepare javascript stuff
//
$js = WS_JAVASCRIPT;
$img = WS_IMAGES;
$needjQuery = TRUE;
$htmlHead = "";
$javaScript = "";

$htmlHead .= <<<EOD
    <!-- jQuery UI -->
    <script src="{$js}jquery-ui/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="{$js}handlebars/handlebars-v1.3.0.js"></script>
    <script src="{$js}jquery-bbq/jquery.ba-bbq.min.js"></script>
        
    <script src="{$js}timepicker/jquery.timepicker.js"></script>
    <script src="{$js}chart/Chart.min.js"></script>
        
    <link rel="stylesheet" type="text/css" href="{$js}timepicker/jquery.timepicker.css" />
        
    <style>
        /* Gör om css för dagens datum till samma css som alla andra (ovalda) datum */
        .ui-datepicker-today .ui-state-highlight {
            background: url("{$js}jquery-ui/cupertino/images/ui-bg_glass_80_d7ebf9_1x400.png") repeat-x scroll 50% 50% #D7EBF9;
            border: 1px solid #AED0EA;
            color: #2779AA;
            font-weight: bold;
        }
        
        .ui-datepicker-today .ui-state-active {
            background: url("{$js}jquery-ui/cupertino/images/ui-bg_glass_50_3baae3_1x400.png") repeat-x scroll 50% 50% #3BAAE3;
            border: 1px solid #2694E8;
            color: #FFFFFF;
            font-weight: bold;
        }
        
        .ui-datepicker.ui-widget {
            font-size: 0.9em;
        }
        
        
        .ui-datepicker th {
            width: 4em;
        }
        
        /* .ui-datepicker { font-size: 9pt !important; } */
        
        .specialDate { 
            background-color: #6F0 !important;
        }
        
        .babydata {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 18px;
            -webkit-border-radius: 7px;
            -moz-border-radius: 7px;
            border-radius: 7px;
        }
        
        .babydata .row {
            padding: 4px 4px 0 4px;
        }
        
        .babydata .row span {
            display: inline-block;
        }
        
        .babydata .row:nth-child(even) {
            background-color: #D7EBF9;
        }
        .babydata .row:nth-child(odd) {
            background-color: #FFF;
        }
        
        .babydata .row .time {
            width: 40px;
            font-style: italic;
        }
        
        .babydata .row .literal {
            width: 100px;
            font-weight: bold;
        }

        .babydata span.value {
            width: 60px;
        }
        
        .babydata .row .note {
            border: 1px solid #CCCCCC;
            background-color: #FFF;
            font-style: italic;
            padding: 2px;
            display: none;
            margin: 0 20px 0 20px;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
        }
        
        input.date {
            width: 80px;
        }
        
        input.time {
            width: 80px;
        }
        
        .deletePost {
            float: right;
        }
        
        a.deletePost, a.deletePost:visited, a.deletePost:hover {
            text-decoration: none;
        }
        
        a.showNote, a.showNote:visited {
            color: #999999;
        }
        
        div#newPost {
            margin-bottom: 10px;
        }
        
        #newPostLink {
            display: block;
            padding-bottom: 10px;
        }
        
        #message {
            height: 15px;
            margin-top: 5px;
        }
              
        /*
        .ui-datepicker .specialDate a { 
            background: #6F0;
        }
        */
    </style>
EOD;

$javaScript .= <<<EOD
var datesArray = {$jsMarkedDatesArray};
var lastAction = "";

(function($){

    function parseDate(input) {
        var parts = input.split('-');
        // new Date(year, month [, day [, hours[, minutes[, seconds[, ms]]]]])
        return new Date(parts[0], parts[1]-1, parts[2]); // Note: months are 0-based
    }
    
    /* For a given date, get the ISO week number
     *
     * Based on information at:
     *
     *    http://www.merlyn.demon.co.uk/weekcalc.htm#WNR
     *
     * Algorithm is to find nearest thursday, it's year
     * is the year of the week number. Then get weeks
     * between that date and the first day of that year.
     *
     * Note that dates in one year can be weeks of previous
     * or next year, overlap is up to 3 days.
     *
     * e.g. 2014/12/29 is Monday in week  1 of 2015
     *      2012/1/1   is Sunday in week 52 of 2011
     */
    function getWeekNumber(d) {
        // Copy date so don't modify original
        // d = new Date(+d);
        d = parseDate(d);
        d.setHours(0,0,0);
        // Set to nearest Thursday: current date + 4 - current day number
        // Make Sunday's day number 7
        d.setDate(d.getDate() + 4 - (d.getDay()||7));
        // Get first day of year
        var yearStart = new Date(d.getFullYear(),0,1);
        // Calculate full weeks to nearest Thursday
        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
        // Return array of year and week number
        return [d.getFullYear(), weekNo];
    }

    function getCurrentUrl() {
        var href = window.location.href;
        return href;
    }

    function getParameterByName(name, href) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( href );
        if( results == null ) {
            return "";
        } else {
            return decodeURIComponent(results[1].replace(/\+/g, " "));
        }
    }

    function retrieveAndDisplayBabyData(theObj, message) {
        // First, create a map with date as key and and arrays of date
        // related baby data objects as values.
        $("#message").empty();

        // Present a message first in the content block if a message is recieved.
        if (typeof message !== "undefined") {
            $("#message").append("<div class='message'>" + message + "</div>");
        }
        
        var json = JSON.stringify(theObj);

        // Call the server side. The call takes to parameters:
        // * method - the method of choice (for availible methods see PFilterByDate.php)
        // * payload - the data for the method. May be empty.
        $.ajax({
            url: "{$action}",
            type:'POST',
            dataType: "json",
            data: {"payload": json},
            success: function(data) {
                $("#bcont").empty();
                // ********************************************************
                // * Display the retrieved data using a handlebars template.
                // *
                var lastDate = null;
                var tempMap = {};
                var tempArray = [];
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        if (lastDate == null || lastDate != data[key].date) {
                            lastDate = data[key].date;
                            tempMap[lastDate] = [];
                        }
                        tempMap[lastDate].push(data[key]);
                    }
                }
                // Feed the template with the map created above.
                for (var key in tempMap) {
                    if (tempMap.hasOwnProperty(key)) {
                        $("#bcont").append(template({date: key, data: tempMap[key]}));
                    }
                }
            }
        });
    }
    
    function activateCurrentState(message) {
        // In jQuery 1.4, use e.getState( "url" );
        var url = $.bbq.getState( "url" );
        var myObj = $.deparam.fragment( url );
        if (myObj.action != "post" && myObj.action != "delete") {
            retrieveAndDisplayBabyData(myObj, message);
        }
    }
    
    function loadDatePicker(picker, aDate, special) {
        if (special !== true) {
            special = false;
        }
        var options = {
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            dayNamesShort: ["Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"],
            monthNames: ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"],
            dayNamesMin: ["Sö", "Må", "Ti", "On", "To", "Fr", "Lö"],
            onSelect: function(dateText) {
                var paramsObj = {
                    action: "selectDate",
                    date: dateText
                    
                };
                var newUrl = $.param.fragment("", paramsObj );
                $.bbq.pushState({url:newUrl});
            },
            beforeShowDay: function (date) {
                if (special) {
                    var day = 0,
                        month = 0;
                    day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
                    month = date.getMonth() + 1;
                    month = month < 10 ? "0" + month : month;
                    var theday = date.getFullYear() + '-' + month +'-'+ day;
                    return [true,$.inArray(theday, datesArray) >=0 ? "specialDate":''];
                }
                return [true, ''];
            }
        };

        $(picker).datepicker("destroy");
        $(picker).datepicker(options).datepicker("setDate", aDate);
        $(picker).datepicker("refresh");
    }
    
    var source = null;
    var template = null;

    $(document).ready(function() {
        $('#newPost').hide();
        $('#newPostLink').click(function() {
            $('#newPost').toggle(400);
            return false;
        });
        
        source = $("#data-template").html();
        // console.log(source);
        template = Handlebars.compile(source);
        
        $('#menu a[href^=#]').live( 'click', function(e){
            $("#menu a").removeClass('sel');
            $(this).addClass('sel');

            var paramsObj = {
                action: "showall"
            };
            var json = JSON.stringify(paramsObj);

            $.ajax({
                url: "{$action}",
                type:'POST',
                dataType: "json",
                data: {"payload": json},
                success: function(data) {
                    var lastDate = null;
                    var tempMap = {};
                    var tempArray = [];
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            if (lastDate == null || lastDate != data[key].date) {
                                lastDate = data[key].date;
                                tempMap[lastDate] = [];
                            } // *************************************************** HÅLLER PÅ HÄÄÄÄÄR ******************** göra i php?
                            tempMap[lastDate].push(data[key]);
                        }
                    }
                    var data = {
	labels : ["January","February","March","April","May","June","July"],
	datasets : [
		{
			fillColor : "rgba(220,220,220,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			pointColor : "rgba(220,220,220,1)",
			pointStrokeColor : "#fff",
			data : [65,59,90,81,56,55,40]
		},
		{
			fillColor : "rgba(151,187,205,0.5)",
			strokeColor : "rgba(151,187,205,1)",
			pointColor : "rgba(151,187,205,1)",
			pointStrokeColor : "#fff",
			data : [28,48,40,19,96,27,100]
		}
	]
}
                    //Get the context of the canvas element we want to select
                    var ctx = document.getElementById("myChart").getContext("2d");
                    var myNewChart = new Chart(ctx).Line(data);
                    $("#graphDialog").dialog();
                }
            });
            
//            var href = $(this).attr("href");
//            var fragment = $.param.fragment(href);
//            var paramsObj = {
//                action: fragment
//            };
//            var newUrl = $.param.fragment("", paramsObj );
//            $.bbq.pushState({url:newUrl});
//            // And finally, prevent the default link click behavior by returning false.
            e.preventDefault();
            return false;
        });
        
        $( "#button" ).button().click(function( event ) {
            var paramsObj = {
                action: "showall"
            };
            var newUrl = $.param.fragment("", paramsObj );
            $.bbq.pushState({url:newUrl});
            event.preventDefault();
        });
        
        $("#newButton").button().click(function(event) {
            $("#message").empty();
            // collect values
            var obj = {};
            obj.action = "post";
            obj.type = $("#newType option:selected").val();
            obj.value = $("#newValue").val();
            var date = $("#newDate").val();
            var time = $("#newTime").val();
            obj.datetime = date + " " + time;
            obj.note = $("#newNote").val();
            
            var json = JSON.stringify(obj);
            console.log(json);
            
            console.log("Skickar: " + obj.type + " " + obj.value + " " + obj.datetime + " " + obj.note);
            
            $.ajax({
                url: "{$action}",
                type:'POST',
                dataType: "json",
                data: {"payload": json},
                success: function(data) {
                    // If the returning data contains a value in the id attribute
                    // the update has gone well. Update UI.
                    for (var key in data) {
                        if (data.hasOwnProperty(key) && data[key]) {
                            if ($.inArray(date, datesArray) < 0) {
                                datesArray.push(date);
                                loadDatePicker("div#datePicker", null);
                                loadDatePicker("input#newDate", 'today');
                            }
                            
                            activateCurrentState("Ny post inlagd.");
                        }
                    }
                }
            });
            
            event.preventDefault();
        });
        
        loadDatePicker("div#datePicker", null, true);
        loadDatePicker("input#newDate", 'today');

        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        $('input#newTime').timepicker({timeFormat: "HH:mm", scrollbar: "true"}).timepicker('setTime', h + ":" + m);
        
        $("#bcont").on("click", function(event) {
            if ($(event.target).is('a.deletePost') || $(event.target).is('a.deletePost img')) {
                $("#message").empty();
                var href = "";
                if ($(event.target).is('a.deletePost')) {
                    href = $(event.target).attr('href');
                } else {
                    href = $(event.target).parent().attr('href');
                }
                var id = getParameterByName("id", href);
                var obj = {};
                obj.action = "delete";
                obj.id = id;
                
                var json = JSON.stringify(obj);
                
                $.ajax({
                    url: "{$action}",
                    type:'POST',
                    dataType: "json",
                    data: {"payload": json},
                    success: function(data) {
                        console.log("Stuff deleted");
                        if (data.status == "OK") {
                            activateCurrentState("Post raderad.");
                        }
                    }
                });
                event.preventDefault();
                return false;
            } else if ($(event.target).is('a.showNote') || $(event.target).is('a.showNote img')) {
                var href = "";
                if ($(event.target).is('a.showNote')) {
                    href = $(event.target).attr('href');
                } else {
                    href = $(event.target).parent().attr('href');
                }
                var id = getParameterByName("id", href);
                $('#note' + id).toggle(400);
                event.preventDefault();
                return false;
            }
        });
        
        $(window).bind( "hashchange", function(e) {
            activateCurrentState();
        });
        
        $(window).trigger( 'hashchange' );
    });
})(jQuery);
EOD;



$htmlLeft 	= "";
$htmlRight	= "<button style='margin-top: 14px; margin-bottom: 17px;' id='button'>Visa alla poster</button><div id='datePicker'></div>";

// -------------------------------------------------------------------------------------------
//
// Page specific code
//
$htmlMain = <<<EOD
<h1>Babydata</h1>
<a id="newPostLink" class="helpA" href="#">Ny information...</a>
<div id="newPost">
    <div>
    <select id="newType" name="newType">
        <option value="Height">Längd (cm)</option>
        <option value="Weight">Vikt (kg)</option>
        <option value="SkullSize">Skallmått (cm)</option>
        <option value="BreastMilk">Bröstmjölk (ml)</option>
        <option value="Formula">Ersättning (ml)</option>
        <!--<option value="Poo">Har bajsat (ja/nej)</option>-->
        <!--<option value="Pee">Har kissat (ja/nej)</option>-->
    </select>
    <input type="text" style="width:40px;" id="newValue" placeholder="värde" name="newValue" />
    <input type="text" class="date" id="newDate" name="newDate" placeholder="datum" />
    <input type="text" class="time" id="newTime" name="newTime" placeholder="tid" />
    </div>
    <div>
    <textarea placeholder="Frivillig kommentar..." id="newNote" name="newNote" rows="4" cols="50"></textarea>
    </div>
    <button id='newButton' style='margin-top: 3px;'>Skicka</button>
</div>
<div id="message"></div>
<div id="bcont"></div>

<!-- Dialog boxes -->
<div id="graphDialog" title="Linjediagram över">
    <canvas id="myChart" width="400" height="400"></canvas>
</div>

<!-- Templates for the page -->
<script id="data-template" type="text/x-handlebars-template">
    {{! Templaten tar emot ett datum och en array av babydata-objekt }}
    <div class="babydata">
        <h2>{{date}}</h2>
        {{#each data}}
            {{#with this}}
                <div class="row">
                    <span class="time">{{time}}</span><span class="literal">{{typec}}</span><span class="value">{{value}} {{unit}}</span>
                    {{#if note}}
                    <a class="showNote" href="?id={{id}}">
                        >>Notering>>
                    </a>
                    {{/if}}
                    <a class="deletePost" href="?id={{id}}">
                        <img src="{$img}close_16.png">
                    </a>
                    <div class="cleaner"></div>
                    {{#if note}}
                        <div id="note{{id}}" class="note">{{note}}</div>
                    {{/if}}
                </div>
            {{/with}}
        {{/each}}
    </div>
</script>
EOD;

// -------------------------------------------------------------------------------------------
//
// Create and print out the resulting page
//
$page = new CHTMLPage();

$page->printPage('Turnering - DMF', $htmlLeft, $htmlMain, $htmlRight, $htmlHead, $javaScript, $needjQuery);
exit;

?>