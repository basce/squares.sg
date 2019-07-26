<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Prize";
$tablename = "prize";
$addable = false;
//update html form to the item you need.
$formfields = array(
    array(
        "id"=>"name",
        "label"=>"prize's name",
        "name"=>"name",
        "placeholder"=>"prize's name",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"shortname",
        "label"=>"prize's name (short)",
        "name"=>"shortname",
        "placeholder"=>"prize's name (short)",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"name_in_sentence",
        "label"=>"prize's name in sentece",
        "name"=>"name_in_sentence",
        "placeholder"=>"",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"redemption_detail",
        "label"=>"redemption detail",
        "name"=>"redemption_detail",
        "placeholder"=>"",
        "default"=>"",
        "required"=>false,
        "type"=>"textarea"
      ),
    array(
        "id"=>"tnc",
        "label"=>"Term & condition",
        "name"=>"tnc",
        "placeholder"=>"",
        "default"=>"",
        "required"=>false,
        "type"=>"textarea"
      ),
    array(
        "id"=>"startdate",
        "label"=>"start date",
        "name"=>"startdate",
        "placeholder"=>"",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"enddate",
        "label"=>"end date",
        "name"=>"enddate",
        "placeholder"=>"",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"multiplier",
        "label"=>"multiplier",
        "name"=>"multiplier",
        "placeholder"=>"",
        "default"=>"",
        "required"=>true,
        "type"=>"number"
      ),
    array(
        "id"=>"numberofplay",
        "label"=>"number of plays",
        "name"=>"numberofplay",
        "placeholder"=>"",
        "default"=>"",
        "required"=>true,
        "type"=>"number"
      )
  );
  
?>
<div class="container codepage" id="form1">
  <h1><span>Add New <?=$objname?></span><button id="add" class="btn btn-link hide">
            <i class="glyphicon glyphicon-plus"></i> Add New <?=$objname?>
        </button></h1>
  <form class="form-horizontal">
  <?php $adminManager->tableForm($formfields); ?>
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