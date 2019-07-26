<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Winning Code";
$tablename = "winningcode";
$addable = false;
//update html form to the item you need.
$formfields1 = array(
    array(
        "id"=>"code",
        "label"=>"Winning Code",
        "name"=>"code",
        "placeholder"=>"Winning Code",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      )
  );
  
?>
<div class="container codepage" id="search_form">
  <h1>Search Winner</h1>
  <form class="form-horizontal">
  <input type="text" class="form-control" id="code" name="code" placeholder="Winning Code" value="" required="">
  <div class="error_container">
  </div>
  <div class="form-group text-center" style="margin:20px 0">
    
      <button type="submit" class="btn btn-default" value="search" name="submit">search <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
    
  </div>
  </form>
</div>
<div class="container codepage hide" id="redemption_form">
  <form class="form-horizontal">
    <div style="text-align:center"  id="profile_image">
        <img src="">
    </div>
    <div id="winner_name">
          <span class="display_text text-center" style="font-weight: bold;">
            XXX
          </span>
      </div>
    <div class="form-group">
      <label for="winner_email" class="col-sm-3 control-label">
          Email
      </label>
      <div class="col-sm-9" id="winner_email">
          <span class="display_text">
            Email
          </span>
      </div>
    </div>
    <div class="form-group">
      <label for="winning_code" class="col-sm-3 control-label">
          Winner Code
      </label>
      <div class="col-sm-9" id="winning_code">
          <span class="display_text">
            Code
          </span>
      </div>
    </div>
     <div class="form-group">
      <label for="prize_id" class="col-sm-3 control-label">
          Prize ID
      </label>
      <div class="col-sm-9" id="prize_id">
          <span class="display_text">
            Prize ID
          </span>
      </div>
    </div>
    <div class="form-group">
      <label for="prize_won" class="col-sm-3 control-label">
          Prize
      </label>
      <div class="col-sm-9" id="prize_won">
          <span class="display_text">
            Prize
          </span>
      </div>
    </div>
    <div class="form-group">
      <label for="won_time" class="col-sm-3 control-label">
          Date Time
      </label>
      <div class="col-sm-9" id="won_time">
          <span class="display_text">
            Time
          </span>
      </div>
    </div>
    <div class="form-group">
      <label for="redeem_status" class="col-sm-3 control-label">
          Status
      </label>
      <div class="col-sm-9" id="redeem_status">
          <span class="display_text">
            redeem
          </span>
      </div>
    </div>
<?php
$conn = ncapputil::getConnection();    
    if($user["level"] == 3){
      //restrict location
      $query = "SELECT a.locationid, b.name FROM ( SELECT locationid FROM `".DB_ADMIN."_location` WHERE aid = ? ) a LEFT JOIN `".DB_LOCATION."` b ON a.locationid = b.id";
      $locationInfo = $conn->GetRow($query, array($user["aid"]));
?>
  <div class="form-group">
      <label for="location" class="col-sm-3 control-label">
          Location
      </label>
      <div class="col-sm-9">
          <span class="display_text">
            <?=$locationInfo["name"]?>
          </span>
      </div>
      <input type="hidden" id="location_select_force" value="<?=$locationInfo["locationid"]?>">
    </div>
<?php      
    }else{      
?>    
    <div class="form-group">
      <label for="location_select" class="col-sm-3 control-label">Location</label>
      <div class="col-sm-9"  id="location">
        <span class="display_text">
          Location
        </span>
        <select class="form-control" id="location_select" name="level">
<?php
    $query = "SELECT id, name FROM `".DB_LOCATION."` ORDER BY name DESC";
    $locations = $conn->GetArray($query);
    foreach($locations as $key=>$location){?>          
          <option value="<?=$location["id"]?>"><?=$location["name"]?></option>
<?php } ?>
          </select>
      </div>
    </div>
<?php
}
?>    
    <div class="error_container">
    </div>
    <input type="hidden" id="redeem_wid" value="0">
    <div class="text-center">
      
        <button type="submit" class="btn btn-default" value="redeem" name="submit">redeem <span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>
        <button type="submit" class="btn btn-default" value="unredeem" name="submit">unredeem <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>

    </div>
  </form>
</div>
<?php
  if($user["level"] != 3){
?>
<div class="container codetable hide" id="redeemlogtable">
    <h1>Claim History</h1>
    <table id="table"></table>
</div>
<?php
  }
?>