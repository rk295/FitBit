<?php
include('conf.php');
    
    // Request token path
    $req_url = $baseUrl . '/oauth/request_token';

    // Authorization path
    $authurl = $baseUrl . '/oauth/authorize';

    // Access token path
    $acc_url = $baseUrl . '/oauth/access_token';

    // Call back URL - otherwise known as this page
    // The below ought to self figure this out, otherwise
    // just set the variable to the full internet accessible
    // url for the page
    $callbackUrl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

    // Strt session to store the information between calls
    session_start();

    // In state=1 the next request should include an oauth_token.
    // If it doesn't go back to 0
    if ( !isset($_GET['oauth_token']) && $_SESSION['state']==1 ) $_SESSION['state'] = 0;

    try 
    {
        // Create OAuth object
        $oauth = new OAuth($conskey,$conssec,OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_AUTHORIZATION);

        // Enable ouath debug (should be disabled in production)
        $oauth->enableDebug();

        if ( $_SESSION['state'] == 0 ) 
        {
            // Getting request token. Callback URL is the Absolute URL to which the server provider 
            // will redirect the User back when the obtaining user authorization step is completed.
            $request_token_info = $oauth->getRequestToken($req_url, $callbackUrl);

            // Storing key and state in a session.
            $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
            $_SESSION['state'] = 1;

            // Redirect to the authorization.
            header('Location: '.$authurl.'?oauth_token='.$request_token_info['oauth_token']);
            exit;
        } 
        else if ( $_SESSION['state']==1 ) 
        {
            // Authorized. Getting access token and secret
            $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
            $access_token_info = $oauth->getAccessToken($acc_url);

            // Storing key and state in a session.
            // useful to store it in the session incase you want to do more with this page
            // however for my use, storing it in a config file was more appropriate
            $_SESSION['state'] = 2;
            $_SESSION['token'] = $access_token_info['oauth_token'];
            $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
        } 

        // Setting asccess token to the OAuth object
        // $oauth->setToken($_SESSION['token'],$_SESSION['secret']);

        // Performing API call 
        // $oauth->fetch($apiCall);

        // Getting last response
        // $response = $oauth->getLastResponse();

        // $json = json_encode($response);

        // Initializing the simple_xml object using API response 
        // $xml = simplexml_load_string($response);
    } 
    catch( OAuthException $E ) 
    {
        print_r($E);
    }
?>
<html><body>
<p>Looks like authentication succeeded, the two useful bits of information are as follows:</p>
<ol>
    <li><strong>OAuth Token Secret</strong>: <?php echo $_SESSION['secret'];?></li>
    <li><strong>OAuth Token</strong>: <?php echo $_SESSION['token'];?></li>
</ol>
<p>You might be able to copy and paste this directly into conf.php:</p>
<pre>
$secret = '<?php echo $_SESSION['secret'];?>';
$token = '<?php echo $_SESSION['token'];?>';
</pre>
<p>fin!</p>
</body></html>
