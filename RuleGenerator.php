<?php

$accessToken = $argv[1];
$owlURL = 'http://fowl.local';

$getClientURL = $owlURL . '/v1/clients/rios';
$getRoutesURL = $owlURL . '/v1/system/routes';

$routes = getOWL($getRoutesURL, $accessToken);

$routeNames = array('GET-v1-verified-search', 'GET-v1-verified-search-geo', 'GET-v1-verified-:businessId');

print_r(setRules($routeNames, $routes->routes));

function setRules($routeNames, $routes)
{
    $rules = array();
    foreach($routeNames as $routeName) {
        $rules = array_merge_recursive($rules, json_decode($routes->$routeName->routingJson, true));
    }
    return json_encode($rules);
}

function getOWL($url, $accessToken)
{
    return callOWL($url, $accessToken, 'GET');
}

function postOWL($url, $accessToken, $params = false)
{
    return callOWL($url, $accessToken, 'POST', $params);
}

function callOWL($url, $accessToken, $method, $params = false)
{
    $opts = array(
        'http'=>array(
            'method'=>$method,
            'header'=>"access-token: " . $accessToken . "\r\n",
        )
    );

    if($params) {
        $opts[0]['http']['content'] = $params;
    }

    $context = stream_context_create($opts);

    return json_decode(file_get_contents($url, false, $context))->response;
}