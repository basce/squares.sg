<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Country";
$tablename = "country";
$addable = true;
//update html form to the item you need.
$formfields = array(
    array(
      "id"=>"name",
      "label"=>"Country name",
      "name"=>"name",
      "placeholder"=>"Country name",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    ),
    array(
      "id"=>"code",
      "label"=>"Country Code",
      "name"=>"code",
      "placeholder"=>"Country Code",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    ),
    array(
      "id"=>"default_language",
      "label"=>"Default Language",
      "name"=>"default_language",
      "placeholder"=>"EN",
      "default"=>"EN",
      "required"=>true,
      "type"=>"text"
    ),
    array(
      "id"=>"list_id",
      "label"=>"ActiveCampaign List ID",
      "name"=>"list_id",
      "placeholder"=>"",
      "default"=>"",
      "required"=>true,
      "type"=>"text"
    )
  );
  
if($user["level"] == 4){  
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
      <button type="submit" class="btn btn-default" value="add" name="submit">Done</button>
    </div>
  </div>
  <input type="hidden" name="id" value="" />
  <input type="hidden" name="method" value="add" />
  <input type="hidden" name="nctable" value="<?=$tablename?>" />
</form>
</div>
<?php
}
?>
<div class="container codetable">
    <h1><?=$objname?> Table</h1>
    <table id="table"></table>
</div>