<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Location";
$tablename = "location";
$addable = true;
//update html form to the item you need.
$formfields = array(
    array(
        "id"=>"name",
        "label"=>"Location name",
        "name"=>"name",
        "placeholder"=>"Location name",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      )

  );
  
if($user["level"] == 2){  
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
<?php
}
?>
<div class="container codetable">
    <h1><?=$objname?> Table</h1>
    <table id="table"></table>
</div>