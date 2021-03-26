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

$url = 'https://f5nwb68387.execute-api.us-east-1.amazonaws.com/Best/topic';
  //Initiate cURL.
$ch = curl_init($url);

if(isset($_POST['submit'])){
  //The JSON data.


  $title = trim(stripslashes(htmlspecialchars($_POST['title'])));
  $comment = trim(stripslashes(htmlspecialchars($_POST['comment'])));
  $jsonData = array(
    'message' => $comment,
    'subject' => $title
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
    header("Location: addMsg.php?result=1");
  }
  else{
    header("Location: addMsg.php?result=2");
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
  <h1 class="text-white">Publish Email Message</h1>
  <br>
  <ul class="list-group">
    <div class="container">
      <div class="row">
        <div class="col"></div>
          <div  class="col-6">
            <form method="post">
              <div class="form-group">
                <label class="text-white" for="exampleInputEmail1">Message Subject:</label>
                <input name="title" id="title" type="text" class="form-control" id="exampleSubject" placeholder="Enter Subject">
                <br>
                <label class="text-white" for="comment">Message:</label>
                <textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
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

              <br><button id="submit" type="submit" name="submit" class="btn btn-light">Publish</button>
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
