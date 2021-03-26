<?php

session_start();

if($_SESSION['id']!=1){
    header("Location: index.php");
}

include('includes/header.php');

?>

<div class="card-body centered bg-dark">
  <h1 class="text-white">Menu</h1>
  <br>
  <ul class="list-group">
    <div class="container">
      <div class="row">
        <div class="col"></div>
          <div style="border-radius: 25px;" class="col-6">
            <li class="list-group-item"><a class="text-decoration-none text-dark" href='app.php'>Email List</a></li>
            <li class="list-group-item text-dark"><a class="text-decoration-none text-dark" href='addUser.php'>Add User</a></li>
            <li class="list-group-item text-dark"><a class="text-decoration-none text-dark" href='addMsg.php'>Publish Message</a></li>
            <?php

            if($_SESSION['admin']==1){
              echo "<li class='list-group-item text-dark'><a class='text-decoration-none text-dark' href='addCRM.php'>Add New CRM User</a></li>";
            }

           ?>

          </div>
          <div class="col"></div>
        </div>
    </div>
  </ul>
  <br><a class="text-white" href="index.php?logout=true">Logout</a>
</div>



<?

include('includes/footer.php')

?>
