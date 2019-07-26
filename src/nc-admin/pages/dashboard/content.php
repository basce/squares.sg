<?php
  $conn = ncapputil::getConnection();

  $query = "SELECT COUNT(*) FROM `".DB_USER."`";
  $total_voter = $conn->GetOne($query);
  $total_voter_label = $total_voter == 1 ? "Voter" : "Voters";

?>
<div class="container container-main">
  <div class="row">
<?php
 echo $renderManager->createNumberBlock($total_voter, $total_voter_label);
 
?>  
  </div>
</div>
