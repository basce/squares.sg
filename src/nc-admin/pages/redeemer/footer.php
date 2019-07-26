<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $ownfolder."post.php";
$ncTable = "redeemer";
?>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
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
<?php if($user["level"]==2){ ?>
    <script type="text/javascript" src="<?=$ownfolder?>js/chartfunc_admin.js"></script>
<?php }else{ ?>
	<script type="text/javascript" src="<?=$ownfolder?>js/chartfunc.js"></script>
<?php } ?>
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

function getChart(locationid, onComplete){
	$.ajax({
		type:"POST",
		url:"<?=$jsonPostURL?>",
		data:{method:"getChart",nctable:"<?=$ncTable?>", locationid:locationid},
		dataType:'json',
		timeout:5000,
		success:function(d){
			onComplete(d);
		},
		error:function(request,status,err){
			console.log(status);
		}
	});
}

function getUniqueNumber(locationid, onComplete){
	$.ajax({
		type:"POST",
		url:"<?=$jsonPostURL?>",
		data:{method:"getUniqueNumber",nctable:"<?=$ncTable?>", locationid:locationid},
		dataType:'json',
		timeout:5000,
		success:function(d){
			onComplete(d);
		},
		error:function(request,status,err){
			console.log(status);
		}
	});	
}

function updateUniqueCount(data){
	$(".uniquecount").text(data.num);

	var a = data.ar;
	$.each(a, function(index, value){
		$('#redeem_location').find("option[value='"+value.id+"']").text(value.name+" ("+value.count+", "+value.uniquecount+")");
	});
}

$(function(){
	function updateDonut(){
		var value = $('#redeem_location').find("option:selected").val();
		getUniqueNumber(value, function(data){
			updateUniqueCount(data)
		});
		getChart(value,function(data){

			var genderPerc = data.gender;
			var agePerc = data.age;
			var countryPerc =  data.country;

			var colorsblue = ['#00ACEE','#6DCFF6','#0B486B','#0B4EAD'];
			var colorsgreen = ['#D1E54E','#A9BD26','#ACD373','#669966'];
			var colorspurple = ['#EB0D8A','#F57E67','#BB077C','#300030'];

			if(genderPerc.length + agePerc.length + countryPerc.length  > 0){
				$(".chartrow").removeClass("hide");
			}else{
				$(".chartrow").addClass("hide");
			}

			if(genderPerc && genderPerc.length){
				var tempgender = f2(genderPerc);
				var tempgenderimg;
				if(tempgender.major == "Female"){
					tempgenderimg = {
						img:"<?=$ownfolder?>images/icon4woman.png",
						w:39,
						h:85
					};
				}else{
					tempgenderimg = {
						img:"<?=$ownfolder?>images/icon4man.png",
						w:39,
						h:86
					};
				}

				createDonutType1($("#genderdonut"), $("#genderlegend"), genderPerc ,colorsblue,tempgenderimg.img,tempgenderimg.w,tempgenderimg.h);
			}else{
				$("#genderdonut").empty();
				$("#genderlegend").empty();			
				$("#genderdonut").parent().siblings(".dominant").text("N/A");
			}
			if(countryPerc && countryPerc.length){
				createDonutType1($("#countrydonut"), $("#countrylegend"), countryPerc,colorsgreen,'<?=$ownfolder?>images/icon6.png',52,51);
			}else{
				$("#countrydonut").empty();
				$("#countrylegend").empty();			
				$("#countrydonut").parent().siblings(".dominant").text("N/A");
			}

			if(agePerc && agePerc.length){
				createDonutType1($("#agedonut"), $("#agelegend"), agePerc,colorspurple,'<?=$ownfolder?>images/icon5.png',52,53);
			}else{
				$("#agedonut").empty();
				$("#agelegend").empty();			
				$("#agedonut").parent().siblings(".dominant").text("N/A");
			}
		});
	}

	//location change
	$("#redeem_location").change(function(){
		var value = $(this).find("option:selected").val();
		$('#table1').bootstrapTable("refresh");	
		updateDonut();
	});

	function getMethod(){
		return $("#redeem_location option:selected").val();
	}

	updateDonut();

	//generate table
	$('#table1').bootstrapTable({
		method:"get",
		url:"<?=SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"?>",
		ncmethod:function(){return getMethod();},
		exportfilename:"redeemertable",
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
			/* //window.open("https://www.facebook.com/app_scoped_user_id/"+row.fbid+"/"); */
		},
		columns: [{
			field: 'wid',
			title: 'Winner ID',
			sortable:true
		},{
			field: 'id',
			title: 'ID',
			sortable:true
		}, {
			field: 'name',
			title: 'Name',
			sortable:true
		}, {
			field: 'first_name',
			title: 'First Name',
			sortable:true
		}, {
			field: 'last_name',
			title: 'Last Name',
			sortable:true
		}, {
			field: 'phone',
			title: 'Phone',
			sortable:true
		}, {
			field: 'ic',
			title: 'Identity Number',
			sortable:true
		}, {
			field: 'sex',
			title: 'Gender',
			sortable:true
		}, {
			field: 'age',
			title: 'Age'
		}, {
			field: 'email',
			title: 'Email',
			sortable:true
		}, {
			field: 'gid',
			title: 'Prize ID',
			sortable:true
		}, {
			field: 'code',
			title: 'Winnig Code',
			sortable:true
		},  {
			field: 'prizename',
			title: 'Prize Won',
			sortable:true
		}, {
			field: 'location_name',
			title: 'Redeemed Location',
			sortable:true
		}, {
			field: 'redeemed_time',
			title: 'Redeemed On',
			sortable:true
		}, {
			field: 'won_tt',
			title: 'Issued',
			sortable:true
		}]
	}).bootstrapTable('hideColumns', {
		columns:[
			'id','first_name','last_name', 'fbid', 'sex', 'phone','ic','age','code', 'country', 'locale','ip','share', 'referral', 'gid','code'
		]
	});
});
</script>