<?php
// we need a REST Library, found HttpFul
// Download from http://phphttpclient.com/
// You may need to add permissions for MAC OS with Apache
//     sudo chmod -R 755 /library/webserver/documents
//     (assuming default folder)

include('./httpful.phar');

// define some constants for this quick sample
define("CONSUMER_KEY", "Your app's Client ID");
define("CONSUMER_SECRET", "Your app's Client Secret");
define("BASE_URL", 'https://developer.api.autodesk.com');

// if the request URL contains the method being requested
// for instance, a call to view.and.data.php/authenticate
// will redirect to the function with the same name

if (isset($_SERVER['PATH_INFO'])) {
  $apiName = explode('/', trim($_SERVER['PATH_INFO'],'/'))[0];
  if (!empty($apiName)){
      // get the function by API name
      try{ $apiFunction = new ReflectionFunction($apiName);}
      catch (Exception $e) { echo ('API not found');}

      // run the function and 'echo' it's reponse
      if ($apiFunction != null) echo $apiFunction->invoke();

      exit(); // no HTML output
  }
}

// now the APIs
function authenticate(){
    // request body (client key & secret)
    $body = sprintf('client_id=%s' .
                    '&client_secret=%s' .
                    '&grant_type=client_credentials' .
                    '&scope=data:read',
                    CONSUMER_KEY, CONSUMER_SECRET);

    // prepare a POST request following the documentation
    $response =
        \Httpful\Request::post(
          BASE_URL . '/authentication/v1/authenticate')
        ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->body($body)
        ->send(); // make the request

    if ( $response->code == 200)
        // access the JSON response directly
        return $response->body->access_token;
    else
        // something went wrong...
        throw new Exception('Cannot authenticate');
}
?>
