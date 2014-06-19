<?php

// ===========================================================================================
//
// Class CHTMLPage
//
// Creating and printing out a HTML page.
//

class CHTMLPage {

    // ------------------------------------------------------------------------------------
    //
    // Internal variables
    //

    // ------------------------------------------------------------------------------------
    //
    // Constructor
    //
    public function __construct() {
    }

    // ------------------------------------------------------------------------------------
    //
    // Destructor
    //
    public function __destruct() {
        ;
    }

    // ------------------------------------------------------------------------------------
    //
    // Print out a resulting page according to arguments
    //
    public function PrintPage($aTitle="", $aHTMLLeft="", $aHTMLMain="", $aHTMLRight="", $aHTMLHead="", $aJavaScript="", $enablejQuery=FALSE, $aSubMenu="") {

        $titlePage	= $aTitle;
        $titleSite	= WS_TITLE;
        $subTitleSite   = WS_SUB_TITLE;
        $subTitleSite   = !empty($subTitleSite) ? "<p id='subtitle'>" . $subTitleSite . "</p>" : "";
        $language	= WS_LANGUAGE;
        $charset	= WS_CHARSET;
        $stylesheet	= WS_STYLESHEET;
        $favicon 	= WS_FAVICON;
        $footer         = WS_FOOTER;
        $js = WS_JAVASCRIPT;
        $img = WS_IMAGES;

        $top 	= $this->prepareLoginLogoutMenu();
        $nav = "";
        // if(isset($_SESSION['accountUser'])) {
        if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
            $nav 	= $this->prepareNavigationBar(MENU_NAVBAR_FOR_ADMIN);
        } else {
            $nav 	= $this->prepareNavigationBar();
        }
        $w3c	= $this->prepareValidatorTools();
        $timer	= $this->prepareTimer();

        // Javascript
        $jQuery = "";
        if ($enablejQuery) {
            $jQuery  = "<script type='text/javascript' src='" . JS_JQUERY . "'></script> <!-- jQuery --> ";
            $jQuery .= "<link rel='stylesheet' href='{$js}jqeasypanel/jqeasypanel.css' type='text/css' media='screen'>";
            $jQuery .= "<script type='text/javascript' src='{$js}jqeasypanel/jquery.jqEasyPanel-min.js'></script>";
            $jQuery .= <<<EOD
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#jqeasypanel').jqEasyPanel({
                        position: 'bottom'
                    });
                });
            </script>
EOD;
        }
	$javascript = (empty($aJavaScript)) ? '' : "<script type='text/javascript'>{$aJavaScript}</script>";
        
        $ui_theme_css = JS_JQUERY_UI_CSS;

        $html = <<<EOD
<!DOCTYPE html>
<html lang="{$language}">
    <head>
        <meta charset="{$charset}" />
        <title>{$titlePage}</title>
        <link rel="shortcut icon" href="{$favicon}" />
        <link rel="stylesheet" href="{$stylesheet}" type='text/css' media='screen' />
        <link rel="stylesheet" href="{$ui_theme_css}" type='text/css' media='screen' />
        {$jQuery}
	{$aHTMLHead}
	{$javascript}
        <!-- om webbläsaren är under internet explorer 9 så fixar vi till html5-element -->
        <!--[if lt IE 9]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Kommandopanelen implementerad mha jqEasyPanel -->
        <div id="jqeasypanel">
            <ul class="panelbuttons">
                <li><a href="#"><img src="{$img}jqeasypanel_icons/pencil_48.png" alt="" />Item One</a></li>
                <li><a href="#"><img src="{$img}jqeasypanel_icons/clipboard_48.png" alt="" />Item Two</a></li>
                <li><a href="#"><img src="{$img}jqeasypanel_icons/letter_48.png" alt="" />Item Three</a></li>
                <li><a href="#"><img src="{$img}jqeasypanel_icons/diagram_48.png" alt="" />Item Four</a></li>
                <li><a href="#"><img src="{$img}jqeasypanel_icons/gear_48.png" alt="" />Item Five</a></li>
                <li><a href="#"><img src="{$img}jqeasypanel_icons/save_48.png" alt="" />Item Six</a></li>
            </ul>
            <div id="copy">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean varius odio ac nibh fringilla cursus. Fusce euismod commodo ipsum eget vehicula. Cras et mauris in enim porta fermentum. Morbi cursus orci in turpis fringilla feugiat. Vestibulum ut libero non libero pretium ornare eget nec ante.</p>	
            </div>
            <div id="jqeasypaneloptions">
                <p><label for="keepopen">Keep panel open?</label> <input name="keepopen" id="keepopen" type="checkbox" value="" /></p>
            </div>
        </div> <!-- Slut kommandopanel -->
        
        <!-- Knapp för att öppna och stänga kommandopanel -->
        <div id="jqeasytrigger">
            <a href="#" class="open">Öppna</a>
            <a href="#" class="close">Stäng</a>
        </div> <!-- Slut knapp för att öppna och stänga kommandopanel -->
        <div id="topbg"></div>
        <div id="main">
            <div id="header">
                <div id="hdr-overlay"></div>
                <h1>Baby manager</h1>
                <h2>Håll koll på din bäbis</h2>
            </div>

            <div id="nav">
            {$nav}
            </div>
            <div class="filler"></div>
            <div class="cleaner"></div>

            <div id="content">
                <div id="left">
                    {$aHTMLMain}
                    <!--
                    
                    <h3 class="ttl">Mollit Anim</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor <a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">incididunt</a> ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis <a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">nostrud</a> exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                    </p>
                    <dl>
                        <dt style="color: rgb(139, 177, 207);" class="ttl">Lorem</dt>
                        <dd>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</dd>
                        <dt style="color: rgb(139, 177, 207);" class="ttl">Ipsum</dt>
                        <dd>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</dd>
                    </dl>
                    <img id="ico" src="l.png">
                    <h4>Duis aute irure dolor</h4>
                    <ul>
                        <li><a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">Lorem ipsum</a></li>
                        <li><a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">Dolor sit amet</a></li>
                        <li><a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">Consectetur adipisicing</a></li>
                    </ul>
                    -->
                </div>

                <div id="right">
                    {$aHTMLRight}

                    <!--
                    
                    <h3 style="color: rgb(139, 177, 207);" class="ttl"><span style="background: none repeat scroll 0% 0% rgb(17, 71, 113); border-color: rgb(30, 100, 153);"></span>Lorem ipsum dolor sit amet</h3>
                    <img class="photo" src="photo1.jpg">
                    <h4>Duis aute</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ullamco laboris nisi ut aliquip.
                    </p>
                    <p class="link"><a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">Ut labore… »</a></p>

                    <h3 style="color: rgb(139, 177, 207);" class="ttl"><span style="background: none repeat scroll 0% 0% rgb(17, 71, 113); border-color: rgb(30, 100, 153);"></span>Excepteur sint occaecat</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ullamco.
                    </p>

                    <div style="background: none repeat scroll 0% 0% rgb(17, 71, 113); color: rgb(30, 100, 153);" id="rbox">
                    <span style="background: none repeat scroll 0% 0% rgb(139, 177, 207);"></span>Adipisicing elit, sed do eiusmod tempor sunt in culpa qui officia.
                    </div>

                    <button style="border-color: rgb(17, 71, 113); background: none repeat scroll 0% 0% rgb(139, 177, 207); color: rgb(30, 100, 153);" type="button">Deserunt »</button>

                    <div class="cleaner"></div>

                    <img class="photo" src="photo2.jpg">
                    <h4>Duis aute</h4>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ullamco laboris nisi ut aliquip.
                    </p>
                    <p class="link"><a style="background: none repeat scroll 0% 0% transparent; color: rgb(30, 100, 153);" href="#">Ut labore… »</a></p>
                    
                    -->
                </div>

                <div class="cleaner"></div>

                <div style="background: none repeat scroll 0% 0% rgb(17, 71, 113); color: rgb(30, 100, 153); border-color: rgb(139, 177, 207);" id="footer">
                    <a href="#">Lorem</a> |
                    <a href="#">Ipsum</a> |
                    <a href="#">Dolor</a> |
                    <a href="#">Sit amet</a> |
                    <a href="#">Aliquip</a>
                </div>
            </div>
        </div>
    </body>
</html>

EOD;

            // Print the header and page
            header("Content-Type: text/html; charset={$charset}");
            echo $html;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare the info-menu. This menu contains information about the deveveloper.
    //
    public function PrepareInfoMenu() {
        $menu = unserialize(INFO_NAVBAR);
        $theMenu = "";
        foreach ($menu as $key => $value) {
            $theMenu .= "<a href='" . $value . "'>" . $key . "</a> | ";
        }
        $theMenu = substr($theMenu, 0, -3);
        $html = <<<EOD
<div id='infobar'>
    <p>
        {$theMenu}
    </p>
</div>
EOD;

        return $html;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare the login-menu, changes look if user is logged in or not
    //
    public function PrepareLoginLogoutMenu() {

        $htmlMenu = "";

        // If user is logged in, show details about user and some links.
        // If user is not logged in, show link to login-page
        if(isset($_SESSION['accountUser'])) {
            $admHtml = "";
//            if(isset($_SESSION['groupMemberUser']) && $_SESSION['groupMemberUser'] == 'adm') {
//                $admHtml = "<a href='?p=admin'>Admin</a> ";
//            }
            $htmlMenu .= <<<EOD
<a href='?p=profile'>{$_SESSION['accountUser']}</a>
{$admHtml}
<a href='?p=logoutp'>Logga ut</a>
EOD;
        } else {
            $htmlMenu .= <<<EOD
<a href='?p=login'>Logga in</a>
EOD;
        }

        $html = <<<EOD
<div id='loginbar'>
    <p>
    {$htmlMenu}
    </p>
</div>
EOD;

        return $html;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare the header-div of the page
    //
    public function PrepareNavigationBar($menu = MENU_NAVBAR) {

        global $gPage;
        $menu = unserialize($menu);

        $nav = "<ul id='menu'>";
        foreach($menu as $key => $value) {
            // If a # is found - the user must be logged in for the menu item to be visible
            $showMenuItem = true;
            $revKey = $key;
            if (strpos($key, "#") !== FALSE) {
                $uo = CUserData::getInstance();
                $showMenuItem = $uo->isAuthenticated();
                $revKey = substr($key, 1);
            }
            if ($showMenuItem) {
                $selected = (strcmp($gPage, substr($value, 3)) == 0) ? " class='sel'" : "";
                $nav .= "<li><a{$selected} href='{$value}'><span></span>{$revKey}</a></li>";
            }
        }
        $nav .= "</ul>";

        return $nav;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare left side navigation bar.
    //
    public function PrepareLeftSideNavigationBar($menu, $title = "no title") {
        $menu = unserialize($menu);
        $theSelection = 'nothing'; // dummy value
        global $gSubPages;
        if (count($gSubPages) > 1) {
            $theSelection = $gSubPages[1];
        }
        $nav = <<< EOD
        <div class="leftSideAdminMenu">
            <div class="menuBoxHeadline">
                <div class="menuHeadlineLeft"> </div>
                <div class="menuHeadlineRight">{$title}</div>
                <div class="clearer"> </div>
            </div>
EOD;
        $nav .= "<ul>";
        foreach($menu as $key => $value) {
            $index = strpos($value, "_");
            $index = $index > 0 ? $index : 0;
            $selected = (strcmp($theSelection, substr($value, $index + 1)) == 0) ? " class='sel'" : "";
            $nav .= "<li{$selected}><a href='{$value}'>{$key}</a></li>";
        }
        $nav .= '</ul></div>';

        return $nav;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare html for validator tools
    //
    public function PrepareValidatorTools() {

            if(!WS_VALIDATORS) { return ""; }

            $refToThisPage 			= CHTMLPage::CurrentURL();
            $linkToCSSValidator	 	= "<a href='http://jigsaw.w3.org/css-validator/check/referer'>CSS</a>";
            $linkToMarkupValidator 	= "<a href='http://validator.w3.org/check/referer'>XHTML</a>";
            $linkToCheckLinks	 	= "<a href='http://validator.w3.org/checklink?uri={$refToThisPage}'>Links</a>";
            $linkToHTML5Validator	= "<a href='http://html5.validator.nu/?doc={$refToThisPage}'>HTML5</a>";

            return "<br />{$linkToCSSValidator} {$linkToMarkupValidator} {$linkToCheckLinks} {$linkToHTML5Validator}";
    }

    // ------------------------------------------------------------------------------------
    //
    // Create a errormessage if its set in the SESSION
    //
    public function getErrorMessage() {
        $html = "";

        if(isset($_SESSION['errorMessage'])) {
            $html = <<<EOD
<div class="ui-widget">
    <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
        <strong>Fel:</strong> {$_SESSION['errorMessage']}</p>
    </div>
</div>

EOD;
            unset($_SESSION['errorMessage']);
        }

        return $html;
    }

    // ------------------------------------------------------------------------------------
    //
    // Prepare html for the timer
    //
    public function PrepareTimer() {

            if(WS_TIMER) {
                    global $gTimerStart;
                    return 'Page generated in ' . round(microtime(TRUE) - $gTimerStart, 5) . ' seconds.';
            }
    }

    // ------------------------------------------------------------------------------------
    //
    // Static function
    // Redirect to another page
    // Support $aUri to be local uri within site or external site (starting with http://)
    //
    public static function RedirectTo($aUri) {
        if (strpos($aUri, "http://") !== 0) {
            $aUri = WS_SITELINK . "?p={$aUri}";
        }

        header("Location: {$aUri}");
        exit;
    }


    // ------------------------------------------------------------------------------------
    //
    // Static function
    // Create a URL to the current page.
    //
    public static function CurrentURL() {

            // Create link to current page
            $refToThisPage = "http";
            $refToThisPage .= (@$_SERVER["HTTPS"] == "on") ? 's' : '';
            $refToThisPage .= "://";
            $serverPort = ($_SERVER["SERVER_PORT"] == "80") ? '' : ":{$_SERVER['SERVER_PORT']}";
            $refToThisPage .= $serverPort . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

            return $refToThisPage;
    }

}
// End of Of Class
?>