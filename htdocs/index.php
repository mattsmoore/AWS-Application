<?php


// Code to get secret whcih contains API key

require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

//Create a Secrets Manager Client
$client = new SecretsManagerClient([
    'version' => '2017-10-17',
    'region' => 'us-east-1'
]);

$secretName = 'APIKEY';

try {
    $result = $client->getSecretValue([
        'SecretId' => $secretName,
    ]);

} catch (AwsException $e) {
  echo $e->getMessage();
}

if (isset($result['SecretString'])) {
    $secret = $result['SecretString'];
} else {
    $secret = base64_decode($result['SecretBinary']);
}



$apikey = json_decode($secret, true);
$key = $apikey['APIKEY'];



$url = 'https://f5nwb68387.execute-api.us-east-1.amazonaws.com/Best/login';
  //Initiate cURL.
$ch = curl_init($url);

if(isset($_POST['log'])){
  //The JSON data.

  $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
  $jsonData = array(
    'email' => $email
  );

    //Encode the array into JSON.
  $jsonDataEncoded = json_encode($jsonData);

    //Tell cURL that we want to send a POST request.
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_SSLVERSION, 6);

    //Attach our encoded JSON string to the POST fields.
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


    //Set the content type to application/json
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "X-Api-Key: $key",
    'Content-Type: application/json'
  ));



    //Execute the request
  $re = curl_exec($ch);
  $pass = trim(stripslashes(htmlspecialchars($_POST['pass'])));

  $re = json_decode($re, true);


  if(password_verify($_POST['pass'],$re['pass'])){
    session_start();
    $_SESSION['id'] = 1;
    if(intval($re['admin'])==1){
      $_SESSION['admin'] = 1;
    }
    else{
      $_SESSION['admin'] = 0;
    }
    header("Location: menu.php");
  }
  else{
    header("Location: index.php?loginerror=1");
  }

}

include('includes/header.php');

?>

  <div style="padding-top: 10%;" class="card-body centered bg-dark">
    <h5 class="card-title text-white">Cloudheads 2021</h5>
    <p class="card-text text-white">The best customer management solution.</p>
      <div class="container">
        <div class="row">
          <div class="col-sm ">
          </div>
          <div class="col-sm">
            <form method="post">
              <div class="form-group">
                <label class="text-white" for="email">Email address</label>
                <input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                <small id="emailHelp" class="form-text text-white">We'll never share your email with anyone else.</small>
              </div>
              <br>
              <div class="form-group">
                <label class="text-white" for="pass">Password</label>
                <input type="password" class="form-control" name="pass" placeholder="Password">
              </div>

<?php

function funct(){
  session_start();
  session_destroy();
  $_SESSION = array();
  echo '<br><span class="text-success">Logout Success!</span><br>';
}

if (isset($_GET['logout'])) {
  funct();
}

$error = -1;
if(isset($_GET['loginerror'])) {
  $error = $_GET['loginerror'];
}
if($error == 1){
  echo '<br><span class="text-danger">Login Error</span><br>';
}

?>

                <br>
                <button name="log" type="submit" class="btn btn-light">Submit</button>
                <br>
                <br>
                <a class='text-white' href='signup.php'>Sign up for mail list.</a>
              </form>
            </div>
          <div class="col-sm">
        </div>
      </div>
    </div>
  </div>


<?php

include('includes/footer.php');

 ?>
