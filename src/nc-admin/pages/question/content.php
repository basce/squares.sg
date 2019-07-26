<?php

/*
This is the sample file for table with form. can be used for changing data of row in db

 */
$objname = "Question";
$tablename = "question";
$addable = false;
//update html form to the item you need.
$formfields = array(
    array(
        "id"=>"name",
        "label"=>"Answer(s)",
        "name"=>"name",
        "placeholder"=>"use comma (,) for multiple answers",
        "default"=>"",
        "required"=>true,
        "type"=>"text"
      ),
    array(
        "id"=>"hints",
        "label"=>"Hints",
        "name"=>"hints",
        "placeholder"=>"Hints, appear on game screen",
        "default"=>"",
        "required"=>true,
        "type"=>"textarea"
      ),
    array(
        "id"=>"image",
        "label"=>"Image filename",
        "name"=>"image",
        "placeholder"=>"filename in /images/icons/ folder",
        "default"=>"",
        "required"=>false,
        "type"=>"text"
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