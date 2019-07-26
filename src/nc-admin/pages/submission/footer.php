<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $ownfolder."post.php";
$ncTable = "email";
?>

<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/tableExport.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/jquery.base64.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-table-filter.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bs-table.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-table.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-table-editable.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-table-export.js"></script>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap-table-exportall.js"></script>
<script type="text/javascript">
function findBootstrapEnvironment() {
		var envs = ['xs', 'sm', 'md', 'lg'];
	
		$el = $('<div>');
		$el.appendTo($('body'));
	
		for (var i = envs.length - 1; i >= 0; i--) {
			var env = envs[i];
	
			$el.addClass('hidden-'+env);
			if ($el.is(':hidden')) {
				$el.remove();
				return env;
			}
		};
	}
$(function(){
	//generate table
	$('#table1').bootstrapTable({
		method:"get",
		url:"<?=SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"?>",
		ncmethod:"getEmailTable",
		exportfilename:"email",
		showColumns:true,
		search:true,
		showRefresh:true,
		showToggle:true,
		showExportAll:!(findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs"),
		sidePagination:"server",
		pagination:[5,10],
		cardView:findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs",
		sortName:"id",
		sortOrder:"desc",
		onClickRow:function(row,e){
			//window.open("https://www.facebook.com/app_scoped_user_id/"+row.fbid+"/");
		},
		columns: [{
			field: 'id',
			title: 'ID',
			sortable:true
		},{
			field: 'email',
			title: 'Email',
			sortable:true
		},{
			field: 'landingpage',
			title: 'Page',
			sortable:true
		},{
			field: 'ip',
			title: 'IP',
			sortable:true
		},{
			field: 'country',
			title: 'country',
			sortable:true
		},{
			field: 'tt',
			title: 'Submission Time',
			sortable:true
		}]
	}).bootstrapTable('hideColumns', {
		columns:[
			'id','ip','country'
		]
	});
});
</script>