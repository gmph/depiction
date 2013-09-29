<?php
ini_set("error_reporting", E_ALL ^ E_NOTICE);
session_start();
require("tmhOAuth.php");
$tmhOAuth = new tmhOAuth(array(
  "consumer_key" => "Dm8rSoW8xiy9K5hfmDAjw",
  "consumer_secret" => "t3L4E2ZVoGVOJqRh8dBrZnaWloxlZHTdeheYdjWqmE"
));

define("APP_URL", "http://depiction.co");

function php_self($dropqs=true) {
  $protocol = 'http';
  if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
    $protocol = 'https';
  } elseif (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')) {
    $protocol = 'https';
  }

  $url = sprintf('%s://%s%s',
    $protocol,
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );

  $parts = parse_url($url);

  $port = $_SERVER['SERVER_PORT'];
  $scheme = $parts['scheme'];
  $host = $parts['host'];
  $path = @$parts['path'];
  $qs   = @$parts['query'];

  $port or $port = ($scheme == 'https') ? '443' : '80';

  if (($scheme == 'https' && $port != '443')
      || ($scheme == 'http' && $port != '80')) {
    $host = "$host:$port";
  }
  $url = "$scheme://$host$path";
  if ( ! $dropqs)
    return "{$url}?{$qs}";
  else
    return $url;
}


function error($msg) {
?>
  <h3>Something went wrong</h3>
  <p><?php echo $msg ?></p>
<?php
}

function uri_params() {
  $url = parse_url($_SERVER['REQUEST_URI']);
  $params = array();
  foreach (explode('&', $url['query']) as $p) {
    list($k, $v) = explode('=', $p);
    $params[$k] =$v;
  }
  return $params;
}

function request_token($tmhOAuth) {
  $code = $tmhOAuth->apponly_request(array(
    'without_bearer' => true,
    'method' => 'POST',
    'url' => $tmhOAuth->url('oauth/request_token', ''),
    'params' => array(
      'oauth_callback' => php_self(false),
    ),
  ));

  if ($code != 200) {
    error("There was an error communicating with Twitter. {$tmhOAuth->response['response']}");
    return;
  }

  // store the params into the session so they are there when we come back after the redirect
  $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);

  // check the callback has been confirmed
  if ($_SESSION['oauth']['oauth_callback_confirmed'] !== 'true') {
    error('The callback was not confirmed by Twitter so we cannot continue.');
  } else {
    $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
    header("Location: " . $url);
  }
}

function access_token($tmhOAuth) {
  $params = uri_params();
  if ($params['oauth_token'] !== $_SESSION['oauth']['oauth_token']) {
    error('The oauth token you started with doesn\'t match the one you\'ve been redirected with. do you have multiple tabs open?');
    session_unset();
    return;
  }

  if (!isset($params['oauth_verifier'])) {
    error('The oauth verifier is missing so we cannot continue. did you deny the appliction access?');
    session_unset();
    return;
  }

  // update with the temporary token and secret
  $tmhOAuth->reconfigure(array_merge($tmhOAuth->config, array(
    'token'  => $_SESSION['oauth']['oauth_token'],
    'secret' => $_SESSION['oauth']['oauth_token_secret'],
  )));

  $code = $tmhOAuth->user_request(array(
    'method' => 'POST',
    'url' => $tmhOAuth->url('oauth/access_token', ''),
    'params' => array(
      'oauth_verifier' => trim($params['oauth_verifier']),
    )
  ));

  if ($code == 200) {
    $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    $_SESSION['twitter'] = $oauth_creds;
    header("Location: " . APP_URL);
    exit();
  }
}


$params = uri_params();
if (!isset($params['oauth_token'])) {
  // Step 1: Request a temporary token and
  // Step 2: Direct the user to the authorize web page
  request_token($tmhOAuth);
} else {
  // Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
  access_token($tmhOAuth);
}
