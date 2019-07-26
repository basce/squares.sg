<?php
$conn = ncapputil::getConnection();

$adminManager = new admin(array(
	"conn"=>$conn,
	"table"=>DB_ADMIN
));
$user = $adminManager->validToken();
if(!$user){
	exit("require admin login");
}

function getEmailTemplateByCode($code, $type){
	switch($type){
		case "notify":
			//check if custom email template exist.
			$t_file = dirname(dirname(dirname(__dir__)))."/uploads/templates/".$code."/email/new_submission.html";

			if(file_exists($t_file)){

				$fd = @fopen ($t_file, "r") or die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				$content = @fread ($fd, filesize ($t_file)) or 
				die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				@fclose ($fd);
				
				preg_match('/<title>([^"]+)<\/title>/',$content,$m); 
				
				$title = $m[1];

				return array(
					"title"=>$title,
					"file"=>$t_file,
					"template_found"=>true
				);

			}else{
				
				return array(
					"title"=>"You have a new lead! - ageLOC® LumiSpa® Refer & Win Program.",
					"file"=>dirname(dirname(dirname(__dir__)))."/inc/email/new_submission.html",
					"template_found"=>false
				);
			}
		break;
		case "distributor":
			//check if custom email template exist.
			$t_file = dirname(dirname(dirname(__dir__)))."/uploads/templates/".$code."/email/distributor_new.html";
			if(file_exists($t_file)){

				$fd = @fopen ($t_file, "r") or die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				$content = @fread ($fd, filesize ($t_file)) or 
				die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				@fclose ($fd);

				preg_match('/<title>([^"]+)<\/title>/',$content,$m); 

				$title = $m[1];

				return array(
					"title"=>$title,
					"file"=>$t_file,
					"template_found"=>true
				);

			}else{
				//
				return array(
					"title"=>"Your unique referral URL - ageLOC® LumiSpa® Refer & Win Program.",
					"file"=>dirname(dirname(dirname(__dir__)))."/inc/email/distributor_new.html",
					"template_found"=>false
				);
			}
		break;
		case "distributor_change":
			//check if custom email template exist.
			$t_file = dirname(dirname(dirname(__dir__)))."/uploads/templates/".$code."/email/distributor_change.html";
			if(file_exists($t_file)){

				$fd = @fopen ($t_file, "r") or die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				$content = @fread ($fd, filesize ($t_file)) or 
				die(__FILE__." , ". __LINE__. " Can't open file $t_file");
				@fclose ($fd);

				preg_match('/<title>([^"]+)<\/title>/',$content,$m); 

				$title = $m[1];

				return array(
					"title"=>$title,
					"file"=>$t_file,
					"template_found"=>true
				);

			}else{
				//
				return array(
					"title"=>"Particulars updated.",
					"file"=>dirname(dirname(dirname(__dir__)))."/inc/email/distributor_change.html",
					"template_found"=>false
				);
			}
		break;
	}
}

function _sendEmailBare($receiver, $title, $content, $emailtemplate, $bcc='', $withIncludeEmail=false, $attachment=false){
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->Encoding = 'quoted-printable';
	$mail->From = SENDER_EMAIL;
	$mail->FromName = SENDER_NAME;
	$trackingEmail = array();
	if($withIncludeEmail && INCLUDE_EMAIL){
		$default_emails = explode(",",INCLUDE_EMAIL);
		foreach($default_emails as $value){
			$trackingEmail[] = $value;
			$mail->AddBCC($value);	
		}
	}
	if($bcc!=''){
		$additional_emails = explode(",",$bcc);
		foreach($additional_emails as $value){
			$trackingEmail[] = $value;
			$mail->AddBCC($value);				
		}
	}
	$mail->AddAddress($receiver['email'], $receiver['name']);
	$mail->Subject = '=?utf-8?B?'.base64_encode($title).'?=';
	$mail->isHTML(true);
	$mail->MsgHTML(parse_email_template($emailtemplate, $content, true));
	if($attachment){
		if(is_array($attachment)){
			foreach($attachment as $key=>$value){
				$mail->AddAttachment($value);	
			}
		}else{
			$mail->AddAttachment($attachment);
		}
	}
	if(USE_LOCAL_DKIM){
		$mail->DKIM_domain = DKIM_DOMAIN;
		$mail->DKIM_private =  dirname(dirname(dirname(__dir__)))."/inc/".DKIM_PRIVATE_FILENAME; //path to file on the disk.
		$mail->DKIM_selector = DKIM_SELECTOR;// change this to whatever you set during step 2
		$mail->DKIM_passphrase = "";
		$mail->DKIM_identifier = $mail->From;
	}
			
	if($mail->Send()) {
		logger::trace("TEST EMAIL SENT : ".$receiver["email"]);
		return array("error"=>0, "msg"=>"email sent to ".$receiver["email"]." ");
	}else{
		logger::trace("TEST EMAIL ERROR : ".$mail->ErrorInfo);
		return array("error"=>1, "msg"=>$mail->ErrorInfo);
	}
}

function _sendEmail($receiver, $title, $content, $emailtemplate, $bcc='', $withIncludeEmail=false, $attachment=false){
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->Encoding = 'quoted-printable';
	if(defined('AWS_SMTP_USERNAME') && AWS_SMTP_USERNAME){
		$mail->IsSMTP(); 
		$mail->SMTPAuth = true;
		$mail->Host = AWS_SMTP_ENDPOINT;
		$mail->Port = AWS_PORT;
		$mail->SMTPSecure = 'tls';
		$mail->Username = AWS_SMTP_USERNAME;
		$mail->Password = AWS_SMTP_PASSWORD;
	}
	$mail->From = SENDER_EMAIL;
	$mail->FromName = SENDER_NAME;
	$trackingEmail = array();
	if($withIncludeEmail && INCLUDE_EMAIL){
		$default_emails = explode(",",INCLUDE_EMAIL);
		foreach($default_emails as $value){
			$trackingEmail[] = $value;
			$mail->AddBCC($value);	
		}
	}
	if($bcc!=''){
		$additional_emails = explode(",",$bcc);
		foreach($additional_emails as $value){
			$trackingEmail[] = $value;
			$mail->AddBCC($value);				
		}
	}
	$mail->AddAddress($receiver['email'], $receiver['name']);
	$mail->Subject = '=?utf-8?B?'.base64_encode($title).'?=';
	$mail->isHTML(true);
	$mail->MsgHTML(parse_email_template($emailtemplate, $content, true));
	if($attachment){
		if(is_array($attachment)){
			foreach($attachment as $key=>$value){
				$mail->AddAttachment($value);	
			}
		}else{
			$mail->AddAttachment($attachment);
		}
	}
	if(USE_LOCAL_DKIM){
		$mail->DKIM_domain = DKIM_DOMAIN;
		$mail->DKIM_private =  dirname(dirname(dirname(__dir__)))."/inc/".DKIM_PRIVATE_FILENAME; //path to file on the disk.
		$mail->DKIM_selector = DKIM_SELECTOR;// change this to whatever you set during step 2
		$mail->DKIM_passphrase = "";
		$mail->DKIM_identifier = $mail->From;
	}
			
	if($mail->Send()) {
		logger::trace("TEST SMTP email sent to ".$receiver["email"]." ");
		return array("error"=>0, "msg"=>"TEST SMTP email sent to ".$receiver["email"]." ");
	}else{
		logger::trace("TEST SMTP EMAIL ERROR : ".$mail->ErrorInfo." usernmae : ".AWS_SMTP_USERNAME." password :".AWS_SMTP_PASSWORD." endpoint : ".AWS_SMTP_ENDPOINT." port : ".AWS_PORT);
		return _sendEmailBare($receiver, $title, $content, $emailtemplate, $bcc, $withIncludeEmail, $attachment);
	}
}

function parse_email_template($t_file, $replace, $return_output = false){
	$fd = @fopen ($t_file, "r") or die(__FILE__." , ". __LINE__. " Can't open file $t_file");
	$content = @fread ($fd, filesize ($t_file)) or 
	die(__FILE__." , ". __LINE__. " Can't open file $t_file");
	@fclose ($fd);

	$content = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($replace){
		return isset($replace[$matches[1]])?$replace[$matches[1]]:'';
	},$content);

	$content = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($replace){
		return isset($replace[$matches[1]])?$replace[$matches[1]]:'';
	},$content);

	if ($return_output) {
		return $content;
	}
	else {
		echo $content;
		exit();
	}
}

function sendEmail($q){
	global $user, $adminManager;
	if($user["level"] == 4){
		//check code exist
		$receiver = array(
				"email"=>$q["email"],
				"name"=>$q["email"]
			);
		switch($q["template"]){
			case 0:
				//new submission
				$result = getEmailTemplateByCode($q["code"], "notify");
				$additionalmsg = $result["template_found"]?"found":"not found, use default";
				$content = array(
					"distributor_name"=>"[DISTRIBUTOR NAME]",
					"distributor_id"=>"[DISTRIBUTOR ID]",
					"customer_name"=>"[CUSTOMER_NAME]",
					"customer_phone"=>"[CUSTOMER_PHONE]",
					"customer_country"=>"[CUSTOMER_COUNTRY]",
					"customer_email"=>"[CUSTOMER_EMAIL]",
					"country_name"=>"[COUNTRY]",
					"share_link"=>"[SHARE_URL]"
				);

				$title = $result["title"];

				$title = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($content){
					return isset($content[$matches[1]])?$content[$matches[1]]:'';
				},$title);


				$result = _sendEmail($receiver, $title, $content, $result["file"]);
				
				return array(
					"success"=>$result["error"] ? 0 : 1,
					"msg"=>$result["msg"]."template ".$q["code"]." ".$additionalmsg
				);
			break;
			case 1:
				//new distributor
				
				$result = getEmailTemplateByCode($q["code"], "distributor");
				$additionalmsg = $result["template_found"]?"found":"not found, use default";
				$content = array(
					"distributor_name"=>"[DISTRIBUTOR NAME]",
					"distributor_id"=>"[DISTRIBUTOR ID]",
					"customer_name"=>"[CUSTOMER_NAME]",
					"customer_phone"=>"[CUSTOMER_PHONE]",
					"customer_country"=>"[CUSTOMER_COUNTRY]",
					"customer_email"=>"[CUSTOMER_EMAIL]",
					"country_name"=>"[COUNTRY]",
					"share_link"=>"[SHARE_URL]"
				);

				$title = $result["title"];

				$title = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($content){
					return isset($content[$matches[1]])?$content[$matches[1]]:'';
				},$title);

				$result = _sendEmail($receiver, $title, $content, $result["file"]);
				
				return array(
					"success"=>$result["error"] ? 0 : 1,
					"msg"=>$result["msg"]."template ".$q["code"]." ".$additionalmsg
				);

			break;
			case 2:
				//distributor detail change
				
				$result = getEmailTemplateByCode($q["code"], "distributor_change");
				$additionalmsg = $result["template_found"]?"found":"not found, use default";
				$content = array(
					"distributor_name"=>"[DISTRIBUTOR NAME]",
					"distributor_id"=>"[DISTRIBUTOR ID]",
					"customer_name"=>"[CUSTOMER_NAME]",
					"customer_phone"=>"[CUSTOMER_PHONE]",
					"customer_country"=>"[CUSTOMER_COUNTRY]",
					"customer_email"=>"[CUSTOMER_EMAIL]",
					"country_name"=>"[COUNTRY]",
					"share_link"=>"[SHARE_URL]"
				);

				$title = $result["title"];

				$title = preg_replace_callback("/%%([A-Za-z0-9_ ]+)%%/", function($matches) use ($content){
					return isset($content[$matches[1]])?$content[$matches[1]]:'';
				},$title);

				$result = _sendEmail($receiver, $title, $content, $result["file"]);
				
				return array(
					"success"=>$result["error"] ? 0 : 1,
					"msg"=>$result["msg"]."template ".$q["code"]." ".$additionalmsg
				);
			break;
		}
	}else{
		return array(
			"success"=>0,
			"msg"=>"You don't have permission to delete"
		);
	}
}

$method = isset($_REQUEST["method"]) ? $_REQUEST["method"] : "";
switch($method){
	case "sendTestEmail":
		echo json_encode(sendEmail($_REQUEST));
	break;
}

