<div class="container codetable">
    <h1>Redeemers (<span class="uniquecount">0</span>)</h1>
    <form class="form-horizontal" role="form">
        <div class="form-group">
            <label class="col-sm-2 col-sm-offset-8 control-label">Location</label>
            <div class="col-sm-2">
<select class="form-control" id="redeem_location">
<?php
    $conn = ncAppUtil::getConnection();
    $query = "SELECT COUNT(*) FROM `".DB_WINNER."` WHERE redeem = 1";
    $totalcount = $conn->GetOne($query);

    $query = "SELECT COUNT(*) FROM ( SELECT uid FROM `".DB_WINNER."` WHERE redeem = 1 GROUP BY uid) a";
    $totalunqiuecount = $conn->GetOne($query);
    $totalunqiuecount = $totalunqiuecount?$totalunqiuecount:0;
?>    
    <option value="0">All (<?=$totalcount?>, <?=$totalunqiuecount?>)</option>
<?php
    
    $query = "SELECT id, name FROM `".DB_LOCATION."` ORDER BY name";
    $result = $conn->GetArray($query);
    foreach($result as $key=>$value){
        $query = "SELECT COUNT(*) FROM `".DB_WINNER."` WHERE redeem = 1 AND locationid = ?";
        $numcount = $conn->GetOne($query, array($value["id"]));

        $query = "SELECT COUNT(*) FROM ( SELECT uid FROM `".DB_WINNER."` WHERE redeem = 1 AND locationid = ? GROUP BY uid) a";
        $uniquecount = $conn->GetOne($query, array($value["id"]));
        $uniquecount = $uniquecount? $uniquecount : 0;
?>  <option value="<?=$value["id"]?>"><?=$value["name"]?> (<?=$numcount?>, <?=$uniquecount?>)</option>
<?php       
    }
?>
    </select>
            </div>
        </div>
    </form>
    <div class="row row3 chartrow">
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Gender</div>
                            <div class="floatrect">
                                <div id="genderdonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="genderlegend">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Country</div>
                            <div class="floatrect">
                                <div id="countrydonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="countrylegend">
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 demochart">
                    <div class="row row-no-padding">
                        <div class="col-xs-5 col-sm-5">
                            <div class="category">Age</div>
                            <div class="floatrect">
                                <div id="agedonut" class="content">
                                </div>
                            </div>
                            <div class="dominant"></div>
                        </div>
                        <div class="col-xs-7 col-sm-7">
                            <div class="legends" id="agelegend">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <table id="table1"></table>
</div>