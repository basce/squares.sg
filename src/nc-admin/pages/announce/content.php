<?php
$objname = "App Notification";
$tablename = "appnotification";

  
?>
<div class="container codepage" id="form1">
  <h1><span>Add New <?=$objname?></span><button id="add" class="btn btn-link hide">
            <i class="glyphicon glyphicon-plus"></i> Add New <?=$objname?>
        </button></h1>
  <form class="form-horizontal">
  <div class="form-group">
    <label for="publishtime" class="col-sm-3 control-label">Publish Time</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="publishtime" name="publishtime" placeholder="Publish Time" value="<?=date("Y-m-d H:i:s")?>" required>
    </div>
  </div>
  <div class="form-group">
    <label for="msg" class="col-sm-3 control-label">Message</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="msg" name="msg" placeholder="(max 95 chars)" value="" required>
    </div>
  </div>
  <div class="form-group">
    <label for="target" class="col-sm-3 control-label">Target</label>
    <div class="col-sm-9">
      <select class="form-control" id="target" name="target">
            <option value="all">All</option>
            <option value="notwinner">notwinner</option>
            <option value="notredeem">not yet redeem</option>
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
  <input type="hidden" name="id" value="0" />
  <input type="hidden" name="method" value="add" />
  <input type="hidden" name="nctable" value="admin" />
</form>
</div>
<div class="container codetable">
    <h1><?=$objname?> Table</h1>
    <table id="table"></table>
</div>