<?php
class newreport_render{
  private $admin = null;

  function __construct(){
    
  }

  public function getQueryPath(){
    $request = str_replace(FOLDER_TO_ROOT.ADMIN_FOLDER,"",$_SERVER['REQUEST_URI']);
    return explode("/", trim($request, "/"));
  }

  function setAdmin($user){
    $this->admin = $user;
  }

  function getPageData($name){
    global $admin_menu;
    if(!$this->admin){
      return null;
    }else{
      $pageMatch = null;
      foreach($admin_menu as $key=>$page){
        if($page["name"] == $name){
          //if page name exist
          $pageMatch = $page;
       }
      }
      if($pageMatch){
          //match
          //check permission
          $user = $this->admin;
          $accesslevel = isset($user["level"]) ? $user["level"] : "0";
          $allowLevels = explode(",",$pageMatch["visible"]);
          if(in_array($accesslevel, $allowLevels)){
            //allow
            return $pageMatch;
          }else{
            return null;
          }
      }else{
          //not match
        return null;
      }
    }
  }

  function isAdminPageAvailable($pagename){
    $params = $this->getQueryPath();
    if($params > 1){
      //with parameters
    }else{

    }
    foreach($admin_menu as $key=>$page){
      if($page["label"] == $pagename){
        //if page name exist
      }
    }
  }

  function numberFunc($num){
    if($num > 1000000){
      $num = $num / 1000000;
      return number_format($num,2)."M";
    }else if($num > 1000){
      //5 digit
      $num = $num / 1000;
      return number_format($num,2)."K";
    }else{
      return round($num, 2);
    }
  }

  function createEmptyBlock(){
    ob_start();
?>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
</div>  
<?php    
   $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  function createNumberBlock($num, $title, $pref = ""){
    if(is_array($num) && sizeof($num) === 2){
      //got dominator 
      /*
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="content-box">
              <div class="content">
                <div class="num-sm"><span class="fractions">135</span><span class="num-divider">/</span><span class="numerator">500</span></div>
                <div class="text-title">Total Participants</div>
              </div>
            </div>
          </div>
       */
      ob_start();
?>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
  <div class="content-box">
    <div class="content">
      <div class="num-sm"><span class="fractions"><?=$this->numberFunc($num[0])?></span><span class="num-divider">/</span><span class="numerator"><?=$this->numberFunc($num[1])?></span></div>
      <div class="text-title"><?=$title?></div>
    </div>
  </div>
</div>
<?php      
    }else{
      /*
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
            <div class="content-box">
              <div class="content">
                <div class="num">1,288</div>
                <div class="text-title">Total Participants</div>
              </div>
            </div>
          </div>
     */
      ob_start();
      ?>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
  <div class="content-box">
    <div class="content">
      <div class="num"><?=$pref?><?=$this->numberFunc($num)?></div>
      <div class="text-title"><?=$title?></div>
    </div>
  </div>
</div>
      <?php
      $output = ob_get_contents();
      ob_end_clean();
    }
    return $output;
  }

  function createMenu($currentPage=""){
    //get admin level
    global $admin_menu;
    $user = $this->admin;
    $currentuserlevel = isset($user["level"]) ? $user["level"] : 0;

    ob_start();
    ?>
    <header class="container-fluid">
        <div class="row herobanner">
          <div class="container">
            <h2><?=htmlspecialchars(CLIENT_NAME)?></h2>
            <h1><?=htmlspecialchars(APP_NAME)?></h1>
          </div>
        </div>
        <div class="row row-no-padding">
          <div class="col-xs-4 col-sm-4 bgcolor1"></div>
          <div class="col-xs-4 col-sm-4 bgcolor2"></div>
          <div class="col-xs-4 col-sm-4 bgcolor3"></div>
        </div>
        <?php
          if($currentPage != ""){
        ?>
        <div class="menu-container">
          <div class="menu menu-3"> <span class="menu-item"></span> <span class="menu-item"></span> <span class="menu-item"></span> </div>
          <div class="menu-text"><span class="opentext burger-active">Menu</span><span class="closetext">Close</span></div>
        </div>
        <div class="menu-full vh100 menu-full-hide">
          <ul>
          <?php
            
            foreach($admin_menu as $key=>$value){
              $allowLevels = explode(",",$value["visible"]);
              if(in_array($currentuserlevel, $allowLevels)){
                if($value["name"] == $currentPage["name"]){
                  ?><li class="menu-list active"><a href="<?=$value["url"]?>"><span><?=$value["label"]?></span></a></li><?php
                }else{
                  ?><li class="menu-list"><a href="<?=$value["url"]?>"><span><?=$value["label"]?></span></a></li><?php
                }
              }
            }
          ?>
          </ul>
        </div>
        <?php 
        }
        ?>
      </header>
      <header class="container-fluid header-spacer">
        <div class="row herobanner">
          <div class="container">
            <h2><?=htmlspecialchars(CLIENT_NAME)?></h2>
            <h1><?=htmlspecialchars(APP_NAME)?></h1>
          </div>
        </div>
        <div class="row row-no-padding">
          <div class="col-xs-4 col-sm-4 bgcolor1"></div>
          <div class="col-xs-4 col-sm-4 bgcolor2"></div>
          <div class="col-xs-4 col-sm-4 bgcolor3"></div>
        </div>
      </header>
    <?php
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

}