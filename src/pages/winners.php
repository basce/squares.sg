<?php

$winners = $main->get_se_winners();

?><!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <meta name="description" content="We celebrate these intriguing squares of Singapore life, created by promising young artists in Singapore.">

    <title>Winners - 54 Squares of Life by Schneider Electric and NAFA Singapore</title>

    <meta property="og:url"                content="<?=SERVER_PATH?>rules" />
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="Winners - 54 Squares of Life by Schneider Electric and NAFA Singapore" />
    <meta property="og:description"        content="We celebrate these intriguing squares of Singapore life, created by promising young artists in Singapore." />
    <meta property="og:image"              content="<?=SERVER_PATH?>assets/img/1200x630.png" />

    <!-- favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.ico">

    <!-- style css -->
    <link rel="stylesheet" href="/style.css">
    <!-- modernizr js -->
    <script src="/assets/js/vendor/modernizr-2.8.3.min.js"></script>

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
								<a href="index.html"><img src="/assets/img/logo.png" alt="MiniPo" />
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
	<div class="pages-header bg-color-2 winners" >
    
    	
<div class="container ">
        <img src="/assets/img/main-visual-smaller.png" alt="" class="main-visual">
<div class="row text-left">
				<div class="col-xs-12">
					<div class="page-title ptb-110">
						<h1 class="mb-15">They came, they slogged &amp; they’ve won!</h1>
						<h4 class="mb-5">Let us present the winners. The top 3 receive $500 each.</h4>
					</div>
				</div>
            </div>
            <div class="overlay dark-1"></div> 
		</div>
	</div>
	<!-- Breadcrumbs End -->
    <!-- Portfolio Section Start -->
    <div class="portfolio-area portfolio-seven three-style4 section-padding clearfix winner">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
					<!--div class="portfolio-menu hover-1">	
                        <label > Filter by:             
                            <select name="cars">
                                <option value="POPULAR">POPULAR</option>
                                <option value="RECENT">RECENT</option>
                                <option value="DESIGNER">DESIGNER</option>
                                <option value="TITLE">TITLE</option>
                            </select>
					    </label>                 
					</div-->
                    
					<div class="portfolio-grid fitRows-grid hover-1">
						<div class="grid-item work3d print percent-33">
							<div class="single-portfolio ">
                            	<p class="winner-tag">
                                	<span class="number">1</span>
                                    <span class="triangle"></span>
                                </p>
                                <img src="<?=$winners["top1"]["items"][0]["image_url"]?>" alt="" />
                            </div>
                            
							<div class="project-title light-bg ptb-40">
                            	
								<a href="/<?=$winners["top1"]["unique_code"]?>">
                                <h4 class="no-margin"><?=$winners["top1"]["artwork_name"]?> </h4></a>
								<p><?=$winners["top1"]["first_name"]?> <?=$winners["top1"]["last_name"]?>, <?=$winners["top1"]["faculty"]?> </p>
								<hr class="line"/>
                                
							</div>
						</div>
                        
						<div class="grid-item web print new percent-33">
							<div class="single-portfolio">
                            <p class="winner-tag">
                                	<span class="number">2</span>
                                    <span class="triangle"></span>
                                </p>
                                <img src="<?=$winners["top2"]["items"][0]["image_url"]?>" alt="" />
                            </div>
                            
							<div class="project-title light-bg ptb-40">
                            	
								<a href="/<?=$winners["top2"]["unique_code"]?>">
                                <h4 class="no-margin"><?=$winners["top2"]["artwork_name"]?> </h4></a>
								<p><?=$winners["top2"]["first_name"]?> <?=$winners["top2"]["last_name"]?>, <?=$winners["top2"]["faculty"]?> </p>
								<hr class="line"/>
                                
							</div>
						</div>
                        
                        
						<div class="grid-item work3d design new percent-33">
							<div class="single-portfolio">
                            <p class="winner-tag">
                                	<span class="number">3</span>
                                    <span class="triangle"></span>
                                </p>
                                <img src="<?=$winners["top3"]["items"][0]["image_url"]?>" alt="" />
                            </div>
                            
							<div class="project-title light-bg ptb-40">
                            	
								<a href="/<?=$winners["top3"]["unique_code"]?>">
                                <h4 class="no-margin"><?=$winners["top3"]["artwork_name"]?> </h4></a>
								<p><?=$winners["top3"]["first_name"]?> <?=$winners["top3"]["last_name"]?>, <?=$winners["top3"]["faculty"]?> </p>
								<hr class="line"/>
                                
							</div>
						</div>
                    </div>
                    
                    
                    <h4 class="sub-headline">These works are remarkable too. Pretty-good rewards await.</h4>
                    
                    
                    <div class="portfolio-grid fitRows-grid hover-1">
<?php 					foreach($winners["others"] as $key=>$value){ ?>						

						<div class="grid-item web print new percent-33">
							<div class="single-portfolio">
                                <img src="<?=$value["items"][0]["image_url"]?>" alt="MiniPo" />
                                
                            </div>
                            
							<div class="project-title light-bg ptb-40">
								<a href="/<?=$value["unique_code"]?>"><h4 class="no-margin"><?=$value["artwork_name"]?> </h4></a>
								<p><?=$value["first_name"]?> <?=$value["last_name"]?>, <?=$value["faculty"]?></p>
								<hr class="line"/>
                                
							</div>
						</div>
<?php } ?>
			  </div>
			</div>
		</div>
    </div></div>
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
</body>

</html>