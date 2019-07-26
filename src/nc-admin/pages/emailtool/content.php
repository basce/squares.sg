<?php
?>
<div class="container codepage" id="form1">
  <h1><span>Test Email</span></h1>
  <form class="form-horizontal">

  <div class="form-group">
    <label for="email" class="col-sm-3 control-label">Email</label>
    <div class="col-sm-9">
      <input type="email" class="form-control" id="email" name="email" placeholder="email" value="" required>
    </div>
  </div>
  <div class="form-group">
    <label for="code" class="col-sm-3 control-label">Country and Language</label>
    <div class="col-sm-9">
      <input type="text" class="form-control" id="code" name="code" placeholder="PH_EN" value="" required>
    </div>
  </div>
  <div class="form-group">
    <label for="confirmpassword" class="col-sm-3 control-label">Template</label>
    <div class="col-sm-9">
      <select class="form-control" id="template" name="template">
          <option value="0">new Submission</option>
          <option value="1">new Distributor</option>
          <option value="2">Distributor detail changed</option>
      </select>
    </div>
  </div>
  <div class="error_container">
  </div>
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
      <button type="submit" class="btn btn-default" value="add" name="submit">Send <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button>
    </div>
  </div>
  <input type="hidden" name="method" value="sentEmail" />
</form>
</div>