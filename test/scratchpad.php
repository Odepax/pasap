<?php

/**
 * This file is used for experimentation purpose.
 */

require_once "../source/AttrCollection.php";
require_once "../source/Element.php";
require_once "../source/ElementCollection.php";
require_once "../source/Pasap.php";

use Pasap\AttrCollection;
use Pasap\Element;
use Pasap\ElementCollection;
use Pasap\Pasap;

$xml = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "parsed" . DIRECTORY_SEPARATOR . "simple-pasap.php");

$document = new \DOMDocument("1.0", "UTF-8");

libxml_use_internal_errors(true);
$document->loadXML($xml);
libxml_use_internal_errors(false);
