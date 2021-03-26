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

$url = 'https://f5nwb68387.execute-api.us-east-1.amazonaws.com/Best/addemail';
  //Initiate cURL.
$ch = curl_init($url);

if(isset($_POST['submit'])){
  //The JSON data.


  $email = trim(stripslashes(htmlspecialchars($_POST['email1'])));
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

  $re = curl_exec($ch);

  if($re==1){
    header("Location: addUser.php?result=1");
  }
  else{
    header("Location: addUser.php?result=2");
  }

}

session_start();

if(!isset($_SESSION['id'])){
    header("Location: index.php");
}

include('includes/header.php');

?>

<div class="card-body centered bg-dark">
  <br>
  <h1 class="text-white">Add Email to Mail List</h1>
  <br>
  <ul class="list-group">
    <div class="container">
      <div class="row">
        <div class="col"></div>
          <div  class="col-6">
            <form method="post">
              <div class="form-group">
                <label class="text-white" for="exampleInputEmail1">Email address</label>
                <input name="email1" id="email1" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
              </div>

              <?php

              $error = -1;
              if(isset($_GET['result'])) {
                $error = $_GET['result'];

                if($error == 1){
                  echo '<br><span class="text-success">Success!</span><br>';
                }
                else{
                  echo '<br><span class="text-danger">Error.</span><br>';
                }
              }

              ?>

              <br><button id="submit" type="submit" name="submit" class="btn btn-light">Add</button>
              <br><br><a class="text-white" href="menu.php">Main Menu</a>
            </form>
          </div>
          <div class="col"></div>
        </div>
    </div>
  </ul>
</div>


<?

include('includes/footer.php')

?>
