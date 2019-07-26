<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Distributor";
$tablename = "distributor";
$addable = true;
//update html form to the item you need.
/*
$formfields = array(
    array(
      "id"=>"name",
      "label"=>"Name",
      "name"=>"name",
      "placeholder"=>"Name",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    ),
    array(
      "id"=>"email",
      "label"=>"Email",
      "name"=>"email",
      "placeholder"=>"Email",
      "default"=>"",
      "required"=>true,
      "type"=>"email"
    ),
    array(
      "id"=>"",
      "label"=>"ActiveCampaign domain",
      "name"=>"domain",
      "placeholder"=>"URL",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    ),
    array(
      "id"=>"key",
      "label"=>"ActiveCampaign key",
      "name"=>"key",
      "placeholder"=>"Key",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    )
  );*/
  

?>
<div class="container codepage" id="form1">
  <h1><span>Add New <?=$objname?></span><button id="add" class="btn btn-link hide">
            <i class="glyphicon glyphicon-plus"></i> Add New <?=$objname?>
        </button></h1>
  <form class="form-horizontal">

  <!-- text -->
  <div class="form-group">
    <label for="distributor_id" class="col-sm-3 control-label">Distributor ID</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="distributor_id" name="distributor_id" placeholder="Distributor ID" value="" required="">
    </div>
  </div>

  <!-- text -->
  <div class="form-group">
    <label for="name" class="col-sm-3 control-label">Name</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="" required="">
    </div>
  </div>

    <!-- text -->
  <div class="form-group">
    <label for="contact_no" class="col-sm-3 control-label">Contact No</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact No." value="" required="">
    </div>
  </div>
    <!-- text -->
  <div class="form-group">
    <label for="code" class="col-sm-3 control-label">Email</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="" required="">
    </div>
  </div>
<?php
if($user["level"] == 1){
  $query = "SELECT locationid FROM `".DB_ADMIN."_location` WHERE aid = ?";
  $locationid = $conn->GetOne($query, array($user["aid"]));
?>
  <input type="hidden" id="country" name="country" value="<?=$locationid?>">
<?php }else{ ?>
  <div class="form-group">
    <label for="country" class="col-sm-3 control-label">Country</label>
    <div class="col-sm-9">
      <select class="form-control" id="country" name="country">
        <?php
          $query = "SELECT id, name, code FROM `".DB_COUNTRY."`";
          $country_list = $conn->GetArray($query);

          foreach($country_list as $key=>$value){
            ?><option value="<?=$value["id"]?>"><?=$value["name"]?></option><?php
          }
        ?>
          </select>
    </div>
  </div>
<?php } ?>
  <div class="form-group">
    <label for="exclusion" class="col-sm-3 control-label">Exclusion Flag</label>
    <div class="col-sm-9">
      <select class="form-control" id="exclusion" name="exclusion">
          <option value="0">false</option>
          <option value="1">true</option>
      </select>
    </div>
  </div>
  <div class="error_container">
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-default" value="add" name="submit">Add<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
    </div>
  </div>
  <input type="hidden" name="id" value="" />
  <input type="hidden" name="method" value="add" />
  <input type="hidden" name="nctable" value="<?=$tablename?>" />
</form>
</div>
<div class="container codetable">
    <h1><?=$objname?> Table</h1>
    <table id="table"></table>
</div>