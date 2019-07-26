<body id="loginpage" data-spy="scroll" data-target=".navbar">
<div class="vertical-center">
  <div class="container loginbox">
        <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        <div class="col-xs-10 col-sm-10 col-md-10">
            <form action="" method="post" id="frm_user" class="frm_standard" enctype="multipart/form-data">
              <div class="form-group has-feedback has-feedback-left">
                <label class="control-label sr-only">Email</label>
                <input type="email"  name="email" class="form-control" placeholder="Email" tabindex="1" />
                <i class="form-control-feedback glyphicon glyphicon-user"></i>
                <?php if(isset($errors) && $errors){ ?>
                <div class="errors"><?=$errors?>
                </div>
                <?php } ?>
                <?php if(isset($success) && $success){ ?>
                <div><?=$success?></div>
                <?php } ?>
              </div>
              <div class="text-right">
              <button type="submit" class="btn btn-login" value="sendemail">Retrieve Password Reset Link</button>
              </div>
            </form>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1"></div>
        </div>
    </div>
</div>