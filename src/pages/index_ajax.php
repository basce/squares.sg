<?php
$debugTime = microtime(true);

$obj = array(
    "error"=>1,
    "msg"=>"unknown"
);

if(isset($_GET["signed_request"])){
    $_POST["signed_request"] = $_GET["signed_request"];
}

$user = $main->getFBID();
$userObj = $main->getUserObj(true);
$uid = $main->getUID();
$obj["user"] = $user;

try{
    if(isset($_REQUEST["method"])){
        switch($_REQUEST["method"]){
            case "fblogin":
            case "login":
                $user = $main->getFBID();
                if($user){
                    $tempuserObj = $main->getUserObj(true);
                    $result = $main->fbAuth($user);
                    $obj["tempresult"] = $result;
                    if(!(isset($result) && $result["status"] == 1)){
                        $obj["error"] = 1;
                        $obj["msg"] = $result["msg"];
                    }else{
                        $uid = $main->updateUserByFBdata($result["data"]["userInfo"],$user,$uid);
                        if($tempuserObj == NULL ){
                            if(SKIPREG){
                                //only when user email is not empty
                                $auserObj = $main->getUserObj(true);
                                if($auserObj["email"] != ''){
                                    $main->updateUser(array(
                                        "status"=>STATUS == "staging"? "installed":"registered",
                                        "uid"=>$uid
                                    ));
                                }
                            }
                        }
                    }

                    if($uid){
                        
                        $obj["error"] = 0;
                        $obj["user"] = $obj["userObj"] = $main->getUserObj(true);
                        $obj["voted"] = $main->getVoted($uid);
                        $obj["registered"] = 1;
                        
                        $obj["msg"] = "";
                    }else{
                        $obj["error"] = 1;
                        $obj["registered"] = 0;
                        $obj["msg"] = "Invalid User : access_token denied, please refresh the page and try later";
                    }
                }else{
                    $obj["error"] = 1;
                    $obj["registered"] = 0;
                    $obj["msg"] = "Invalid User : access_token denied, please refresh the page and try later";
                }
            break;
            case "getSubmission":
                $obj["error"] = 0;
                $obj["msg"] = 0;
                $pageindex = isset($_REQUEST["pageindex"]) ? $_REQUEST["pageindex"] : 1;
                $obj["submissions"] = $main->getSubmission(12, $pageindex, $_REQUEST["order"]);
                $obj["voted"] = isset($uid) ? $main->getVoted($uid) : array();
            break;
            case "getVoted":
                if(!$uid){
                    if($user){
                        $tempuserObj = $main->getUserObj(true);
                        $result = $main->fbAuth($user);
                        $obj["tempresult"] = $result;
                        if(!(isset($result) && $result["status"] == 1)){
                            $obj["error"] = 1;
                            $obj["msg"] = $result["msg"];
                        }else{
                            $uid = $main->updateUserByFBdata($result["data"]["userInfo"],$user,$uid);
                            if($tempuserObj == NULL ){
                                if(SKIPREG){
                                    //only when user email is not empty
                                    $auserObj = $main->getUserObj(true);
                                    if($auserObj["email"] != ''){
                                        $main->updateUser(array(
                                            "status"=>STATUS == "staging"? "installed":"registered",
                                            "uid"=>$uid
                                        ));
                                    }
                                }
                            }
                        }

                        if($uid){
                            
                            $obj["error"] = 0;
                            $obj["user"] = $obj["userObj"] = $main->getUserObj(true);
                            $obj["voted"] = $main->getVoted($uid);
                            $obj["registered"] = 1;
                            
                            $obj["msg"] = "";
                        }else{
                            $obj["error"] = 1;
                            $obj["registered"] = 0;
                            $obj["user"] = null;
                            $obj["msg"] = "Invalid User : access_token denied, please refresh the page and try later";
                        }
                    }else{
                        $obj["error"] = 0;
                        $obj["user"] = null;
                        $obj["voted"] = array();
                    }
                }else{
                    $obj["error"] = 0;
                    $obj["userObj"] = $main->getUserObj(true);
                    $obj["voted"] = $main->getVoted($uid);
                }
            break;
            case "reg":
                if(!$uid){
                    $obj["error"] = 1;
                    $obj["msg"] = "Required FBlogin";
                }else{
                    $main->updateUser(array(
                        "name"=>$_REQUEST["name"],
                        "email"=>$_REQUEST["email"],
                        "pdpa"=>$_REQUEST["pdp"] == 1 ? 1 : 0,
                        "status"=>"registered",
                        "uid"=>$uid
                    ));

                    $obj["error"] = 0;
                    $obj["msg"] = "Update Success";
                    $obj["user"] = $obj["userObj"] = $main->getUserObj(true);
                }
            break;
            case "makevote":
                //get submission info
                if(!$uid){
                    if(!$user){
                        $obj["error"] = 0;
                        $obj["status"] = "nologin";
                    }else{
                        //fb login but not register
                        $tempuserObj = $main->getUserObj(true);
                        $result = $main->fbAuth($user);
                        $obj["tempresult"] = $result;
                        if(!(isset($result) && $result["status"] == 1)){
                            $obj["error"] = 1;
                            $obj["msg"] = $result["msg"];
                        }else{
                            $uid = $main->updateUserByFBdata($result["data"]["userInfo"],$user,$uid);
                            if($tempuserObj == NULL ){
                                if(SKIPREG){
                                    //only when user email is not empty
                                    $auserObj = $main->getUserObj(true);
                                    if($auserObj["email"] != ''){
                                        $main->updateUser(array(
                                            "status"=>STATUS == "staging"? "installed":"registered",
                                            "uid"=>$uid
                                        ));
                                    }
                                }
                            }
                            if($main->checkIsVotedToday($uid, $_REQUEST["id"])){
                                $obj["error"] = 0;
                                $obj["status"] = "voted";
                                $obj["submission"] = $main->getSubmissionDetail($_REQUEST["id"]);
                            }else{
                                //check if within tiem
                                if( time() >= strtotime(VOTE_START) && time() < strtotime(VOTE_CLOSE) ){
                                    $main->vote($uid, $_REQUEST["id"]);
                                    $obj["error"] = 0;
                                    $obj["status"] = "success";
                                    $obj["submission"] = $main->getSubmissionDetail($_REQUEST["id"]);
                                }else{
                                    $obj["error"] = 0;
                                    $obj["status"] = "closed";
                                    $obj["submission"] = $main->getSubmissionDetail($_REQUEST["id"]);
                                }
                            }        
                        }
                    }
                }else{
                    if($userObj["status"] != "registered"){
                        $obj["error"] = 0;
                        $obj["status"] = "noReg";
                    }else if($main->checkIsVotedToday($uid, $_REQUEST["id"])){
                        $obj["error"] = 0;
                        $obj["status"] = "voted";
                        $obj["submission"] = $main->getSubmissionDetail($_REQUEST["id"]);
                    }else{
                        $main->vote($uid, $_REQUEST["id"]);
                        $obj["error"] = 0;
                        $obj["status"] = "success";
                        $obj["submission"] = $main->getSubmissionDetail($_REQUEST["id"]);
                    }
                }
            break;
        }
    }   
}catch(Exception $e){
    $obj['error'] = 1;
    $obj['msg'] =$e->getMessage();
}
$obj["response_time"] = getDebugTime();
echo json_encode($obj);
