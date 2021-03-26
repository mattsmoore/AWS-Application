<?php

require 'vendor/autoload.php';
use Aws\DynamoDb\DynamoDbClient;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

$client = new DynamoDbClient([
    'region'  => 'us-east-1',
    'version' => 'latest'
]);

$result = $client->scan(array(
    'TableName' => 'Customers',
    'Select' => 'ALL_ATTRIBUTES'
));

$SC = new SecretsManagerClient([
    'version' => '2017-10-17',
    'region' => 'us-east-1'
]);

$secretName = 'APIKEY';

try {
    $RES = $SC->getSecretValue([
        'SecretId' => $secretName,
    ]);
} catch (AwsException $e) {
  echo $e->getMessage();
}

if (isset($RES['SecretString'])) {
    $secret = $RES['SecretString'];
} else {
    $secret = base64_decode($RES['SecretBinary']);
}


$apikey = json_decode($secret, true);
$key = $apikey['APIKEY'];

session_start();

if(!isset($_SESSION['id'])){
  header("Location: index.php");
}

$page = !empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
$total = count( $result['Items']); //total items in array
$limit = 4; //per page
$totalPages = ceil( $total/ $limit ); //calculate total pages
$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
$offset = ($page - 1) * $limit;
if( $offset < 0 ) $offset = 0;
$result['Items'] = array_slice( $result['Items'], $offset, $limit );



$url = 'https://f5nwb68387.execute-api.us-east-1.amazonaws.com/Best/deleteemail';
  //Initiate cURL.
$ch = curl_init($url);


if(isset($_POST['submit'])){
  //The JSON data.


  $email = trim(stripslashes(htmlspecialchars($_POST['submit'])));
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
    header("Location: app.php?result=1");
  }
  else{
    header("Location: app.php?result=2");
  }

}

include('includes/header.php');

?>


<div class="card-body centered bg-dark">
  <h1 class="text-white">Customer List
  </h1>
  <?php

  $error = -1;
  if(isset($_GET['result'])) {
    $error = $_GET['result'];

    if($error == 1){
      echo '<br><span class="text-center text-success">Successful Deletion!</span><br>';
    }
    else{
      echo '<br><span class="text-center text-danger">Error, email may not be confirmed in SNS!</span><br>';
    }
}



   ?>
	<br>
    <div class="container">
  		<div class="row">
    		<div class="col"></div>
		    <div class="col-6">
          <table class="table bg-light">
            <thead class="thead-light">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Email</th>
                <th scope="col">Delete</th>
              </tr>
            </thead>
            <form method="post">
              <tbody>
                <br>


<?php

# pagination
# https://stackoverflow.com/questions/26451362/how-to-add-php-pagination-in-arrays/31989815

$link = 'app.php?page=%d';
for($i = 0; $i<count($result['Items']); $i++){
	echo '<tr><th scope="row">'. ((($page-1)*($limit))+($i+1)) .'</th><td>' . $result['Items'][$i]["email"]["S"] . '</td><td>
	<button name="submit" id="submit" type="submit" class="btn btn-danger" value="'.$result['Items'][$i]["email"]["S"].'">Delete</button></td></tr>';
};
$pagerContainer .= '</tbody></form></table>';
if( $totalPages != 0 )
{
  if( $page == 1 )
  {
    $pagerContainer .= '';
  }
  else
  {
    $pagerContainer .= sprintf( '<a class="text-white" href="' . $link . '" > &#171; prev page</a>:', $page - 1 );
  }
  $pagerContainer .= ' <span class="text-white"> page <strong>' . $page . '</strong> of ' . $totalPages . '</span>';
  if( $page == $totalPages )
  {
    $pagerContainer .= '';
  }
  else
  {
    $pagerContainer .= sprintf( '<a class="text-white" href="' . $link . '"> next page &#187; </a>', $page + 1 );
  }
}
$pagerContainer .= '<br><br><a class="text-white" href="menu.php">Main Menu</a>';

echo $pagerContainer;


?>

	    </div>
      <div class="col"></div>
	    <br><br>
    </div>
  </div>
</div>


<?php

include('includes/footer.php');


?>
