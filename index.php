<?php
require 'thrive/Thrive.php';

Thrive::init();

// Command line stuff here
if (php_sapi_name() == 'cli')
{
	$cli = new Thrive_CLI_Helper;
	$params = $cli->getParams(array('output=', 'delete'));
}
else
{
	$params = $_GET;
}

if (!isset($params['webpage']) && !isset($params['.extra0']))
{
	echo "ERROR: The web page HTML file to be consolidated is required.\n";
	echo "Try $argv[0] --help for mor information.\n";
	die(1);
}
else
{
	$params['webpage'] = $params['.extra0'];
	unset($params['.extra0']);
}

$webpage = filter_var($params['webpage'], FILTER_SANITIZE_STRING);
$basedir = dirname($webpage);

//echo "Basedir: $orig_basedir";
if (!file_exists($basedir))
{
	echo "ERROR: Cannot access $basedir: No such directory exists.\n";
	die(2);
}

if (!is_dir($basedir))
{
	echo "ERROR: $basedir is not a directory.\n";
	die(3);
}

$outputDir = isset($params['output']) ? $params['output'] : $basedir;

$consolidator = new WebpageConsolidator;

$preHTML = '';
if (isset($params['orig-url']))
{
	$preHTML = '<h3 style="text-align: center; padding-bottom: 5px; margin-bottom: 5px; border-bottom: black 2px solid; width: 100%; height: 1em; background: white; text-transform: none">ORIGINAL URL: <a href="' . htmlspecialchars($params['orig-url']) . '">' . htmlspecialchars($params['orig-url']) . '</a></h3>';
}

$html = $consolidator->consolidate($webpage, $outputDir, $preHTML);

if (isset($params['delete']) && $params['delete'] == true)
{
	$consolidator->deleteEncodedFiles($basedir);
}

if (!isset($argv[1]) && !isset($_GET['suppress']))
{
	echo $html;
}

