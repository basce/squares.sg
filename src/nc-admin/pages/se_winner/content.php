<?php
$objname = "App Notification";
$tablename = "appnotification";

$conn = ncapputil::getConnection();


//get top 3 item 
$query = "SELECT `index`, unique_code FROM `".DB_SE_WINNER_TOP3."`";
$top3 = $conn->GetArray($query);

$top1unique_code = "";
$top2unique_code = "";
$top3unique_code = "";

foreach($top3 as $key=>$value){
  if($value["index"] == 1){
    $top1unique_code = $value["unique_code"];
  }else if($value["index"] == 2){
    $top2unique_code = $value["unique_code"];
  }else if($value["index"] == 3){
    $top3unique_code = $value["unique_code"];
  }
}

$query = "SELECT unique_code FROM `".DB_SE_WINNER."`";
$winners = $conn->GetCol($query);

$unique_codes = "";
if(sizeof($winners)){
  $unique_codes = implode(", ",$winners);
}

?>
<div class="container codepage" id="form1">
  <h1>Update Winners</h1>
  <form class="form-horizontal" method="POST">
  <div class="form-group">
    <label for="winner1" class="col-sm-3 control-label">Winner No 1</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="winner1" name="winner1" placeholder="unique code for winner" value="<?=$top1unique_code?>">
    </div>
  </div>
  <div class="form-group">
    <label for="winner2" class="col-sm-3 control-label">Winner No 2</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="winner2" name="winner2" placeholder="unique code for winner" value="<?=$top2unique_code?>">
    </div>
  </div>
  <div class="form-group">
    <label for="winner3" class="col-sm-3 control-label">Winner No 3</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="winner3" name="winner3" placeholder="unique code for winner" value="<?=$top3unique_code?>">
    </div>
  </div>
  <div class="form-group">
    <label for="otherwinner" class="col-sm-3 control-label">Other Winners</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="otherwinner" name="otherwinner" placeholder="unique codes for winners" value="<?=$unique_codes?>">
      <small id="otherwinnerHelp" class="form-text text-muted">Use comman to include multiple winners.</small>
    </div>
  </div>
  <div class="error_container">
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-default" value="submit" name="submit">Submit</button>
    </div>
  </div>
  <input type="hidden" name="id" value="0" />
  <input type="hidden" name="method" value="add" />
  <input type="hidden" name="nctable" value="admin" />
</form>
</div>