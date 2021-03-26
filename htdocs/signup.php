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
    header("Location: signup.php?result=1");
  }
  else{
    header("Location: signup.php?result=2");
  }

}

include('includes/header.php')

?>

<div class="card-body centered bg-dark">
  <div class="container">
    <div class="row">
      <div class="col-sm ">
      </div>
      <br>
      <div class="col-sm">
        <h2 class="text-white">Please enter email to be added to our mail list.</h2>
        <form method="post">
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" name="email1" id="email1" aria-describedby="emailHelp" placeholder="Enter email">
            <small id="emailHelp" class="form-text text-white">We'll never share your email with anyone else.</small>
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

          <br>
          <button name="submit" id="submit" type="submit" class="btn btn-light">Submit</button>
          <br><br><a class="text-white" href="menu.php">Main Menu</a>
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
