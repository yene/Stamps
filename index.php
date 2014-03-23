<?php

require_once 'vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$uri = $request->getPathInfo();
if ('/' == $uri) {
    echo "base";
} elseif ('/show' == $uri) {
	echo "show";
}

?>