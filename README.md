# README
By Mats Ljungquist

## What?
My first laboration with single page application.

## Used libraries
 * jQuery-bbq
 * handlebars (templating)
 * timepicker (as datepicker but for time)

## Download and install
 
Babym is on GitHub.
 
[http://github.com/matslj/babym/](http://github.com/matslj/babym/)
 
Download it either by 'git clone' or as a tag-zip (preferably the latest tag) and
then change the following define in 'config.php':

- define('WS_SITELINK',   'http://<your domain + path to where index.php is located>/'); // Link to site.

So if you for example have your index.php in localhost/babym/ then the above define should read:

define('WS_SITELINK',   'http://localhost/babym/'); // Link to site. (Observe that the sitlink MUST end with a slash)

That was the installation of the code. Now the database has to be configured. To do this
edit the file <your install directory>/sql/config.php with information about your database. Then,
back in the browser, point your browser to:

the location of WS_SITELINK followd by ?p=install, for example http://localhost/babym/?p=install

and then follow the instructions to in order to set up all the necessary tables and stored routines
required by disimg (press 'Destroy current database and create from scratch'). Some sample data
will also be installed.

Also there are one directory that may need to be added manually to the root of your installation;
log - log files will end up here. Make sure that the file permissions allow write. Toggle logging on off in config.php.

Now the complete power of babym is at your hands.

In final deployment, the install directory (<your install directory>/pages/install) should be erased.
 
An example of a pure standard installation of Babym is available here (not available yet). Review it before moving
on.
 
5. Babym, The license
 
Free software. No warranty.
 
 .
..: &copy; Mats Ljungquist, 2013