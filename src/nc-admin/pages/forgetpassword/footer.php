<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $ownfolder."post.php";
?>

<script>
	var $form = $("#form1");
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
		$form.find("form").submit(function(e){
			e.preventDefault();
			var serializedata = $(this).serializeArray();
			var data = {};
			$.each(serializedata, function(index,value){
				data[value.name] = value.value;
			});

			$.ajax({
				type:"POST",
				url:'<?=$jsonPostURL?>',
				data:data,
				dataType:'json',
				timeout:5000,
				success:function(d){
					displayMessage(d);
				},
				error:function(request, status, err){
					console.log(status);
				}
			});

		});
	});
</script>
