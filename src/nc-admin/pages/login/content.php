<body id="loginpage" data-spy="scroll" data-target=".navbar">
<div class="vertical-center">
  <div class="container loginbox">
        <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        <div class="col-xs-10 col-sm-10 col-md-10">
            <form action="" method="post" id="frm_user" class="frm_standard" enctype="multipart/form-data">
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Email</label>
                <input type="text"  name="username" class="form-control" placeholder="Email Or Username" tabindex="1" />
                <i class="form-control-feedback glyphicon glyphicon-user"></i>
              </div>
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Username</label>
                <input type="password"  name="password" class="form-control" placeholder="Password" tabindex="2" />
                <i class="form-control-feedback glyphicon glyphicon-lock"></i>
                <div class="firsttime">
                  <a href="<?=SERVER_PATH.ADMIN_FOLDER."forgetpassword"?>">First time login</a>
                </div>
                <?php if($errors){ ?>
                <div class="errors"><?=$errors?><br >
                    <a href="<?=SERVER_PATH.ADMIN_FOLDER."forgetpassword"?>">Forget your password?</a>
                </div>
                <?php } ?>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="remme"> Remember me
                </label>
              </div>
              <div class="text-right">
              <button type="submit" class="btn btn-login">Login</button>
              </div>
            </form>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        </div>
    </div>
</div>