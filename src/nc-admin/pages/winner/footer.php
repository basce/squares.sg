<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $ownfolder."post.php";
$ncTable = "winner";
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

function addWinner(uid, gid){
	$.ajax({
		type:"POST",
		url:"<?=$jsonPostURL?>",
		data:{method:"add",nctable:"<?=$ncTable?>", uid:uid, gid:gid},
		dataType:'json',
		timeout:5000,
		success:function(d){
			console.log(d);
		},
		error:function(request,status,err){
			console.log(status);
		}
	});
}

function getChart(onComplete){
	$.ajax({
		type:"POST",
		url:"<?=$jsonPostURL?>",
		data:{method:"getChart",nctable:"<?=$ncTable?>"},
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

function getUniqueNumber(onComplete){
	$.ajax({
		type:"POST",
		url:"<?=$jsonPostURL?>",
		data:{method:"getUniqueNumber",nctable:"<?=$ncTable?>"},
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

function updateUniqueCount(num){
	$(".uniquecount").text(num);
}


$(function(){
	function updateDonut(){
		getChart(function(data){

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

	//generate table
	$('#table1').bootstrapTable({
		method:"get",
		url:"<?=SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"?>",
		ncmethod:"getWinnerTable",
		exportfilename:"winnertable",
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
			field: 'wid',
			title: 'Winner ID',
			sortable:true
		},{
			field: 'id',
			title: 'ID',
			sortable:true
		}, {
			field: 'avatar',
			title: 'Avatar',
			sortable:false,
			align:'center',
			formatter:function(value, row, index){
				return "<img src='https://graph.facebook.com/"+value.slice(1)+"/picture?width=25&height=25' width='25' height='25'>";
			}
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
			field: 'fbid',
			title: 'Facebook ID',
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
			field: 'age_range',
			title: 'Age Range',
			sortable:true
		}, {
			field: 'email',
			title: 'Email',
			sortable:true
		}, {
			field: 'country',
			title: 'Country',
			sortable:true
		}, {
			field: 'locale',
			title: 'Langauge',
			sortable:true
		}, {
			field: 'ip',
			title: 'IP',
			sortable:true
		}, {
			field: 'referral',
			title: 'Total Activated',
			sortable:true
		}, {
			field: 'share',
			title: 'Share',
			sortable:true
		}, {
			field: 'apprequest',
			title: 'Invite',
			sortable:true
		}, {
			field: 'fbverified',
			title: 'Verified Facebook User',
			sortable:true
		}, {
			field: 'fblikeshown',
			title: 'Like Button Action',
			sortable:true
		}, {
			field: 'gid',
			title: 'Prize ID',
			sortable:true
		}, {
			field: 'code',
			title: 'Winning Code',
			sortable:true
		},  {
			field: 'redeem',
			title: 'Redeemed',
			sortable:true
		}, {
			field: 'prizename',
			title: 'Prize Won',
			sortable:true
		}, {
			field: 'won_tt',
			title: 'Date Won',
			sortable:true
		}, {
			field: 'tt',
			title: 'Installed Time',
			sortable:true
		}]
	}).bootstrapTable('hideColumns', {
		columns:[
			'id','first_name','last_name', 'fbid', 'sex', 'phone','ic','age','age_range', 'country', 'locale','ip','share', 'referral', 'apprequest', 'fbverified','fblikeshown','gid','code', 'tt'
		]
	});

	updateDonut();

	getUniqueNumber(function(data){
		updateUniqueCount(data.num);
	});
});
</script>