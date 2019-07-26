<body id="resetpassword" data-spy="scroll" data-target=".navbar">
<div class="vertical-center">
  <div class="container loginbox">
        <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        <div class="col-xs-10 col-sm-10 col-md-10">
<?php if(isset($success) && $success){ ?>
        Password updated.<br>
        <a href="<?=SERVER_PATH.ADMIN_FOLDER?>">Click here to login.</a>
<?php }else{ ?>
            <form action="" method="post" id="frm_user" class="frm_standard" enctype="multipart/form-data">
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Password</label>
                <input type="password"  name="password" class="form-control" placeholder="Password" tabindex="1" />
                <i class="form-control-feedback glyphicon glyphicon-lock"></i>
              </div>
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Confirm Password</label>
                <input type="password"  name="cpassword" class="form-control" placeholder="Confirm Password" tabindex="2" />
                <i class="form-control-feedback glyphicon glyphicon-lock"></i>
                <?php if(isset($errors) && $errors){ ?>
                <div class="errors"><?=$errors?>
                </div>
                <?php } ?>
              </div>
              <div class="text-right">
              <button type="submit" class="btn btn-login">Update</button>
              </div>
            </form>
<?php } ?>            
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        </div>
    </div>
</div>