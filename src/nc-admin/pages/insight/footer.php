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
    <script type="text/javascript" src="<?=$commonfolder?>js/export-csv.js"></script>
<?php if($user["level"]==2){ ?>
    <script type="text/javascript" src="<?=$ownfolder?>js/chartfunc_admin.js"></script>
<?php }else{ ?>
	<script type="text/javascript" src="<?=$ownfolder?>js/chartfunc.js"></script>
<?php } ?>
<script>
$(function(){
new vUnit({
		CSSMap: {
			'.vh': {
				property: 'height',
				reference: 'vh'
			},
			'.vw': {
				property: 'width',
				reference: 'vw'
			},
			'.vwfs': {
				property: 'font-size',
				reference: 'vw'
			},
			'.vhmt': {
				property: 'margin-top',
				reference: 'vh'
			},
			'.vhmb': {
				property: 'margin-bottom',
				reference: 'vh'
			},
			'.vminw': {
				property: 'width',
				reference: 'vmin'
			},
			'.vmaxw': {
				property: 'width',
				reference: 'vmax'
			}
		}
	}).init();
	});


var genderPerc = <?=$genderPerData?>;
var agePerc = <?=$agePerData?>;
var countryPerc =  <?=$countryPerData?>;
var topinflu = <?=$topinfluencer?>;
var referPerc = <?=$referralC?>;
var data1 = {
	gender:genderPerc,
	country:countryPerc,
	age:agePerc
};

var date2 = <?=$dateLabelRangeJs?>;

var colorsblue = ['#00ACEE','#6DCFF6','#0B486B','#0B4EAD'];
var colorsgreen = ['#D1E54E','#A9BD26','#ACD373','#669966'];
var colorspurple = ['#EB0D8A','#F57E67','#BB077C','#300030'];
$(function(){
	
	 // One Page Smooth Scrolling
		$('.page-scroll a, a.page-scroll').bind('click', function(event) {
			var $anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top
			}, 1500, 'easeInOutExpo');
			event.preventDefault();
		});
		
		createColumnType1(data1, $(".majorbar .content"));
		/* [{"uid":15,"fbid":10153602026237334,"name":"Lina Tan","share":31,"apprequest":7,"referral":38},{"uid":377,"fbid":10152978963107669,"name":"Caroline Lee","share":10,"apprequest":17,"referral":27},{"uid":447,"fbid":10155500902390481,"name":"Chong CinCin Qiuying","share":2,"apprequest":24,"referral":26},{"uid":133,"fbid":10153268860180763,"name":"Isabelle Ow","share":11,"apprequest":14,"referral":25},{"uid":46,"fbid":10153273740849402,"name":"Winston Lew Heng Heng","share":14,"apprequest":2,"referral":16},{"uid":451,"fbid":836171639770537,"name":"Alyssa Yu Tong","share":0,"apprequest":16,"referral":16}]
		
		<div class="influencer media">
                        <div class="media-left">
                            <img src="" alt="icon" width="100" height="100" class="media-object">
                        </div>
                        <div class="influencer_info media-body">
                            <h4 class="name media-heading">
                                Dian Hafiza
                            </h4>
                            <p class="social_info">
                                <span class="activated_text"><span class="emnumber">8220</span> People Activated</span>
                                <span class="detail_text"><span class="emnumber">999</span> by Share <span class="emnumber">999</span> by Invite</span>
                            </p>
                        </div>
                    </div>
					
		*/
		//top influencer
		$("#topinfluencers .col-sm-6").empty();
		$.each(topinflu, function(index,value){
			var tempa = value.referral == 1 ? " Person Activated" : " People Activated";
			var a = $("<div>").addClass("influencer media")
						.append($("<div>").addClass("media-left").attr("data-rank-number", index+1)
							.append(
								$("<a>").attr({href:"#"}).click(function(e){e.preventDefault();}).css({cursor:"default"}).append(
								$("<img>").attr({src:"https://graph.facebook.com/"+value.fbid.slice(1)+"/picture?width=100&height=100",width:100,height:100})).addClass("media-object")
							)
						)
						.append($("<div>").addClass("influencer_info media-body")
							.append($("<h4>").addClass("name media-heading").text(value.name))
							.append($("<p>").addClass("social_info")
								.append($("<span>").addClass("activated_text")
									.append($("<span>").addClass("emnumber").text(value.referral))
									.append(document.createTextNode(tempa))
								)
								.append($("<span>").addClass("detail_text")
									.append($("<span>").addClass("emnumber").text(value.share))
									.append(document.createTextNode(" by Share "))
									.append($("<span>").addClass("emnumber").text(value.apprequest))
									.append(document.createTextNode(" by Invite "))
								)
							)
						)
			if(index < 3){
				$("#topinfluencers .col-sm-6:eq(0)").append(a);
			}else{
				$("#topinfluencers .col-sm-6:eq(1)").append(a);
			}
		});
		
		//get gender dominant
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
		createDonutType1($("#countrydonut"), $("#countrylegend"), countryPerc,colorsgreen,'<?=$ownfolder?>images/icon6.png',52,51);
		createDonutType1($("#agedonut"), $("#agelegend"), agePerc,colorspurple,'<?=$ownfolder?>images/icon5.png',52,53);
		
		createColumnType2($("#genderbarchart"), colorsblue, date2, generateColumnData(genderPerc, 4), 'No. of Participants', 7);
		createColumnType2($("#countrybarchart"), colorsgreen, date2, generateColumnData(countryPerc, 4), 'No. of Participants', 7);
		createColumnType2($("#agebarchart"), colorspurple, date2, generateColumnData(agePerc, 4), 'No. of Participants', 7);
		createColumnType3($("#refbarchart"), colorsblue, date2, generateColumnData(referPerc,4), 'Participants', 19);
		
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
	
	//generate table
	$('#table1').bootstrapTable({
		method:"get",
		url:"<?=SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"?>",
		ncmethod:"getUserTable",
		exportfilename:"usertable",
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
			field: 'tt',
			title: 'Installed Time',
			sortable:true
		}]
	}).bootstrapTable('hideColumns', {
		columns:[
			'id','first_name','last_name', 'fbid', 'phone','ic','age_range', 'country', 'locale','ip','share', 'apprequest', 'fbverified','fblikeshown','tt'
		]
	});
});
</script>