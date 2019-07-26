</div> <!-- for .wrapper -->
<script src="<?=$commonfolder?>js/vendor/modernizr.min.js"></script>
<!--[if lte IE 8]><script src="<?=$commonfolder?>js/vendor/html5.js"></script><![endif]-->
<script src="<?=$commonfolder?>assets/js/plugins/jquery.easing/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vunit.js"></script>
<script>
$(function(){
	$(".menu-container").click(function(){
	  $(".menu").toggleClass("open");
	  $(".opentext").toggleClass("burger-active");
	  $(".closetext").toggleClass("burger-active");
	  $(".menu-full").toggleClass("menu-full-hide");
	  $(this).toggleClass("menu-container-active");
	});
	$(window).scroll( function(){
		if($(window).scrollTop() > 73){
			$(".herobanner:eq(0)").addClass("tiny");
		}else{
			$(".herobanner:eq(0)").removeClass("tiny");
		}
	});
});
</script>
