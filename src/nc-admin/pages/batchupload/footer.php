<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $jsonGetURL;
$ncTable = "distributor";
$idLabel = "id";
?>
<script src="<?=$ownfolder?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/papaparse.min.js"></script>

<script>

	
	function addOutputMessage(strong, message, type){
		$(".result").append(
			$("<div>").addClass("output_row "+type)
				.append($("<strong>").text(strong))
				.append(document.createTextNode(message))
			);
		$(".result").scrollTop(9999999);
	}

	var submission_data = null;

	function completeFn(results)
	{
		addOutputMessage("File "+$(".input-group input:text").val(), "selected", "nochange");
		if(results.data && results.data.length){
			submission_data = JSON.stringify(results.data);
			console.log(submission_data);
			addOutputMessage("", JSON.stringify(submission_data), "success");
		}
		ready();
	}

	function insertSubmission(){
		$.ajax({
			type: "POST",
			url: "<?=$jsonPostURL?>",
			data: {method:'insert', nctable:'<?=$ncTable?>', data:submission_data},
			dataType: "json",
			timeout: 30000,
			success: function(d){
				addOutputMessage("Result", d.msg, d.success?"success":"error");
			},
			error: function(request, status, err){
				addOutputMessage("Ajax Error", status, "error");
			}
		});
	}

	function errorFn(err, file)
	{
		console.log("ERROR:", err, file);
	}

	function buildConfig()
	{
		return {
			delimiter: '',
			header: true,
			dynamicTyping: false,
			skipEmptyLines: true,
			preview: 0,
			step: undefined,
			encoding: '',
			worker: false,
			comments: '',
			complete: completeFn,
			error: errorFn,
			download: false
		};
	}

	function processing(){
		$(".help-block").html("Processing. <i class='fa fa-circle-o-notch fa-spin'></i>");
		$(".input-group-btn").addClass("disabled");
		$(".input-group-btn input").attr({disabled:true});
	}

	function ready(){
		$(".help-block").html("Kindly download a sample on distributor page for the structure of CSV.");
		$(".input-group-btn").removeClass("disabled");
		$(".input-group-btn input").removeAttr("disabled");
	}

	function validdatefile(){
		processing();
		var files = $(".input-group-btn input").prop("files");

		if(files.length){
			$.each(files, function(index,f){
				var reader = new FileReader();
				var name = f.name;

				reader.onload = function(e){
					var data = e.target.result;
					Papa.parse(data, buildConfig());

				}

				reader.readAsText(f);
			});
		}else{
			processing();
		}
	}

	$(document).on('change', ':file', function() {
	    var input = $(this),
	        numFiles = input.get(0).files ? input.get(0).files.length : 1,
	        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	    input.trigger('fileselect', [numFiles, label]);

	    validdatefile();
	  });

	$(document).ready( function() {
	      $(':file').on('fileselect', function(event, numFiles, label) {

	          var input = $(this).parents('.input-group').find(':text'),
	              log = numFiles > 1 ? numFiles + ' files selected' : label;

	          if( input.length ) {
	              input.val(log);
	          } else {
	              if( log ) alert(log);
	          }

	      });
	  });
 
</script>