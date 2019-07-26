<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $jsonGetURL;
$ncTable = "distributor";
$idLabel = "id";
?>
<script src="<?=$ownfolder?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/papaparse.min.js"></script>

<script>
	
	function displayMessage(d){
		$('.error_container').empty();
		if(d){
			var status = d.success ? "success":"danger";
			$('.error_container').append(
				$("<div>").addClass("alert alert-"+status)
					.text(d.msg)
			);
		}
	}

	$(function(){
		var form = $("#form1 form");

		var sending = false;
		form.submit(function(e){
			e.preventDefault();
			var a = $("#email").val() ? $("#email").val() : null,
				b = $("#template").val() ? $("#template").val() : null,
				c = $("#code").val()? $("#code").val() : null;
			if(a && b){
				if(sending){
					displayMessage({
						success:false,
						msg:"An email is on sending out now. Please wait."
					});
					return;
				}
				sending = true;
				displayMessage({
					success:true,
					msg:"Sending..."
				});
				$.ajax({
					type:"POST",
					url:'<?=$jsonGetURL?>',
					data:{method:"sendTestEmail", email:a, template:b, code:c},
					dataType:'json',
					timeout:5000,
					success:function(d){
						sending = false;
						displayMessage(d);
					},
					error:function(request,status,err){
						sending = false;
						console.log(status);
					}
				});
			}else{
				displayMessage({
					success:false,
					msg:"Missing email"
				});
			}
		});
	});
 
</script>