<?php
	$submission = $main->getSubmissionDetail($submission_id);
    $main->submission_view($submission_id);
	$current_page = SERVER_PATH.$pathquery[0];
?><!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta name="description" content="Love <?=$submission["first_name"]?>'s work? Give it the heart it deserves.">

    <title><?=$submission["first_name"]?>'s artwork - 54 Squares of Life by Schneider Electric and NAFA Singapore</title>

    <meta property="og:url"                content="<?=$current_page?>" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="<?=$submission["first_name"]?>'s artwork - 54 Squares of Life by Schneider Electric and NAFA Singapore" />
    <meta property="og:description"        content="Love <?=$submission["first_name"]?>'s work? Give it the heart it deserves." />
    <meta property="og:image"              content="<?=SERVER_PATH.$submission["items"][0]["image_url"]?>" />

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- style css -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="/func-style.css">
    <!-- modernizr js -->
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>

</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    <!-- Loading Bar Start -->
    <div id="loading-wrap">
        <div class="loading-effect"></div>
    </div>
    <!-- Loading Bar End -->
    <!-- Loading Bar End -->
    <!-- Header Section Start -->
    <header class="header-style-1">
		<div class="header-top active-sticky">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-2 col-md-3">
						<div class="left">
							<div class="logo">
								<a href="/"><img src="assets/img/logo.png" alt="54 SQUARES OF LIFE" />
								</a>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-10 col-md-9">
						<div class="right">
							<nav class="mainmenu menu-hover-1 pull-right">
								<div class="navbar-header">
									<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
								</div>
								<div class="navbar-collapse collapse clearfix">
                                    <ul class="navigation clearfix">
                                        <li><a href="/">Home</a></li>
<?php if($main->GotWinners()){?>                                        
                                        <li><a href="/winners">Winners</a></li>
<?php } ?>
                                        <li><a href="/rules">Rules</a></li>
                                        <li><a href="/about">About</a></li>
                                    </ul>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
    </header>
	<!-- Header Space -->
	<div class="header-space"></div>
    <!-- Header Section End -->
	<!-- Breadcrumbs Start -->
	<div class="pages-header bg-color-2 submission-detail">
		<div class="container ">
        
			<div class="row text-left">
				<div class="col-xs-12">
					<div class="page-title ptb-110">
						<h1 class="mb-15">ART BY <?=$submission["first_name"]?></h1>
						<h4 class="mb-5">FOR AvatarON </h4>
					</div>
				</div>
            </div>
            <div class="overlay dark-1"></div> 
		</div>
	</div>
	<!-- Breadcrumbs End -->
	<!-- Portfolio Details Section Start -->
	<div class="portfolio-details light-bg section-padding submission-detail">
		<div class="container">
			<div class="row">
				
				<div class="col-xs-12 col-sm-6 col-md-7">
                
                	<h2><?=$submission["artwork_name"]?></h2>
                    <p class="vote"><a href="#" class="vote-btn" data-id="<?=$submission["id"]?>"><span class="oi vote-icon" data-glyph="heart"></span> vote this!</a> | <span class="lb-number-votes number-votes"><?=($submission["number_of_vote"] == 1 )?"1 vote":$submission["number_of_vote"]." votes"?></span></p>
                    <?php foreach($submission["items"] as $key=>$value){?>
                    <div class="portfolio-image mb-30">
						<a class="venobox" data-gall="gall-img" href="<?=$value["image_url"]?>" alt="">
							<img src="<?=$value["image_url"]?>" />
						</a>
					</div>
                    <?php } ?>
				</div>
                
                <div class="col-xs-12 col-sm-6 col-md-5 mobile-mb-30">
					<div class="portfolio-info">
						
						<!--p>There are many variations of passages of Lorem Ipsum available, but the majority have fered alteration in some form, by injmour, or randomised words which don't look even slightlievable. Iyoare going to use a passage of Lorem Ipsum, you need to be surthere isn't anything embarrassing hidden.</p-->
						<ul class="work-info pt-40">
                        	<!--li><p style="margin-bottom:1em"><span class="profile-background"><img src="<?=$submission["profile_image"]?>" alt=""></span></p>
                        	</li-->
							<li><p class="name"><?=$submission["first_name"]?> <?=$submission["last_name"]?></p></li>
							<li><p><?=$submission["faculty"]?>, <?=$submission["course"]?>, Year <?=$submission["year"]?> </p></li>
							<li class="profile-desc">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. </li>
                            
							<li class="share"><span>Share:</span> 
								<div class="social-icon style1">
									<ul class="clearfix">
										<li><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?=$current_page?>"><i class="zmdi zmdi-facebook"></i></a></li>
										<li><a target="_blank" href="https://twitter.com/intent/tweet?url=<?=$current_page?>"><i class="zmdi zmdi-twitter"></i></a></li>
										<li><a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?=$current_page?>"><i class="zmdi zmdi-pinterest"></i></a></li>
										<li><a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=<?=$current_page?>"><i class="zmdi zmdi-linkedin"></i></a></li>
										<!-- <li><a target="_blank" href="http://www.instagram.com"><i class="zmdi zmdi-instagram"></i></a></li> -->
									</ul>
								</div>
							</li>
						</ul>
					</div>
				</div>
                
			</div>
		</div>
	</div>
	<!-- Portfolio Details Section End -->
    <!-- Footer Section Start -->
    <footer class="footer ptb-20 clearfix">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="left pull-left">
                        <p>&copy;2019 54 SQUARES OF LIFE BY SCHNEIDER ELECTRIC &amp; NAFA SINGAPORE | <a href="/terms" target="_blank">COMPETITION TERMS</a>.</p>
                    </div>
                    <!--
                    <div class="social-icon simple pull-right">
						<ul class="clearfix">
							<li><a href="http://www.facebook.com" target="_blank"><i class="zmdi zmdi-facebook"></i></a></li>
							<li><a href="http://www.twitter.com" target="_blank"><i class="zmdi zmdi-twitter"></i></a></li>
							<li><a href="http://www.pinterest.com" target="_blank"><i class="zmdi zmdi-pinterest"></i></a></li>
							<li><a href="http://www.linkedin.com" target="_blank"><i class="zmdi zmdi-linkedin"></i></a></li>
							<li><a href="http://www.instagram.com" target="_blank"><i class="zmdi zmdi-instagram"></i></a></li>
						</ul>
					</div>
                    -->
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- lightbox -->
    <a class="venobox invisible-venobutton" data-gall="gallfb" data-vbtype="inline" href="#lb-fblogin" id="lb-fblogin-trigger"></a>
    <a class="venobox invisible-venobutton" data-gall="gallvote" data-vbtype="inline" href="#lb-voted" id="lb-voted-trigger"></a>
    <a class="venobox invisible-venobutton" data-gall="gallnovote" data-vbtype="inline" href="#lb-novote" id="lb-novote-trigger"></a>
    <a class="venobox invisible-venobutton" data-gall="gallclosed" data-vbtype="inline" href="#lb-nclosed" id="lb-closed-trigger"></a>
    <a class="venobox invisible-venobutton" data-gall="gallform" data-vbtype="inline" href="#lb-form" id="lb-form-trigger"></a>
    <div style="display:none" id="lb-fblogin">
        <div class="message-content">
            <h2>Please log in on Facebook to vote!</h2>
            <a href="javascript:void(0)" class="fb-btn" onClick="fblogin();" ><span class="fab fa-facebook-square"></span>Log in with Facebook</a>
        </div>
    </div>
    <div style="display:none" id="lb-voted">
        <div class="message-content">
            <h2>+1!</h2>
            <p>Thank you! Your vote was successful! <br>+1 <span class="oi position" data-glyph="heart"></span> has been added to <span class="designer_first_name">[first_name]</span>’s submission <span class="lb-number-votes number-votes">0 votes</span>.</p>
            <p><a href="#" class="close-btn vb-close" >OK</a></p>
        </div>
    </div>
    <div style="display:none" id="lb-novote">
        <div class="message-content">
            <h2>Ops! No more votes for today! </h2>
            <p>You have run out of <span class="oi position" data-glyph="heart"></span> for <span class="designer_first_name">[first_name]</span>’s submission! Vote other submissions or come back tomorrow. You can vote for each submission, once per day!</p>
            <p><a href="#" class="close-btn vb-close" >OK</a></p>
        </div>
    </div>
    <div style="display:none" id="lb-closed">
        <div class="message-content">
            <h2>[Voting Closed]</h2>
            <p>[Paragraph]</p>
            <p><a href="#" class="close-btn vb-close" >OK</a></p>
        </div>
    </div>
    <div style="display:none" id="lb-form">
        <div class="message-content confirm-details">
            <h2>Is this you?</h2>
            <h3>Verify your details so that we can contact you when you win!</h3>
            
            <p><input name="Name" type="text" class="form_name" value="Enter your full name" required>
                    <input name="Email" type="text" class="form_email" value="Enter you email address" required></p>
            <p>
                <label><input name="agree" type="checkbox" class="form_pdp" value="I agree with the terms for this" checked>I would like to receive promotional information about Schneider Electric's products.</label>
                </p>        
            <p><a href="javascript:void(0)" class="close-btn" onClick="reg();">Confirm</a></p>
            
            <div class="vbox-close" >×</div>
            
        </div>
    </div>
    <!-- end of lightbox -->

    <!-- All JS Here -->
    <!-- jQuery Latest Version -->
    <script src="assets/js/vendor/jquery-1.12.4.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Isotope -->
    <script src="assets/js/isotope.pkgd.js"></script>
    <!-- Validate -->
    <script src="assets/js/jquery.validate.min.js"></script>
    <!-- Slick Slider JS -->
    <script src="assets/js/slick.min.js"></script>
    <!-- Plugins JS -->
    <script src="assets/js/plugins.js"></script>
    <!-- main JS -->
    <script src="assets/js/main.js"></script>
    <script src="/assets/js/custom.js"></script>
    <script type="text/javascript">
    	var submission = <?=json_encode($submission)?>;
    	//-------------------------------------------------------------
        window.fbAsyncInit = function() {
            FB.init({
              appId  : '<?=APP_ID?>',
              status : true, // check login status
              cookie : true, // enable cookies to allow the server to access the session
              xfbml  : true,  // parse XFBML
              version    : 'v2.12',
              frictionlessRequests : true
            });
            FB.Canvas.setDoneLoading();
            if (typeof fbReady == "function") fbReady();
        };
        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_GB/all.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        //-------------------------------------------------------------
    </script>
</body>

</html>