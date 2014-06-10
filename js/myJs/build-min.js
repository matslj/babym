/*! Build time: 2014-01-23 11:33:02 */
;var tournament=tournament||{};tournament={errorClass:".errorMsg",infoMsg:"Glöm inte att spara/uppdatera!",namespace:function(a){var d=a.split(".");var c=tournament;for(var b in d){if(!c[d[b]]){c[d[b]]={};}c=c[d[b]];}},createErrorMsg:function(c){var b="<ul>";for(var a=0;a<c.length;a++){b=b+"<li>"+c[a]+"</li>";}b=b+"</ul>";$(this.errorClass).html(b);},clearErrorMsg:function(){$(this.errorClass).html("");},isDate:function(b){var a=/^\d{4}-\d{2}-\d{2}$/;if(!a.test(b)){return false;}else{var d=b.split("-")[0];var f=b.split("-")[1];var c=b.split("-")[2];if(parseInt(d,10)>2030){return false;}var e=new Date(d,f-1,c);if((e.getMonth()+1!=f)||(e.getDate()!=c)||(e.getFullYear()!=d)){return false;}}return true;}};var editor=null;(function(a){a.fn.disimgDialog=function(b){var c=a(this);var d=a.extend({},a.fn.disimgDialog.defaults,b);if((typeof d.cancel!=="undefined")&&(d.cancel===false)){d.buttons=[{text:"Stäng",click:function(){a(c).dialog("close");}}];}if(d.buttons==null){d.buttons=[{text:"Ok",click:function(){var e=true;if(typeof d.validator!=="undefined"){e=d.validator(c.attr("id"));}if(e){a("#"+c.attr("id")+"Form").submit();a(c).dialog("close");}}},{text:"Avbryt",click:function(){a(c).dialog("close");}}];}a.fn.disimgDialog.dialogify(c,d);};a.fn.pageEditDialog=function(b,d){var c=a(this);var e=a.extend({},a.fn.disimgDialog.defaults,b);if(e.buttons==null){e.buttons=[{text:"Spara och stäng",click:function(){if(editor!=null){editor.post();d.title=a("#titlePED").val();d.content=a("#contentPED").val();a.post(e.url,{page_id:d.pageId,page_name:d.pageName,redirect_on_success:"json",title:d.title,content:d.content},e.callback,"json");a(c).dialog("close");}else{console.log("Error: editor must be initialized");}}},{text:"Spara",click:function(){if(editor!=null){editor.post();d.title=a("#titlePED").val();d.content=a("#contentPED").val();a.post(e.url,{page_id:d.pageId,page_name:d.pageName,redirect_on_success:"json",title:d.title,content:d.content},e.callback,"json");}else{console.log("Error: editor must be initialized");}}},{text:"Avbryt",click:function(){a(c).dialog("close");}}];}a.fn.disimgDialog.dialogify(c,e);};a.fn.pointFilterDialog=function(b,d){var c=a(this);var e=a.extend({},a.fn.disimgDialog.defaults,b);if(e.buttons==null){e.buttons=[{text:"Spara",click:function(){tournament.config.updateScoreFilter(d.tournamentId,e.callback);}},{text:"Avbryt",click:function(){a(c).dialog("close");}}];}a.fn.disimgDialog.dialogify(c,e);};a.fn.disimgDialog.dialogify=function(b,c){if((typeof b==="undefined")||(typeof c==="undefined")){throw new Error("Error: dialogify must have all parameters set");}b.dialog({autoOpen:c.autoOpen,modal:c.modal,width:c.width,buttons:c.buttons});};a.fn.disimgDialog.defaults={autoOpen:false,width:400,modal:true,buttons:null};})(jQuery);tournament.namespace("config");tournament.config={idDateFrom:"input#dateFrom",idDateTom:"input#dateTom",idInfo:"td#info",idForm:"#turneringForm",idPointFilterForm:"#dialogPointFilterForm",idPointFilterCbx:"#pointFilterCbx",idPointFilterDiv:"#pointFilterDiv",idTable:"#minaTurneringar table tbody",images:"",sfSelector:"td.sfCell input",nrInputOnRow:4,sfOrgFrom:"orgFrom",sfOrgTom:"orgTom",sfNewFrom:"newFrom",sfNewTom:"newTom",tournamentId:0,datePattern:"yy-mm-dd",rowIdError:"Error: rowId must be an integer.",inputError:"Varje fält måste innehålla en siffra och för varje rad så måste den andra kolumnen ha större värden än den första.",responseCallbackFunction:null,init:function(d,c,a){tournament.config.tournamentId=a;tournament.config.images=c;tournament.config.responseCallbackFunction=d;$(this.idDateFrom).datepicker({onSelect:function(){$(tournament.config.idInfo).html(tournament.infoMsg);},minDate:0,dateFormat:tournament.config.datePattern});$(this.idDateTom).datepicker({onSelect:function(){$(tournament.config.idInfo).html(tournament.infoMsg);},minDate:0,dateFormat:tournament.config.datePattern});var b=$(tournament.config.idPointFilterCbx).attr("checked");if(!b){$(tournament.config.idPointFilterDiv).hide();}$(tournament.config.idPointFilterCbx).click(function(){$(tournament.config.idPointFilterDiv).toggle(400);$(tournament.config.idInfo).html(tournament.infoMsg);});$(this.idForm).ajaxForm({dataType:"json",beforeSubmit:tournament.config.validate,success:tournament.config.response});$("input").bind("keyup",function(){$(tournament.config.idInfo).html(tournament.infoMsg);});},validate:function(c,b,m){tournament.clearErrorMsg();if($("input[name=dateFrom]").prop("disabled")){$(tournament.config.idInfo).html("");return true;}var j=[];var g=$("input[name=dateFrom]").fieldValue()[0];var h=$("input[name=hourFrom]").fieldValue()[0];var k=$("input[name=minuteFrom]").fieldValue()[0];var e=$("input[name=dateTom]").fieldValue()[0];var f=$("input[name=hourTom]").fieldValue()[0];var d=$("input[name=minuteTom]").fieldValue()[0];var i=$("input[name=nrOfRounds]").fieldValue()[0];var l=$("input[name=byeScore]").fieldValue()[0];if(!tournament.isDate(g,"-")){j.push("Ogiltigt datumformat på startdatum");}if(!tournament.isDate(e,"-")){j.push("Ogiltigt datumformat på slutdatum");}var a=/^\d+$/;if(!a.test(h)||h<0||h>23){j.push("Felaktigt format på timmar på startdatum");}if(!a.test(k)||k<0||k>59){j.push("Felaktigt format på minuter på startdatum");}if(!a.test(f)||f<0||f>23){j.push("Felaktigt format på timmar på slutdatum");}if(!a.test(d)||d<0||d>59){j.push("Felaktigt format på minuter på slutdatum");}if(!a.test(i)){j.push("Antal rundor måste vara ett positivt heltal");}if(!a.test(l)){j.push("Bye score måste vara ett positivt heltal");}if(j.length!=0){tournament.createErrorMsg(j);return false;}$(tournament.config.idInfo).html("");},createTable:function(d){var c="";var a=0;for(var f in d){var b="";var g="";if(d[f].active){b="*";}if(d[f].id==tournament.config.tournamentId){g=" class='markedRow'";}c+="<tr>";c+="<td"+g+" style='width:10px; text-align: right;'>";c+=b;c+="</td>";c+="<td"+g+">";c+="<a href='?p=admin_tournament&st="+d[f].id+"'>"+d[f].fromDate+" - "+d[f].tomDate+"</a>";c+="</td>";c+="<td"+g+">";var e=parseInt(d[f].playedRounds,10);if(!e||e<=1){c+="<a href='?p=admin_tournamentdp&tId="+d[f].id+"' onclick='return confirm(\"Vill du verkligen radera den här turneringen?\")'><img style='vertical-align: bottom; border: 0;' src='"+tournament.config.images+"close_16.png' /></a>";}else{c+="&nbsp;";}c+="</td>";c+="</tr>";a++;}$(tournament.config.idTable).html(c);},response:function(a){tournament.clearErrorMsg();if(a){if(a.status!="ok"){tournament.createErrorMsg(a.message);}else{console.log(a.active);console.log(a.tournaments);tournament.config.createTable(a.tournaments);}}},updateScoreFilter:function(a,h){var b=[];var c=null;var g=-1;var i=1;var f=false;var e=$(tournament.config.idPointFilterForm).attr("action");console.log("actionUrl: "+e);$(tournament.config.sfSelector).each(function(){var k=$(this);var l=/^\d+$/g;if(!l.test(k.val())){k.addClass("errorBackground");f=true;}else{k.removeClass("errorBackground");}var j=parseInt(k.val(),10);if(i==1){c={};b.push(c);g=j;}else{if(i==2){if(g>j){k.addClass("errorBackground");f=true;console.log("error: "+g+" - "+j);}}}var m=k.attr("class");c[m]=j;if(i==tournament.config.nrInputOnRow){i=1;}else{i++;}});if(f){data={};data.errorMsg=tournament.config.inputError;h(data);}else{var d=JSON.stringify(b);console.log(d);$.ajax({url:e,type:"POST",dataType:"json",data:{tournamentId:a,scores:d},success:function(j){j={};h(j);}});}}};tournament.namespace("matches");tournament.matches={idSaveScoreButton:"input#saveScoreButton",idInfo:"span#info",idScoreSubmitDiv:"#scoreSubmitDiv",classScoreInput:"input.scoreInput",textFields:".round input:text",classInputRows:"tr.inputRow",infoMsg:"Resultat har ändrats, glöm inte att spara!",proxyFilter:[],init:function(a,c,b){$(tournament.matches.idSaveScoreButton).attr("disabled","disabled");tournament.matches.proxyFilter=b;$(tournament.matches.idSaveScoreButton).click(function(d){$(d.target).attr("disabled","disabled");$(tournament.matches.idInfo).html("");if(tournament.matches.isComplete()){$(tournament.matches.idScoreSubmitDiv).html(a);}else{$(tournament.matches.idScoreSubmitDiv).html("");}tournament.matches.saveScores(c);});$(tournament.matches.classScoreInput).bind("keyup",function(){$(tournament.matches.idScoreSubmitDiv).html("");$(tournament.matches.idSaveScoreButton).removeAttr("disabled");$(tournament.matches.idInfo).html(tournament.matches.infoMsg);});},isComplete:function(){var e=true;var c=null,b=null;var d=[];$(tournament.matches.textFields).each(function(){var i=$(this).attr("id");var f=i.indexOf("#");var g=i.substring(f+1);g=g*1;if(c==null||g<c){c=g;}if(b==null||g>b){b=g;}var h=parseInt($(this).val(),10);if(isNaN(h)){h=0;}if(!(g in d)){d[g]=0;}d[g]=Math.abs(h)+d[g];});for(var a=c;a<=b;a++){if(d[a]==""||d[a]==0){e=false;}}return e;},getProxyScore:function(e,a){var c={playerOne:0,playerTwo:0};var b=isNaN(e)?0:e;var h=isNaN(a)?0:a;var d=b-h;var g=Math.abs(d);var f=false;for(var i in tournament.matches.proxyFilter){if(tournament.matches.proxyFilter[i].diffLow<=g&&tournament.matches.proxyFilter[i].diffHigh>=g){f=true;c.playerOne=tournament.matches.proxyFilter[i].scorePlayerOne;c.playerTwo=tournament.matches.proxyFilter[i].scorePlayerTwo;break;}}if(d<0&&f){var j=c.playerOne;c.playerOne=c.playerTwo;c.playerTwo=j;}return c;},fixProxyScore:function(){$(tournament.matches.classInputRows).each(function(){var b=$(this).find(tournament.matches.classScoreInput);var a=$(this).find("span");if(typeof b!=="undefined"&&b.length>0){var c=tournament.matches.getProxyScore($(b[0]).val(),$(b[1]).val());$(a[0]).html("("+c.playerOne+")");$(a[1]).html("("+c.playerTwo+")");}});},saveScores:function(e){tournament.matches.fixProxyScore();var b={};$(tournament.matches.classScoreInput).each(function(){var g=$(this).attr("id");var h=g.indexOf("#");var f=g.substring(0,h);var i=g.substring(h+1);if(typeof b[i]==="undefined"){b[i]={};b[i]["matchId"]=i;}b[i][f]=$(this).val();});var a=[];for(var c in b){if(b.hasOwnProperty(c)){a.push(b[c]);}}var d=JSON.stringify(a);$.ajax({url:e,type:"POST",dataType:"json",data:{scores:d},success:function(f){if(f.status=="ok"){console.log("klar!!");}else{console.log(f.message);}}});}};tournament.namespace("participation");tournament.participation={idLoginJoinLeaveDiv:"#loginJoinLeave",classJoinLeaveLink:".joinLeave",idJoinLink:"#join",idLeaveLink:"#leave",idParticipantTable:"#participantList",idNrOfParticipants:"#antalDeltagare",init:function(b,a){$(tournament.participation.idLoginJoinLeaveDiv).click(function(c){if($(c.target).is(tournament.participation.idJoinLink)){tournament.participation.saveStatus(b,a,"join");c.preventDefault();}else{if($(c.target).is(tournament.participation.idLeaveLink)){tournament.participation.saveStatus(b,a,"leave");c.preventDefault();}}});},saveStatus:function(d,c,e){var a={tournamentId:c,action:e};var b=JSON.stringify(a);$.ajax({url:d,type:"POST",dataType:"json",data:{status:b},success:function(f){if(f.status=="ok"){if(f.action=="join"){$(tournament.participation.idLoginJoinLeaveDiv).html("<a id='leave' class='joinLeave' href='#'>Lämna turneringen</a>");}else{$(tournament.participation.idLoginJoinLeaveDiv).html("<a id='join' class='joinLeave' href='#'>Gå med i turneringen</a>");}tournament.participation.createParticipantTable(f.participants);}else{console.log(f.message);}}});},createParticipantTable:function(d){var b="";var a=0;for(var c in d){b+="<tr>";b+="<td>"+d[c].account+"</td>";b+="<td>"+d[c].army+"</td>";b+="</tr>";a++;}$(tournament.participation.idParticipantTable).html(b);$(tournament.participation.idNrOfParticipants).html("Antal: "+a);}};