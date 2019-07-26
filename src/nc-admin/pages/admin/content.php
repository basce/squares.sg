<div class="container codepage" id="form1">
  <h1><span>Add New Admin</span><button id="add" class="btn btn-link hide">
            <i class="glyphicon glyphicon-plus"></i> Add New Admin
        </button></h1>
  <form class="form-horizontal">
  <div class="form-group">
    <label for="username" class="col-sm-3 control-label">Username</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="username" name="username" placeholder="username" value="" required>
    </div>
  </div>
  <div class="form-group">
    <label for="email" class="col-sm-3 control-label">Email</label>
    <div class="col-sm-9">
      <input type="email" class="form-control" id="email" name="email" placeholder="email" value="" required>
    </div>
  </div>
  <div class="form-group">
    <label for="email" class="col-sm-3 control-label">Password</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" aria-describedby="passwordHelp" id="password" name="password" placeholder="Password" value="">
      <small id="passwordHelp" class="form-text text-muted">Store Manager password can be set here, and no email needed.</small>
    </div>
  </div>
<?php
  if($user["level"] == 4 || $user["level"] == 2){
?>  
  <div class="form-group">
    <label for="confirmpassword" class="col-sm-3 control-label">Country</label>
    <div class="col-sm-9">
      <select class="form-control" id="location" name="location">
          <option value="0">N/A</option>
<?php
    $conn = ncapputil::getConnection();
    $query = "SELECT id, name FROM `".DB_LOCATION."` ORDER BY name DESC";
    $locations = $conn->GetArray($query);
    foreach($locations as $key=>$location){?>          
          <option value="<?=$location["id"]?>"><?=$location["name"]?></option>
<?php } ?>
        </select>
    </div>
  </div>
 <div class="form-group">
    <label for="level" class="col-sm-3 control-label">Access Level</label>
    <div class="col-sm-9">
      <select class="form-control" id="level" name="level">
            <option value="2">Super Admin</option>
            <option value="1">Regional Admin</option>
          </select>
    </div>
  </div>
<?php
  }else{
    $conn = ncapputil::getConnection();
    $query = "SELECT locationid FROM `".DB_ADMIN."_location` WHERE aid = ?";
    $locationid = $conn->GetOne($query, array($user["aid"]));
?> 
  <input type="hidden" id="location" name="location" value="<?=$locationid?>">
  <input type="hidden" id="level" name="level" value="1">
<?php
  }
?>
  <div class="error_container">
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-default" value="add" name="submit">Done</button>
    </div>
  </div>
  <input type="hidden" name="aid" value="" />
  <input type="hidden" name="method" value="add" />
  <input type="hidden" name="nctable" value="admin" />
</form>
</div>
<div class="container codetable">
    <h1>Admin Table</h1>
    <table id="table"></table>
</div>