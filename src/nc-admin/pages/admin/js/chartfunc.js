// JavaScript Document
var isoCountries = {
    'AF' : 'Afghanistan',
    'AX' : 'Aland Islands',
    'AL' : 'Albania',
    'DZ' : 'Algeria',
    'AS' : 'American Samoa',
    'AD' : 'Andorra',
    'AO' : 'Angola',
    'AI' : 'Anguilla',
    'AQ' : 'Antarctica',
    'AG' : 'Antigua And Barbuda',
    'AR' : 'Argentina',
    'AM' : 'Armenia',
    'AW' : 'Aruba',
    'AU' : 'Australia',
    'AT' : 'Austria',
    'AZ' : 'Azerbaijan',
    'BS' : 'Bahamas',
    'BH' : 'Bahrain',
    'BD' : 'Bangladesh',
    'BB' : 'Barbados',
    'BY' : 'Belarus',
    'BE' : 'Belgium',
    'BZ' : 'Belize',
    'BJ' : 'Benin',
    'BM' : 'Bermuda',
    'BT' : 'Bhutan',
    'BO' : 'Bolivia',
    'BA' : 'Bosnia And Herzegovina',
    'BW' : 'Botswana',
    'BV' : 'Bouvet Island',
    'BR' : 'Brazil',
    'IO' : 'British Indian Ocean Territory',
    'BN' : 'Brunei Darussalam',
    'BG' : 'Bulgaria',
    'BF' : 'Burkina Faso',
    'BI' : 'Burundi',
    'KH' : 'Cambodia',
    'CM' : 'Cameroon',
    'CA' : 'Canada',
    'CV' : 'Cape Verde',
    'KY' : 'Cayman Islands',
    'CF' : 'Central African Republic',
    'TD' : 'Chad',
    'CL' : 'Chile',
    'CN' : 'China',
    'CX' : 'Christmas Island',
    'CC' : 'Cocos (Keeling) Islands',
    'CO' : 'Colombia',
    'KM' : 'Comoros',
    'CG' : 'Congo',
    'CD' : 'Congo, Democratic Republic',
    'CK' : 'Cook Islands',
    'CR' : 'Costa Rica',
    'CI' : 'Cote D\'Ivoire',
    'HR' : 'Croatia',
    'CU' : 'Cuba',
    'CY' : 'Cyprus',
    'CZ' : 'Czech Republic',
    'DK' : 'Denmark',
    'DJ' : 'Djibouti',
    'DM' : 'Dominica',
    'DO' : 'Dominican Republic',
    'EC' : 'Ecuador',
    'EG' : 'Egypt',
    'SV' : 'El Salvador',
    'GQ' : 'Equatorial Guinea',
    'ER' : 'Eritrea',
    'EE' : 'Estonia',
    'ET' : 'Ethiopia',
    'FK' : 'Falkland Islands (Malvinas)',
    'FO' : 'Faroe Islands',
    'FJ' : 'Fiji',
    'FI' : 'Finland',
    'FR' : 'France',
    'GF' : 'French Guiana',
    'PF' : 'French Polynesia',
    'TF' : 'French Southern Territories',
    'GA' : 'Gabon',
    'GM' : 'Gambia',
    'GE' : 'Georgia',
    'DE' : 'Germany',
    'GH' : 'Ghana',
    'GI' : 'Gibraltar',
    'GR' : 'Greece',
    'GL' : 'Greenland',
    'GD' : 'Grenada',
    'GP' : 'Guadeloupe',
    'GU' : 'Guam',
    'GT' : 'Guatemala',
    'GG' : 'Guernsey',
    'GN' : 'Guinea',
    'GW' : 'Guinea-Bissau',
    'GY' : 'Guyana',
    'HT' : 'Haiti',
    'HM' : 'Heard Island & Mcdonald Islands',
    'VA' : 'Holy See (Vatican City State)',
    'HN' : 'Honduras',
    'HK' : 'Hong Kong',
    'HU' : 'Hungary',
    'IS' : 'Iceland',
    'IN' : 'India',
    'ID' : 'Indonesia',
    'IR' : 'Iran, Islamic Republic Of',
    'IQ' : 'Iraq',
    'IE' : 'Ireland',
    'IM' : 'Isle Of Man',
    'IL' : 'Israel',
    'IT' : 'Italy',
    'JM' : 'Jamaica',
    'JP' : 'Japan',
    'JE' : 'Jersey',
    'JO' : 'Jordan',
    'KZ' : 'Kazakhstan',
    'KE' : 'Kenya',
    'KI' : 'Kiribati',
    'KR' : 'Korea',
    'KW' : 'Kuwait',
    'KG' : 'Kyrgyzstan',
    'LA' : 'Lao People\'s Democratic Republic',
    'LV' : 'Latvia',
    'LB' : 'Lebanon',
    'LS' : 'Lesotho',
    'LR' : 'Liberia',
    'LY' : 'Libyan Arab Jamahiriya',
    'LI' : 'Liechtenstein',
    'LT' : 'Lithuania',
    'LU' : 'Luxembourg',
    'MO' : 'Macao',
    'MK' : 'Macedonia',
    'MG' : 'Madagascar',
    'MW' : 'Malawi',
    'MY' : 'Malaysia',
    'MV' : 'Maldives',
    'ML' : 'Mali',
    'MT' : 'Malta',
    'MH' : 'Marshall Islands',
    'MQ' : 'Martinique',
    'MR' : 'Mauritania',
    'MU' : 'Mauritius',
    'YT' : 'Mayotte',
    'MX' : 'Mexico',
    'FM' : 'Micronesia, Federated States Of',
    'MD' : 'Moldova',
    'MC' : 'Monaco',
    'MN' : 'Mongolia',
    'ME' : 'Montenegro',
    'MS' : 'Montserrat',
    'MA' : 'Morocco',
    'MZ' : 'Mozambique',
    'MM' : 'Myanmar',
    'NA' : 'Namibia',
    'NR' : 'Nauru',
    'NP' : 'Nepal',
    'NL' : 'Netherlands',
    'AN' : 'Netherlands Antilles',
    'NC' : 'New Caledonia',
    'NZ' : 'New Zealand',
    'NI' : 'Nicaragua',
    'NE' : 'Niger',
    'NG' : 'Nigeria',
    'NU' : 'Niue',
    'NF' : 'Norfolk Island',
    'MP' : 'Northern Mariana Islands',
    'NO' : 'Norway',
    'OM' : 'Oman',
    'PK' : 'Pakistan',
    'PW' : 'Palau',
    'PS' : 'Palestinian Territory, Occupied',
    'PA' : 'Panama',
    'PG' : 'Papua New Guinea',
    'PY' : 'Paraguay',
    'PE' : 'Peru',
    'PH' : 'Philippines',
    'PN' : 'Pitcairn',
    'PL' : 'Poland',
    'PT' : 'Portugal',
    'PR' : 'Puerto Rico',
    'QA' : 'Qatar',
    'RE' : 'Reunion',
    'RO' : 'Romania',
    'RU' : 'Russian Federation',
    'RW' : 'Rwanda',
    'BL' : 'Saint Barthelemy',
    'SH' : 'Saint Helena',
    'KN' : 'Saint Kitts And Nevis',
    'LC' : 'Saint Lucia',
    'MF' : 'Saint Martin',
    'PM' : 'Saint Pierre And Miquelon',
    'VC' : 'Saint Vincent And Grenadines',
    'WS' : 'Samoa',
    'SM' : 'San Marino',
    'ST' : 'Sao Tome And Principe',
    'SA' : 'Saudi Arabia',
    'SN' : 'Senegal',
    'RS' : 'Serbia',
    'SC' : 'Seychelles',
    'SL' : 'Sierra Leone',
    'SG' : 'Singapore',
    'SK' : 'Slovakia',
    'SI' : 'Slovenia',
    'SB' : 'Solomon Islands',
    'SO' : 'Somalia',
    'ZA' : 'South Africa',
    'GS' : 'South Georgia And Sandwich Isl.',
    'ES' : 'Spain',
    'LK' : 'Sri Lanka',
    'SD' : 'Sudan',
    'SR' : 'Suriname',
    'SJ' : 'Svalbard And Jan Mayen',
    'SZ' : 'Swaziland',
    'SE' : 'Sweden',
    'CH' : 'Switzerland',
    'SY' : 'Syrian Arab Republic',
    'TW' : 'Taiwan',
    'TJ' : 'Tajikistan',
    'TZ' : 'Tanzania',
    'TH' : 'Thailand',
    'TL' : 'Timor-Leste',
    'TG' : 'Togo',
    'TK' : 'Tokelau',
    'TO' : 'Tonga',
    'TT' : 'Trinidad And Tobago',
    'TN' : 'Tunisia',
    'TR' : 'Turkey',
    'TM' : 'Turkmenistan',
    'TC' : 'Turks And Caicos Islands',
    'TV' : 'Tuvalu',
    'UG' : 'Uganda',
    'UA' : 'Ukraine',
    'AE' : 'United Arab Emirates',
    'GB' : 'United Kingdom',
    'US' : 'United States',
    'UM' : 'United States Outlying Islands',
    'UY' : 'Uruguay',
    'UZ' : 'Uzbekistan',
    'VU' : 'Vanuatu',
    'VE' : 'Venezuela',
    'VN' : 'Viet Nam',
    'VG' : 'Virgin Islands, British',
    'VI' : 'Virgin Islands, U.S.',
    'WF' : 'Wallis And Futuna',
    'EH' : 'Western Sahara',
    'YE' : 'Yemen',
    'ZM' : 'Zambia',
    'ZW' : 'Zimbabwe'
};
 
function getCountryName (countryCode) {
    if (isoCountries.hasOwnProperty(countryCode)) {
        return isoCountries[countryCode];
    } else {
        return countryCode;
    }
}
function getHighestAmountItem(data){
	var amount = -1, topitem;
	$.each(data, function(index,value){
		if(amount < value.amount){
			amount = value.amount;
			topitem = value;
		}
	});
	return topitem;
}
function f1(data, otherlabelname){
		var d, maxn, maxObj,maxObjIndex, total, othersAmount, otherlabel;
		//get max
		maxn = -1;
		total = 0;
		$.each(data, function(index,value){
			total += value.amount;
			if(value.amount > maxn){
				maxObj = value;
				maxn = value.amount;
				maxObjIndex = index;
			}
		});
		//re-arrange data
		othersAmount = 0;
		otherlabel = '';
		$.each(data, function(index,value){
			if(maxObjIndex != index){
				otherlabel = value.name;
				othersAmount += value.amount;
			}
		});
		return {
			major:maxObj.name,
			data:[
				{y:total > 0 ? parseInt(maxObj.amount/total*10000,10)/100 : 0, mylabel:maxObj.name, amount:maxObj.amount},
				{y:total > 0 ? parseInt(othersAmount/total*10000,10)/100 : 0, mylabel:otherlabelname ? otherlabelname : otherlabel , amount:othersAmount}
			]
		};
	}
function sortByAmount(a,b){
	return a.amount > b.amount ? -1 : a.amount < b.amount ? 1 : 0;
}
function f2(data, otherlabelname, maxitems, usePercentage){
		var d, maxObj, total, othersAmount, otherlabel = null, pdata;
		//get max
		total = 0;
		data.sort(sortByAmount);
		maxObj = data[0];
		pdata = [];
		othersAmount = 0;
		otherlabel = '';
		$.each(data, function(index,value){
			total += value.amount;
		});
		$.each(data, function(index,value){
			if(index+1 < maxitems){
				if(usePercentage){
					pdata.push({y:total > 0 ? parseInt(value.amount/total*10000,10)/100 : 0, mylabel:value.name, amount:value.amount});
				}else{
					pdata.push({y:value.amount, mylabel:getCountryName(value.name)});
				}
			}else{
				otherlabel = getCountryName(value.name);
				othersAmount += value.amount;
			}
		});
		if(otherlabel){
			if(usePercentage){
				pdata.push({y:total > 0 ? parseInt(othersAmount/total*10000,10)/100 : 0, mylabel:otherlabelname ? otherlabelname : otherlabel, amount:othersAmount});
			}else{
				pdata.push({y:othersAmount, mylabel:otherlabelname ? otherlabelname : otherlabel});
			}
		}
		return {
			major:getCountryName(maxObj.name),
			data:pdata
		};
	}
function generateColumnData(data, maxitems){
		/*
		 [
	{
		"amount":872,
		"name":"Female",
		"data":[{"amount":10,"tt":"2015-04-24 00:00:00"},{"amount":16,"tt":"2015-04-25 00:00:00"},{"amount":41,"tt":"2015-04-26 00:00:00"},{"amount":23,"tt":"2015-04-27 00:00:00"},{"amount":47,"tt":"2015-04-28 00:00:00"},{"amount":48,"tt":"2015-04-29 00:00:00"},{"amount":18,"tt":"2015-04-30 00:00:00"},{"amount":53,"tt":"2015-05-01 00:00:00"},{"amount":72,"tt":"2015-05-02 00:00:00"},{"amount":90,"tt":"2015-05-03 00:00:00"},{"amount":76,"tt":"2015-05-04 00:00:00"},{"amount":77,"tt":"2015-05-05 00:00:00"},{"amount":99,"tt":"2015-05-06 00:00:00"},{"amount":122,"tt":"2015-05-07 00:00:00"},{"amount":80,"tt":"2015-05-08 00:00:00"}],
		"chartdata":[10,16,41,23,47,48,18,53,72,90,76,77,99,122,80]
	},{
		"amount":365,"name":"Male","data":[{"amount":7,"tt":"2015-04-24 00:00:00"},{"amount":8,"tt":"2015-04-25 00:00:00"},{"amount":27,"tt":"2015-04-26 00:00:00"},{"amount":14,"tt":"2015-04-27 00:00:00"},{"amount":17,"tt":"2015-04-28 00:00:00"},{"amount":10,"tt":"2015-04-29 00:00:00"},{"amount":11,"tt":"2015-04-30 00:00:00"},{"amount":32,"tt":"2015-05-01 00:00:00"},{"amount":30,"tt":"2015-05-02 00:00:00"},{"amount":38,"tt":"2015-05-03 00:00:00"},{"amount":33,"tt":"2015-05-04 00:00:00"},{"amount":38,"tt":"2015-05-05 00:00:00"},{"amount":24,"tt":"2015-05-06 00:00:00"},{"amount":37,"tt":"2015-05-07 00:00:00"},{"amount":39,"tt":"2015-05-08 00:00:00"}],"chartdata":[7,8,27,14,17,10,11,32,30,38,33,38,24,37,39]}]
		 */	
		 var ar = [];
		 var others = {
			 name:"Others",
			 data:[]
		 };
		 $.each(data, function(index,value){
			 if(index < maxitems-1){
				if(!ar[index]){
					ar[index] = {
						name:getCountryName(value.name),
						data:[]
					}
				}
				$.each(value.chartdata, function(index2, value2){
					ar[index].data[index2] = {
						y : value2,
						mylabel: getCountryName(value.name)
					};
				});
			 }else{
				 $.each(value.chartdata, function(index2, value2){
					if(!others.data[index2]){
						others.data[index2] = {
							y : 0,
							mylabel: 'Others'
						}	
					}
					others.data[index2].y += value2;
				});
			 }
		 });
		 
		if(data.length > maxitems-1){
			ar.push(others);
		}
		return ar;
}
function createColumnType1(data, element){
	//parse data to preferred format
	var data1a = f2(data.gender, null,2,true);
	var data1b = f2(data.country, "Others",2,true);
	var data1c = f2(data.age, "Others",2,true);
	
	var axisCat = [data1a.major,data1b.major,data1c.major];
	var seriesgourp = [{
			name: 'percentage',
			data: [
				data1a.data[1] ? data1a.data[1] : 0,
				data1b.data[1] ? data1b.data[1] : 0,
				data1c.data[1] ? data1c.data[1] : 0]
		}, {
			name: '',
			data: [
				data1a.data[0],
				data1b.data[0],
				data1c.data[0]]
		}];
	
	element.highcharts({
		chart: {
			type: 'column',
			 margin: [0,0,30,0]
		},
		colors:['#D7D7D7','#6DCFF6'],
		title: {
			text: null
		},
		xAxis: {
			categories: axisCat,
			lineColor: 'transparent',
			tickWidth:0,
			minorTickLength: 0,
			tickLength:0
		},
		yAxis: {
			min: 0,
			max:100,
			minorTickLength: 0,
			tickLength:0,
			gridLineWidth: 0,
			labels: {
				enabled: false
			},
			title: {
				text: null
			},
			stackLabels: {
				style: {
					color: 'white',
					textShadow: null
				},
				enabled: true,
				verticalAlign:'bottom',
				formatter: function() {
					
					return this.total > 0 && this.axis.series[1].yData[this.x] >0? (this.axis.series[1].yData[this.x] / this.total * 100).toPrecision(3) + '%' : "";
					
				}
			}
		},
		legend: {
			enable:false
		},
		tooltip: {
			formatter: function () {
				return '<b>' + this.point.mylabel + '</b><br/>' +
					this.point.amount + '<br/>';
			}
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				borderWidth: 1,
				dataLabels: {
					enabled: false,
					color:'white',
					style: {
						textShadow: null
					}
				}
			}
		},
		credits:{
			enabled:false
		},
		legend:{
			enabled:false 
		},
		exporting: {
			enabled:false
		},
		series:seriesgourp
	});
}
function createColumnType2(element, colors, date, data, axislabel, maxColumn){
	var calmaxColumn = date.length -1 < maxColumn ? date.length -1 : maxColumn;
	var xsetting;
	if(date.length -1 < maxColumn){
		xsetting = {
			categories: date,
			lineColor: 'transparent',
			tickWidth:0,
			minorTickLength: 0,
			tickLength:0,
			max:calmaxColumn,
			min:0
		};
	}else{
		
		xsetting = {
			categories: date,
			lineColor: 'transparent',
			tickWidth:0,
			minorTickLength: 0,
			tickLength:0,
			min:date.length - maxColumn
		};
	}
	var vcolor;
	if(data[data.length-1].name == "Others"){
		vcolor = colors.slice(0, data.length-1);
		vcolor.push('#d7d7d7');
	}else{
		vcolor = colors;
	}
	element.highcharts({
		chart: {
			type: 'column'
		},
		colors:vcolor,
		title: {
			text: null
		},
		xAxis: xsetting,
		scrollbar: {
			enabled: true
		},
		yAxis: {
			min: 0,
			minorTickLength: 0,
			tickLength:0,
			gridLineWidth: 0,
			labels: {
				enabled: false
			},
			title: {
				text: axislabel
			},
			stackLabels: {
				enabled: false
			}
		},
		legend: {
			enabled:false
		},
		tooltip: {
			formatter: function () {
					var points='<table class="tip"><caption>'+this.x+'</caption>'
					+'<tbody>';
					$.each(this.points,function(i,point){
						points+='<tr><th style="color: '+point.series.color+'">'+point.series.name+': </th>'
						+ '<td style="text-align: right">'+point.y+'</td></tr>'
					});
					points+='<tr><th>Total: </th>'
					+'<td style="text-align:right"><b>'+this.points[0].total+'</b></td></tr>'
					+'</tbody></table>';
					return points;
			},
			useHTML: true,
			shared: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				borderWidth: 1,
				dataLabels: {
					enabled: false,
					color:'white',
					style: {
						textShadow: null
					}
				}
			}
		},
		credits:{
			enabled:false
		},
		legend:{
			enabled:false 
		},
		exporting: {
			enabled:false
		},
		series:data
	});
}
function createColumnType3(element, colors, date, data, axislabel, maxColumn){
	var calmaxColumn = date.length -1 < maxColumn ? date.length -1 : maxColumn;
	var xsetting;
	if(date.length -1 < maxColumn){
		xsetting = {
			categories: date,
			lineColor: 'transparent',
			tickWidth:0,
			minorTickLength: 0,
			tickLength:0,
			max:calmaxColumn,
			min:0
		};
	}else{
		
		xsetting = {
			categories: date,
			lineColor: 'transparent',
			tickWidth:0,
			minorTickLength: 0,
			tickLength:0,
			min:date.length - maxColumn
		};
	}
	element.highcharts({
		chart: {
			type: 'column'
		},
		colors:colors,
		title: {
			text: null
		},
		xAxis:xsetting,
		scrollbar: {
			enabled: true
		},
		yAxis: {
			min: 0,
			minorTickLength: 0,
			tickLength:0,
			gridLineWidth: 0,
			labels: {
				enabled: false
			},
			title: {
				text: axislabel
			},
			stackLabels: {
				style: {
					color: '#606060',
					textShadow: null,
					fontWeight:100
				},
				enabled: true,
				formatter: function() {
					
					return this.total > 0  && this.axis.series[0].yData[this.x] > 0? (this.axis.series[0].yData[this.x] / this.total * 100).toPrecision(3) + '%': "";
					
				}
			}
		},
		legend: {
			enabled:false
		},
		tooltip: {
			formatter: function () {
					var points='<table class="tip"><caption>'+this.x+'</caption>'
					+'<tbody>';
					$.each(this.points,function(i,point){
						points+='<tr><th style="color: '+point.series.color+'">'+point.series.name+': </th>'
						+ '<td style="text-align: right">'+point.y+'</td></tr>'
					});
					points+='<tr><th>Total: </th>'
					+'<td style="text-align:right"><b>'+this.points[0].total+'</b></td></tr>'
					+'</tbody></table>';
					return points;
			},
			useHTML: true,
			shared: true
		},
		plotOptions: {
			column: {
				stacking: 'normal',
				pointPadding: 0,
				borderWidth: 1,
				dataLabels: {
					enabled: false,
					color:'white',
					style: {
						textShadow: null
					}
				}
			}
		},
		credits:{
			enabled:false
		},
		legend:{
			enabled:false 
		},
		exporting: {
			enabled:false
		},
		series:data
	});
}

function createDonutType1(donutelement, legendelement, data, colors, imagesrc, imagewidth,imageheight){
	var data1a = f2(data, 'Others',4,true);
	var vcolor;
	if(data1a.data[data1a.data.length-1].mylabel == "Others"){
		vcolor = colors.slice(0, data1a.data.length-1);
		vcolor.push('#d7d7d7');
	}else{
		vcolor = colors;
	}
	donutelement.highcharts({
		chart:{
			type:'pie',
			backgroundColor:'transparent',
			margin:[0,0,0,0],
			events:{
				redraw:function(e){
					if(this.selfimg && this.selfimg.element) $(this.selfimg.element).remove();
					var xpos = '50%';
					var ypos = '50%';
					var circleradius = $(this.container).width()*0.4;
					
					// Render the image
					//get the rescale size
					var scale = Math.min(circleradius*1.4 / imageheight , circleradius*1.4 / imagewidth)*0.7;
					this.selfimg = this.renderer.image(imagesrc, $(this.container).width()/2 - scale*imagewidth /2 , $(this.container).width()/2 -  scale*imageheight /2, scale*imagewidth, scale*imageheight).add();
				}
			}
		},
		title:{
			text:''
		},
		credits:{
			enabled:false
		},
		tooltip:{
			enabled:false
		},
		exporting:{
			enabled:false
		},
		plotOptions:{
			pie:{
				colors:vcolor,
				enableMouseTracking:false,
				borderWidth:0,
				innerSize:'80%',
				dataLabels:{
					enabled: false
				}
			}
		},
		series: [{
			data:data1a.data
		}]
	},
	function(chart){
		var xpos = '50%';
		var ypos = '50%';
		var circleradius = $(chart.container).width()*0.4;
		
		// Render the image
		//get the rescale size
		var scale = Math.min(circleradius*1.4 / imageheight , circleradius*1.4 / imagewidth)*0.7;
		this.selfimg = chart.renderer.image(imagesrc, $(chart.container).width()/2 - scale*imagewidth /2 , $(chart.container).width()/2 -  scale*imageheight /2, scale*imagewidth, scale*imageheight).add();

	});
	
	legendelement.empty();
	donutelement.parent().siblings(".dominant").text(data1a.major);
	$.each(data1a.data, function(index,value){
		legendelement.append(
			$("<div>").addClass("legend").append(
				$("<span>").addClass("name").text(getCountryName(value.mylabel))
				.prepend(
					$("<span>").addClass("number").text(value.y+"%").css({"background-color":vcolor[index]})
				)
			)
		);
	});
}
