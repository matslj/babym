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
        
    <script src="{$js}timepicker/jquery.timepicker.js"></script>
        
    <link rel="stylesheet" type="text/css" href="{$js}timepicker/jquery.timepicker.css" />
        
    <style>
        /* Gör om css för dagens datum till samma css som alla andra (ovalda) datum */
        .ui-datepicker-today a.ui-state-highlight {
            background: url("{$js}jquery-ui/sunny/images/ui-bg_gloss-wave_60_fece2f_500x100.png") repeat-x scroll 50% 50% #FECE2F;
            border: 1px solid #D19405;
            color: #4C3000;
            font-weight: bold;
        }
        
        .specialDate { 
            background-color: #6F0 !important;
        }
        
        .babydata {
            border: 1px solid white;
            background: yellow;
        }
        
        .babydata .row .literal {
            width: 40px;
        }
        
        .babydata span.value {
            width: 20px;
        }

        .babydata span.unit {
            width: 20px;
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
        
        div#newPost {
            margin-bottom: 10px;
        }
        
        #newPostLink {
            display: block;
            padding-bottom: 10px;
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

    function retrieveAndDisplayBabyData(aMethod, json, message) {
        // First, create a map with date as key and and arrays of date
        // related baby data objects as values.
        $("#bcont").empty();
        
        // Just to make it neat
        if (typeof json === "undefined") {
            json = "";
        }

        // Present a message first in the content block if a message is recieved.
        if (typeof message !== "undefined") {
            $("#bcont").append("<div class='message'>" + message + "</div>");
        }

        // Call the server side. The call takes to parameters:
        // * method - the method of choice (for availible methods see PFilterByDate.php)
        // * payload - the data for the method. May be empty.
        $.ajax({
            url: "{$action}",
            type:'POST',
            dataType: "json",
            data: {"method": aMethod, "payload": json},
            success: function(data) {
                // ********************************************************
                // * Display the retrieved data using a handlebars template.
                // *
                var lastDate = null;
                var tempMap = {};
                var tempArray = [];
                for (key in data) {
                    if (data.hasOwnProperty(key)) {
                        if (lastDate == null || lastDate != data[key].date) {
                            lastDate = data[key].date;
                            tempMap[lastDate] = [];
                        }
                        tempMap[lastDate].push(data[key]);
                    }
                }
                // Feed the template with the map created above.
                for (key in tempMap) {
                    if (tempMap.hasOwnProperty(key)) {
                        $("#bcont").append(template({date: key, data: tempMap[key]}));
                    }
                }
            }
        });
    }
    
    function loadDatePicker(picker, aDate) {
        var options = {
            firstDay: 1,
            dateFormat: "yy-mm-dd",
            dayNamesShort: ["Sön", "Mån", "Tis", "Ons", "Tor", "Fre", "Lör"],
            monthNames: ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"],
            dayNamesMin: ["Sö", "Må", "Ti", "On", "To", "Fr", "Lö"],
            onSelect: function(dateText) {
                console.log(dateText);
                var tempDate = JSON.stringify(dateText);
                console.log(tempDate);
                retrieveAndDisplayBabyData("selectDate", tempDate);
            },
            beforeShowDay: function (date) {
                var day = 0,
                    month = 0;
                day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
                month = date.getMonth() + 1;
                month = month < 10 ? "0" + month : month;
                var theday = date.getFullYear() + '-' + month +'-'+ day;
                return [true,$.inArray(theday, datesArray) >=0 ? "specialDate":''];
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
        
        $( "#button" ).button().click(function( event ) {
            retrieveAndDisplayBabyData("showall");
            event.preventDefault();
        });
        
        $("#newButton").button().click(function(event) {
            // collect values
            var obj = {};
            obj.type = $("#newType option:selected").val();
            obj.value = $("#newValue").val();
            var date = $("#newDate").val();
            var time = $("#newTime").val();
            obj.datetime = date + " " + time;
            obj.note = $("#newNote").val();
            
            var json = JSON.stringify(obj);
            console.log(json);
            
            console.log("Skickar: " + obj.type + " " + obj.value + " " + obj.datetime + " " + obj.note);
            retrieveAndDisplayBabyData("post", json, "Uppdaterat med:");
            
            if ($.inArray(date, datesArray) < 0) {
                datesArray.push(date);
                loadDatePicker("div#datePicker", null);
                loadDatePicker("input#newDate", 'today');
            }
            
            event.preventDefault();
        });
        
        loadDatePicker("div#datePicker", null);
        loadDatePicker("input#newDate", 'today');

        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        $('input#newTime').timepicker({timeFormat: "HH:mm", scrollbar: "true"}).timepicker('setTime', h + ":" + m);
        
        $("#bcont").on("click", function(event) {
            if ($(event.target).is('a.deletePost') || $(event.target).is('a.deletePost img')) {
                var href = "";
                if ($(event.target).is('a.deletePost')) {
                    href = $(event.target).attr('href');
                } else {
                    href = $(event.target).parent().attr('href');
                }
                var id = getParameterByName("id", href);
                var json = JSON.stringify(id);
                retrieveAndDisplayBabyData("delete", json);
                event.preventDefault();
                return false;
            }
        });
    });
})(jQuery);
EOD;



$htmlLeft 	= "";
$htmlRight	= "<button id='button'>Visa alla poster</button><div id='datePicker'></div>";

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
        <option value="Poo">Har bajsat (ja/nej)</option>
        <option value="Pee">Har kissat (ja/nej)</option>
    </select>
    <input type="text" style="width:40px;" id="newValue" placeholder="värde" name="newValue" />
    <input type="text" class="date" id="newDate" name="newDate" placeholder="datum" />
    <input type="text" class="time" id="newTime" name="newTime" placeholder="tid" />
    </div>
    <div>
    <textarea placeholder="Frivillig kommentar..." id="newNote" name="newNote" rows="4" cols="50"></textarea>
    </div>
    <button id='newButton'>Skicka</button>
</div>
<div id="bcont"></div>

<!-- Templates for the page -->
<script id="data-template" type="text/x-handlebars-template">
    {{! Templaten tar emot ett datum och en array av babydata-objekt }}
    <div class="babydata">
        <h2>{{date}}</h2>
        {{#each data}}
            {{#with this}}
                <div class="row">
                    <span class="literal">{{typec}}</span>:<span class="value">{{value}}</span><span class="unit">{{unit}}</span>
                    <a class="deletePost" href="?id={{id}}">
                        <img src="{$img}close_16.png">
                    </a>
                    <div class="clear"></div>
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