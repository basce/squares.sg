<?php
class reporting{
	
	private $conn;
	private $reportTable;
	
	function __construct($obj){
		$this->conn = isset($obj["conn"])?$obj["conn"]:NULL;
		//$this->conn->debug = true;
		
		if(defined("DB_REPORT")){
			$this->reportTable = DB_REPORT;
		}else{
			//generate table if DB_PREFIX is exist
			if(defined("DB_PREFIX")){
				$this->reportTable = DB_PREFIX."__reports";
			}
		}
		
		/*$this->reportTable = isset($obj["table"])?$obj["table"]:NULL;*/
		$this->generateTable();
		
		if(!$this->conn){
			throw new ErrorException("conn cannot be empty");
		}
	
	}
	
	private function generateTable(){
		$query = "
		CREATE TABLE IF NOT EXISTS `".$this->reportTable."` (
		  `rid` int(20) NOT NULL AUTO_INCREMENT,
		  `reportref` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		  `amount` int(20) NOT NULL,
		  `marker` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
		  `tt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`rid`),
		  KEY `reportref` (`reportref`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";
		$this->conn->Execute($query);
	}
	function getRawData($tablename = "", $timefield = "", $accumulate = "", $filter = "", $amountfield ="", $hourInterval=1){
		$reportref = md5(json_encode(func_get_args()));
		$query = "SELECT amount, marker, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt DESC LIMIT 1";
		$lastData = $this->conn->GetRow($query, array($reportref));
		if(sizeof($lastData)){
			//with data recorded, grab data after the recordeddate.
			if($filter){ $filter1 = " AND ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;	
			}
			$startamount = $lastData["amount"];
		}else{
			if($filter){ $filter1 = " WHERE ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}
			$startamount = 0;
		}
		$data = $this->conn->GetArray($query);
		if(sizeof($data)){
			$firstdata = '';
			$serielizeData = array();
			if(sizeof($lastData)){
				$serielizeData[$lastData["tt"]] = array(
														"tt"=>$lastData["tt"],
														"count"=>$lastData["amount"]
														);
			}
			$lasttt = strtotime('0000-00-00 00:00:00');
			foreach($data as $key2=>$row){
				//down to hours.
				$datetime = explode(" ",$row["tt"]);
				if(strtotime($row["tt"]) > $lasttt){
					$lasttt = strtotime($row["tt"]);
				}
				$date = explode("-",$datetime[0]);
				$time = explode(":",$datetime[1]);
				if($hourInterval){
					$newtt = $datetime[0]." ".$time[0].":00:00";
				}else{
					//is day
					$newtt = $datetime[0]." 00:00:00";
				}
				if(isset($serielizeData[$newtt])){
					if($amountfield){
						$serielizeData[$newtt]["count"] += $data["count"];
					}else{
						$serielizeData[$newtt]["count"]++;
					}
				}else{
					if($amountfield){
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>$data["count"]
														);
					}else{
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>1
														);
					}
				}
			}
			usort($serielizeData, array($this, "sortBytt"));
			$marker = $timefield." > '".date("Y-m-d H:i:s", $lasttt)."'";
			$queryAr= array();
			foreach($serielizeData as $key2=>$row2){
				$startamount = $row2["count"];
				$tempquery = array($reportref, $startamount, $row2["tt"], addslashes($marker));
				$queryAr[] = "('".implode("','",$tempquery)."')";
			}
			//remove last row of data.
			if(sizeof($lastData)){
				$query = "DELETE FROM ".$this->reportTable." WHERE reportref = ? AND tt = ?";
				$this->conn->Execute($query, array($reportref, $lastData["tt"]));
			}
			//insert into reporttable
			if(sizeof($queryAr)){
				$query = "INSERT INTO ".$this->reportTable." (reportref, amount, tt, marker) VALUES ".implode(",",$queryAr);
				$this->conn->Execute($query);
			}
		}
		
		//grab data from table
		$query = "SELECT amount, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt";
		$data = $this->conn->GetArray($query, array($reportref));
		
		return $data;
	}
	function getHighChartTimelineAndBar($table, $maintitle = "table", $subtitle = "", $timeInterval="auto", $tableHeight="400px", $color1 = 0, $color2 = 4){
		/*
		get the difference from first and last data,
		then we decide whether want to show the data in daily or hourly if the $timeInterval set as zero
		*/

		$tableID = md5(json_encode(func_get_args()));
		$chartdata = array();
		$seriesdata = array();
		$rawdatas = array();

		$title = isset($table["title"]) ? $table["title"] : "data";
		$filter = isset($table["filter"]) ? $table["filter"] : "";
		$tablename = isset($table["tablename"]) ? $table["tablename"] : "";
		$countfield = isset($table["countfield"]) ? $table["countfield"] : "";
		$timefield = isset($table["timefield"]) ? $table["timefield"] : "";
		$amountfield = isset($table["amountfield"]) ? $table["amountfield"] : "";
		$reportref =$tableID;
		//get last item in report table for corresponding table and fields
		$query = "SELECT amount, marker, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt DESC LIMIT 1";
		$lastData = $this->conn->GetRow($query, array($reportref));
		if(sizeof($lastData)){
			//with data recorded, grab data after the recordeddate.
			if($filter){ $filter1 = " AND ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;	
			}
			$startamount = $lastData["amount"];
		}else{
			if($filter){ $filter1 = " WHERE ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}
			$startamount = 0;
		}
		$data = $this->conn->GetArray($query);
		if(sizeof($data)){
			$firstdata = '';
			$serielizeData = array();
			//$serielizeData_notaccumutive = array();
			if(sizeof($lastData)){
				$serielizeData[$lastData["tt"]] = array(
														"tt"=>$lastData["tt"],
														"count"=>$lastData["amount"]
														);
				/*$serielizeData_notaccumutive[$lastData["tt"]] = array(
														"tt"=>$lastData["tt"],
														"count"=>$lastData["amount"]
														);*/
			}
			$lasttt = strtotime('0000-00-00 00:00:00');
			foreach($data as $key2=>$row){
				if($timeInterval == "daily"){
					//down to days.
					$datetime = explode(" ",$row["tt"]);
					if(strtotime($row["tt"]) > $lasttt){
						$lasttt = strtotime($row["tt"]);
					}
					$date = explode("-",$datetime[0]);
					$newtt = $datetime[0]." 00:00:00";
				}else/* 
					not working
				if($timeInterval == "3 hours Interval" || $timeInterval == "6 hours Interval" || $timeInterval == "12 hours Interval"){
					$hourNumber = (int) $timeInterval[0];
					//down to X hours.
					$datetime = explode(" ",$row["tt"]);
					if(strtotime($row["tt"]) > $lasttt){
						$lasttt = strtotime($row["tt"]);
					}
					$date = explode("-",$datetime[0]);
					$time = explode(":",$datetime[1]);
					$hourtext = ($time[0]%$hourNumber)*$hourNumber;
					$hourtext = $hourtext."";
					if(strlen($hourtext) == 1 ){
						 $hourtext = "0".$hourtext;
					}
					$newtt = $datetime[0]." ".$hourtext.":00:00";
				}else*/{
					//down to hours.
					$datetime = explode(" ",$row["tt"]);
					if(strtotime($row["tt"]) > $lasttt){
						$lasttt = strtotime($row["tt"]);
					}
					$date = explode("-",$datetime[0]);
					$time = explode(":",$datetime[1]);
					$newtt = $datetime[0]." ".$time[0].":00:00";
				}
				if(isset($serielizeData[$newtt])){
					if($amountfield){
						$serielizeData[$newtt]["count"] += $data["count"];
					}else{
						$serielizeData[$newtt]["count"]++;
					}
				}else{
					if($amountfield){
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>$data["count"]
														);
					}else{
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>1
														);
					}
				}
			}
			usort($serielizeData, array($this, "sortBytt"));
			$marker = $timefield." > '".date("Y-m-d H:i:s", $lasttt)."'";
			$queryAr= array();
			foreach($serielizeData as $key2=>$row2){
				$startamount = $row2["count"];
				$tempquery = array($reportref, $startamount, $row2["tt"], addslashes($marker));
				$queryAr[] = "('".implode("','",$tempquery)."')";
			}
			//remove last row of data.
			if(sizeof($lastData)){
				$query = "DELETE FROM ".$this->reportTable." WHERE reportref = ? AND tt = ?";
				$this->conn->Execute($query, array($reportref, $lastData["tt"]));
			}
			//insert into reporttable
			if(sizeof($queryAr)){
				$query = "INSERT INTO ".$this->reportTable." (reportref, amount, tt, marker) VALUES ".implode(",",$queryAr);
				$this->conn->Execute($query);
			}
		}
			
		//grab data from table
		$query = "SELECT amount, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt";
		$data = $this->conn->GetArray($query, array($reportref));
		$dataAr = array();
		$datanotaccumative = array();
		$vamount = 0;
		foreach($data as $key2=>$row){
			$datetime = explode(" ",$row["tt"]);
			$date = explode("-",$datetime[0]);
			$time = explode(":",$datetime[1]);
			$vamount += $row["amount"];
			$dataAr[] = "[Date.UTC(".$date[0].",".($date[1]-1).",".$date[2].",".$time[0]."), ".$vamount."]";
			$datanotaccumative[] = "[Date.UTC(".$date[0].",".($date[1]-1).",".$date[2].",".$time[0]."), ".$row["amount"]."]";
		}

		$seriesdata[] = "{
			gridLineWidth: 0,
			title: {
				text: 'total ".$countfield."',
				style: {
					color: Highcharts.getOptions().colors[".$color1."]
				}
			},
			labels: {
				format: '{value}',
				style: {
					color: Highcharts.getOptions().colors[".$color1."]
				}
			}
		}";
		$seriesdata[] = "{
				gridLineWidth: 0,
				title: {
					text: '".$countfield." (".$timeInterval.")',
					style: {
						color: Highcharts.getOptions().colors[".$color2."],
					}
				},
				labels: {
					format: '{value}',
					style: {
						color: Highcharts.getOptions().colors[".$color2."],
					}
				},
				opposite: 'true'
			}";
		$chartdata[] = "{
			name:'".$title."',
			type: 'spline',
			dashStyle: 'ShortDot',	
			color:Highcharts.getOptions().colors[".$color1."],
			yAxis: 0,
			data:[
				".implode(",",$dataAr)."
			]
		}";
		$chartdata[] = "{
			name:'".$title." (".$timeInterval.")',
			type: 'area',
			yAxis: 1,
			dashStyle: 'ShortDot',	
			color:Highcharts.getOptions().colors[".$color2."],
			data:[
				".implode(",",$datanotaccumative)."
			]
		}";
		
		$html = "<div id=\"table_".$tableID."\" style=\"height:".$tableHeight.";\"></div><script>$(function () {
    $('#table_".$tableID."').highcharts({
        chart: {
            zoomType: 'x',
        },
        title: {
            text: '".$maintitle."'
        },
        subtitle: {
            text: '".$subtitle."'
        },
		credits: {
            enabled: false
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
				hour: '%H',
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: [
		".implode(",",$seriesdata)."
		],
        tooltip: {
            shared: true
        },

        plotOptions: {
            spline: {
                marker: {
                    enabled: false
                }
            }
        },

        series: [".
		implode(",",$chartdata)
		."]
    });
});</script>";
echo $html;
	}
	/*
		$tablear
		$tablename, $countfield, $timefield, $accumulate = true, $amountfield="", $filter=""
	*/
	function getHighChartMultiTimeLine($tablear, $yaxislabel = "y axis", $maintitle = "table", $subtitle = ""){
		$tableID = md5(json_encode(func_get_args()));
		$chartdata = array();
		$seriesdata = array();
		$rawdatas = array();
		foreach($tablear as $key=>$table){
			$title = isset($table["title"]) ? $table["title"] : "data".$key;
			$filter = isset($table["filter"]) ? $table["filter"] : "";
			$tablename = isset($table["tablename"]) ? $table["tablename"] : "";
			$countfield = isset($table["countfield"]) ? $table["countfield"] : "";
			$timefield = isset($table["timefield"]) ? $table["timefield"] : "";
			$accumulate = isset($table["accumulate"]) ? $table["accumulate"] : true;
			$amountfield = isset($table["amountfield"]) ? $table["amountfield"] : "";
			$reportref = md5(json_encode($table));
			//get last item in report table for corresponding table and fields
			$query = "SELECT amount, marker, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt DESC LIMIT 1";
			$lastData = $this->conn->GetRow($query, array($reportref));
			if(sizeof($lastData)){
				//with data recorded, grab data after the recordeddate.
				if($filter){ $filter1 = " AND ".$filter; }else{ $filter1 ='';}
				if($amountfield){
					$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;
				}else{
					$query = "SELECT ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;	
				}
				$startamount = $lastData["amount"];
			}else{
				if($filter){ $filter1 = " WHERE ".$filter; }else{ $filter1 ='';}
				if($amountfield){
					$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
				}else{
					$query = "SELECT ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
				}
				$startamount = 0;
			}
			$data = $this->conn->GetArray($query);
			if(sizeof($data)){
				$firstdata = '';
				$serielizeData = array();
				if(sizeof($lastData)){
					$serielizeData[$lastData["tt"]] = array(
															"tt"=>$lastData["tt"],
															"count"=>$lastData["amount"]
															);
				}
				$lasttt = strtotime('0000-00-00 00:00:00');
				foreach($data as $key2=>$row){
					//down to hours.
					$datetime = explode(" ",$row["tt"]);
					if(strtotime($row["tt"]) > $lasttt){
						$lasttt = strtotime($row["tt"]);
					}
					$date = explode("-",$datetime[0]);
					$time = explode(":",$datetime[1]);
					$newtt = $datetime[0]." ".$time[0].":00:00";
					if(isset($serielizeData[$newtt])){
						if($amountfield){
							$serielizeData[$newtt]["count"] += $data["count"];
						}else{
							$serielizeData[$newtt]["count"]++;
						}
					}else{
						if($amountfield){
							$serielizeData[$newtt] = array(
															"tt"=>$newtt,
															"count"=>$data["count"]
															);
						}else{
							$serielizeData[$newtt] = array(
															"tt"=>$newtt,
															"count"=>1
															);
						}
					}
				}
				usort($serielizeData, array($this, "sortBytt"));
				$marker = $timefield." > '".date("Y-m-d H:i:s", $lasttt)."'";
				$queryAr= array();
				foreach($serielizeData as $key2=>$row2){
					$startamount = $row2["count"];
					$tempquery = array($reportref, $startamount, $row2["tt"], addslashes($marker));
					$queryAr[] = "('".implode("','",$tempquery)."')";
				}
				//remove last row of data.
				if(sizeof($lastData)){
					$query = "DELETE FROM ".$this->reportTable." WHERE reportref = ? AND tt = ?";
					$this->conn->Execute($query, array($reportref, $lastData["tt"]));
				}
				//insert into reporttable
				if(sizeof($queryAr)){
					$query = "INSERT INTO ".$this->reportTable." (reportref, amount, tt, marker) VALUES ".implode(",",$queryAr);
					$this->conn->Execute($query);
				}
			}
			
			//grab data from table
			$query = "SELECT amount, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt";
			$data = $this->conn->GetArray($query, array($reportref));
			$dataAr = array();
			$vamount = 0;
			/*
			foreach($data as $key2=>$row){
				$datetime = explode(" ",$row["tt"]);
				$date = explode("-",$datetime[0]);
				$time = explode(":",$datetime[1]);
				if($accumulate){
					$vamount += $row["amount"];
				}else{
					$vamount = $row["amount"];
				}
				$dataAr[] = "[Date.UTC(".$date[0].",".($date[1]-1).",".$date[2].",".$time[0]."), ".$vamount."]";
			}
			*/
			$rawdatas[] = array(
							"title"=>$title,
							"rawdata"=>$data
							);
		}
		//tidy data, get all time
		$timeindexes = array();
		$indexdatas = array();
		foreach($rawdatas as $key=>$value){
			$indexdatas[$key] = array();
			foreach($value["rawdata"] as $key2=>$value2){
				$timeindexes[] = $value2["tt"];
				$indexdatas[$key][$value2["tt"]] = $value2["amount"];
			}
		}
		$timeindexes = array_unique($timeindexes);
		sort($timeindexes);
		$finaldatas = array();
		foreach($rawdatas as $key=>$data){
			$dataAr = array();
			$vamount = 0;
			foreach($timeindexes as $key2=>$value2){
				$datetime = explode(" ",$value2);
				$date = explode("-",$datetime[0]);
				$time = explode(":",$datetime[1]);
				if(isset($indexdatas[$key][$value2])){
					if($accumulate){
						$vamount += $indexdatas[$key][$value2];
					}else{
						$vamount = $indexdatas[$key][$value2];
					}	
				}
				$dataAr[] = "[Date.UTC(".$date[0].",".($date[1]-1).",".$date[2].",".$time[0]."), ".$vamount."]";				
			}
			$finaldatas[] = $dataAr;
		}
		foreach($finaldatas as $key=>$dataAr){
			$seriesdata[] = "{
				gridLineWidth: 0,
				title: {
					text: '".addslashes($rawdatas[$key]["title"])."',
					style: {
						color: Highcharts.getOptions().colors[".$key."]
					}
				},
				labels: {
					format: '{value}',
					style: {
						color: Highcharts.getOptions().colors[".$key."]
					}
				},
				opposite: ".(($key%2)?"false":"true")."
			}";
			$chartdata[] = "{
				name:'".addslashes($rawdatas[$key]["title"])."',
				type: 'spline',
				yAxis:".$key.",				
				data:[
					".implode(",",$dataAr)."
				]
			}";
		}
		
		$html = "<div id=\"table_".$tableID."\" style=\"min-width: 310px; height: 400px; margin: 0 auto\"></div><script>$(function () {
    $('#table_".$tableID."').highcharts({
        chart: {
            zoomType: 'x',
        },
        title: {
            text: '".$maintitle."'
        },
        subtitle: {
            text: '".$subtitle."'
        },
		credits: {
            enabled: false
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
				hour: '%H',
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: [
		".implode(",",$seriesdata)."
		],
        tooltip: {
            shared: true
        },

        plotOptions: {
            spline: {
                marker: {
                    enabled: false
                }
            }
        },

        series: [".
		implode(",",$chartdata)
		."]
    });
});</script>";
echo $html;
	}
	
	function getHighChartPie($tablename, $countlabel, $data, $minwidth = "310px",$maxwidth = "600px", $tableheight = "400px"){
		$tableID = md5(json_encode(func_get_args()));
		?>
		<div id="container_<?=$tableID?>" style="min-width: <?=$minwidth?>; height: <?=$tableheight?>; max-width: <?=$maxwidth?>; margin: 0 auto"></div>
        <script type="text/javascript">
        $(function () {
            $('#container_<?=$tableID?>').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: '<?=$tablename?>'
                },
				credits: {
					enabled: false
				},
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: '<?=$countlabel?>',
                    data: [
                        <?php
							$chartdata = array();
                        	foreach($data as $key=>$value){
								$chartdata[] = "['".htmlspecialchars($key)."', ".$value."]";
							}
							echo implode(",",$chartdata);
						?>
                    ]
                }]
            });
        });
		</script>
        <?php
	}
	
	function getGoogleAnnotatedTimeLine($tablename, $countfield, $timefield, $accumulate = true, $amountfield="", $filter="", $tablewidth = "900px", $tableheight = "240px"){
		$reportref = md5(json_encode(func_get_args()));
		//get last item in report table for corresponding table and fields
		$query = "SELECT amount, marker, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt DESC LIMIT 1";
		$lastData = $this->conn->GetRow($query, array($reportref));
		if(sizeof($lastData)){
			//with data recorded, grab data after the recordeddate.
			if($filter){ $filter1 = " AND ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename." WHERE ".$lastData["marker"].$filter1;	
			}
			$startamount = $lastData["amount"];
		}else{
			if($filter){ $filter1 = " WHERE ".$filter; }else{ $filter1 ='';}
			if($amountfield){
				$query = "SELECT ".$amountfield." as `count`, ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}else{
				$query = "SELECT ".$timefield." as `tt` FROM ".$tablename. $filter1 ;
			}
			$startamount = 0;
		}
		$data = $this->conn->GetArray($query);
		if(sizeof($data)){
			$firstdata = '';
			$serielizeData = array();
			if(sizeof($lastData)){
				$serielizeData[$lastData["tt"]] = array(
														"tt"=>$lastData["tt"],
														"count"=>$lastData["amount"]
														);
			}
			$lasttt = strtotime('0000-00-00 00:00:00');
			foreach($data as $key=>$row){
				//down to hours.
				$datetime = explode(" ",$row["tt"]);
				if(strtotime($row["tt"]) > $lasttt){
					$lasttt = strtotime($row["tt"]);
				}
				$date = explode("-",$datetime[0]);
				$time = explode(":",$datetime[1]);
				$newtt = $datetime[0]." ".$time[0].":00:00";
				if(isset($serielizeData[$newtt])){
					if($amountfield){
						$serielizeData[$newtt]["count"] += $data["count"];
					}else{
						$serielizeData[$newtt]["count"]++;
					}
				}else{
					if($amountfield){
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>$data["count"]
														);
					}else{
						$serielizeData[$newtt] = array(
														"tt"=>$newtt,
														"count"=>1
														);
					}
				}
			}
			usort($serielizeData, array($this, "sortBytt"));
			$marker = $timefield." > '".date("Y-m-d H:i:s", $lasttt)."'";
			$queryAr= array();
			foreach($serielizeData as $key2=>$row2){
				$startamount = $row2["count"];
				$tempquery = array($reportref, $startamount, $row2["tt"], addslashes($marker));
				$queryAr[] = "('".implode("','",$tempquery)."')";
			}
			//remove last row of data.
			if(sizeof($lastData)){
				$query = "DELETE FROM ".$this->reportTable." WHERE reportref = ? AND tt = ?";
				$this->conn->Execute($query, array($reportref, $lastData["tt"]));
			}
			//insert into reporttable
			if(sizeof($queryAr)){
				$query = "INSERT INTO ".$this->reportTable." (reportref, amount, tt, marker) VALUES ".implode(",",$queryAr);
				$this->conn->Execute($query);
			}
		}
		
		//grab data from table
		$query = "SELECT amount, tt FROM ".$this->reportTable." WHERE reportref = ? ORDER BY tt";
		$data = $this->conn->GetArray($query, array($reportref));
		$dataAr = array();
		$vamount = 0;
		foreach($data as $key=>$row){
			$datetime = explode(" ",$row["tt"]);
			$date = explode("-",$datetime[0]);
			$time = explode(":",$datetime[1]);
			if($accumulate){
				$vamount += $row["amount"];
			}else{
				$vamount = $row["amount"];
			}
			$dataAr[] = "[new Date(".$date[0].",".($date[1]-1).",".$date[2].",".$time[0]."), ".$vamount."]";
		}
		$html = "<div id='chart_".$reportref."' style='width: ".$tablewidth."; height: ".$tableheight.";'></div><script type='text/javascript'>google.load('visualization', '1', {'packages':['annotatedtimeline']});google.setOnLoadCallback(function(){var data = new google.visualization.DataTable();data.addColumn('datetime', 'DateTime');data.addColumn('number', '".$countfield."');data.addRows([".implode(",",$dataAr)."]);var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_".$reportref."'));chart.draw(data, {displayAnnotations: true, displayExactValues:true, scaleType:'allmaximized',displayAnnotationsFilter:true});});</script>";
		
		echo $html;
	}
	
	function sortBytt($a, $b){
		if($a["tt"] == $b["tt"]){
			return 0;
		}
		return $a["tt"]>$b["tt"]?1:-1;
	}
	
	function exportMysqlToCsv2($data,$filename = 'export.xlsx',$fileTile = "Noisy Crayons Campaign Report",$fileDescription = "Noisy Crayons Campaign Report")
	{

		/*
		$newdata = array();
		$newdata[] = array();
		if(sizeof($data)){
			foreach($data[0] as $key2=>$value2){
				$newdata[0][] = $key2;
			}
		}*/		
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Noisy Crayons")
							 ->setLastModifiedBy("Noisy Crayons")
							 ->setTitle($fileTile)
							 ->setSubject($fileTile)
							 ->setDescription($fileDescription)
							 ->setKeywords($fileDescription)
							 ->setCategory("report");
		$objPHPExcel->setActiveSheetIndex(0);							 
		//$objPHPExcel->getActiveSheet()->fromArray($newdata, null, 'A1');
		$objPHPExcel->getActiveSheet()->fromArray($data, null, 'A1');
		
		
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
	function exportMysqlToCsv($data,$filename = 'export.csv')
	{
		$csv_terminated = "\n";
		$csv_separator = "\t";
		$csv_enclosed = '"';
		$csv_escaped = "\\";
	 
		$schema_insert = '';
	 
		$keyArray = array();
		foreach ($data[0] as $key => $value){
			$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
				stripslashes($key)) . $csv_enclosed;
			$keyArray[] = $key;
			$schema_insert .= $l;
			$schema_insert .= $csv_separator;
		}
	 
		$out = trim(substr($schema_insert, 0, -1));
		$out .= $csv_terminated;
	 
		for($i = 0 ; $i < sizeof($data); $i++){
			$schema_insert = '';
			for($j=0; $j < sizeof($keyArray); $j++){
				if ($data[$i][$keyArray[$j]] == '0' || $data[$i][$keyArray[$j]] != '')
				{
	 
					if ($csv_enclosed == '')
					{
						$schema_insert .= $data[$i][$keyArray[$j]];
					} else
					{
						$schema_insert .= $csv_enclosed . 
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $data[$i][$keyArray[$j]]) . $csv_enclosed;
					}
				} else
				{
					$schema_insert .= '';
				}
	 
				if ($j < sizeof($keyArray) - 1)
				{
					$schema_insert .= $csv_separator;
				}
			}
			$out .= $schema_insert;
			$out .= $csv_terminated;		
		}
	 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$out = chr(255).chr(254).mb_convert_encoding( $out, 'UTF-16LE', 'UTF-8');
		header("Content-Length: " . strlen($out));
		// Output to browser with appropriate mime type, you choose ;)
		header("Content-type: text/x-csv; charset=UTF-16LE");
		//header("Content-type: text/csv");
		//header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=$filename");
		echo $out;
		exit;
	 
	}
}
?>