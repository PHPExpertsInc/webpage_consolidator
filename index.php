<?php
require 'thrive/Thrive.php';
require 'WebPageConsolidator.inc.php';

Thrive::init();

// Command line stuff here
if (php_sapi_name() == 'cli')
{
	$cli = new Thrive_CLI_Helper;
	$params = $cli->getParams();
	print_r($params); die();
}
print_r($_GET);
if (!isset($_GET['base']))
{
	echo "ERROR: A directory to parse is required.\n";
	exit;
}

$orig_basedir = $basedir = (isset($argv[1])) ? 
                            $argv[1] :
                            filter_var($_GET['base'], FILTER_SANITIZE_STRING);
//echo "Basedir: $orig_basedir";
if (!file_exists($basedir))
{
	if (!mkdir($basedir))
	{
		throw new RuntimeException('Cannot find or create the basedir: ' . $basedir);
	}
}

$outputDir = isset($argv[2]) ? $argv[2] : $orig_basedir;

$consolidator = new WebpageConsolidator;
$preHTML = '<h3 style="text-align: center; padding-bottom: 5px; margin-bottom: 5px; border-bottom: black 2px solid; width: 100%; height: 1em; background: white; text-transform: none">ORIGINAL URL: <a href="' . htmlspecialchars($originalURL) . '">' . htmlspecialchars($originalURL) . '</a></h3>';
$html = $consolidator->consolidate(array('basedir' => $basedir,
                                         'outputDir' => $outputDir,
                                         'preHTML' => $preHTML));


if (isset($_GET['delete']))
{
	$consolidator->deleteEncodedFiles($basedir);
}

if (!isset($argv[1]) && !isset($_GET['suppress']))
{
	echo $html;
}

