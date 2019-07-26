<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta name="description" content="54 Squares of Life is a design competition that showcases the works of emerging young artist and illustrators in Singapore.">

    <title>54 Squares of Life by Schneider Electric and NAFA Singapore</title>

    <meta property="og:url"                content="<?=SERVER_PATH?>" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="54 Squares of Life by Schneider Electric and NAFA Singapore" />
    <meta property="og:description"        content="54 Squares of Life is a design competition that showcases the works of emerging young artist and illustrators in Singapore." />
    <meta property="og:image"              content="<?=SERVER_PATH?>assets/img/1200x630.png" />

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.ico">

    <!-- style css -->
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/func-style.css">
    <!-- modernizr js -->
    <script src="/assets/js/vendor/modernizr-2.8.3.min.js"></script>
    <style type="text/css">
        .portfolio-grid .grid-item{
            display:inline-block;
            width:33%;
        }
        @media (max-width: 767px){
            .portfolio-grid .grid-item{
                width:100%;
            }   
        }
    </style>
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
								<a href="/"><img src="/assets/img/logo.png" alt="MiniPo" />
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
	<div class="pages-header bg-color-2 home">
		<div class="container">
			<div class="row text-center">
				<div class="col-xs-12">
					<div class="page-title ptb-110">
                    	<img src="/assets/img/main-visual.png" alt="">
						<h1 class="mb-5"><span class="oi position" data-glyph="heart"></span> your art &#8211; win a hundred!</h1>
                        <!--h4 class="mb-15">Lorem ipsum dolor sit amet, consectetuer ipsum dolor adipiscing elit. Aenean commodo ligula eget dolor. Ipsum dolor <a href="about.html">About</a>.</h4-->
                    </div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcrumbs End -->
    
    
    
    
    
    <!-- Portfolio Section Start -->
    <div class="portfolio-area portfolio-seven three-style4 section-padding clearfix">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
					<div class="portfolio-menu hover-1">
                        <label > Filter by:             
                            <select name="cars" id="sort_order">
                                <option value="POPULAR">POPULAR</option>
                                <option value="RECENT">RECENT</option>
                                <option value="DESIGNER">DESIGNER</option>
                                <option value="TITLE">TITLE</option>
                            </select>
					    </label>                 
					</div>
                    
					<div class="portfolio-grid hover-1">
						
					</div>
					<div class="view-all text-center">
						<a class="btn mt-40" id="load-more-btn" href="javascript:void(0)">Load More <i class="zmdi zmdi-refresh-sync"></i></a>
					</div>
			  </div>
			</div>
		</div>
    </div>
    <!-- Portfolio Section End -->
    <!-- Footer Section Start -->
    <footer class="footer white-bg ptb-20 clearfix">
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
            <a href="javascript:void(0)" class="fb-btn" onclick="fblogin();" ><span class="fab fa-facebook-square"></span>Log in with Facebook</a>
        </div>
    </div>
    <div style="display:none" id="lb-voted">
        <div class="message-content">
            <h2>+1!</h2>
            <p>Thank you! Your vote was successful! <br>+1 <span class="oi position" data-glyph="heart"></span> has been added to <span class="designer_first_name">[first_name]</span>’s submission <span class="number-votes lb-number-votes">0 votes</span>.</p>
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
            <p><a href="javascript:void(0)" class="close-btn" onclick="reg();">Confirm</a></p>
            
            <div class="vbox-close" >×</div>
            
        </div>
    </div>
    <!-- end of lightbox -->
    
    <!-- All JS Here -->
    <!-- jQuery Latest Version -->
    <script src="/assets/js/vendor/jquery-1.12.4.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="/assets/js/bootstrap.min.js"></script>
    <!-- Isotope -->
    <script src="/assets/js/isotope.pkgd.js"></script>
    <!-- Validate -->
    <script src="/assets/js/jquery.validate.min.js"></script>
    <!-- Slick Slider JS -->
    <script src="/assets/js/slick.min.js"></script>
    <!-- Plugins JS -->
    <script src="/assets/js/plugins.js"></script>
    <!-- main JS -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/custom.js"></script>
    <script type="text/javascript">
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