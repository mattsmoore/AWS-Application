<?php

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

$url = 'https://f5nwb68387.execute-api.us-east-1.amazonaws.com/Best/addCRM';
  //Initiate cURL.
$ch = curl_init($url);


if(isset($_POST['submit1'])){
  //The JSON data.

  if(isset($_POST['admin2'])){
    $ad = "1";
  }
  else{
    $ad = "0";
  }

  $email = trim(stripslashes(htmlspecialchars($_POST['email2'])));
  $pass = password_hash(trim(stripslashes(htmlspecialchars($_POST['pass2']))),PASSWORD_DEFAULT);

  $jsonData = array(
    "email" => $email,
    "pass" => $pass,
    "admin" => $ad
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

  $re = curl_exec($ch);

  if($re==1){
    header("Location: addCRM.php?result=1");
  }
  else{
    header("Location: addCRM.php?result=2");
  }

}

session_start();

if(!isset($_SESSION['id'])){
    header("Location: index.php");
}

if($_SESSION['admin']==0){
  header("Location: index.php");
}

include('includes/header.php');

?>

<div class="card-body centered bg-dark">
  <br>
  <h1 class="text-white">Add new CRM User</h1>
  <br>
  <div class="container">
    <div class="row">
      <div class="col"></div>
      <div  class="col-6">
        <form method="post">
          <div class="form-group">
            <label class="text-white" for="email2">Email address</label>
            <input id="email2" name="email2" type="email" class="form-control" aria-describedby="emailHelp" placeholder="Enter email">
            <small id="emailHelp" class="form-text text-muted"></small>
          </div>
          <br>
          <div class="form-group">
            <label class="text-white" for="pass2">Password</label>
            <input id="pass2" name="pass2" type="password" class="form-control" placeholder="Password">
          </div>
          <br>
          <div class="form-check">
            <input id="admin2" name="admin2" class="form-check-input" type="checkbox">
            <label style="float:left;" class="text-white form-check-label" for="admin2">
              Admin
            </label>
          </div>

          <?php

          $error = -1;
          if(isset($_GET['result'])) {
            $error = $_GET['result'];

            if($error == 1){
              echo '<br><span class="text-success">Success!</span><br>';
            }
            else{
              echo $re;
              echo '<br><span class="text-danger">Error.</span><br>';
            }
          }

          ?>

          <button name="submit1" id="submit1" type="submit" class="btn btn-light">Submit</button>
          <br><br><a class="text-white" href="menu.php">Main Menu</a>
        </form>
      </div>
      <div class="col"></div>
    </div>
  </div>
</div>

<?php

include('includes/footer.php')

?>
