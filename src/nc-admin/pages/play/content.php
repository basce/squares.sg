<?php
  $reportManager = new reporting(array("conn"=>ncapputil::getConnection()));
?>
<section id="dashboard" class="container">
      <div class="row">
          <h3 class="subtitle">State of Play</h3>
        </div>
      <div class="row">
        <div class="contents" style="float:left;width:calc(100% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Apps Installs",
      "tablename"=>DB_USER,
      "countfield"=>"install",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"App Installation vs Time","",REPORT_INTERVAL);?>
        </div>
<!--        
        <div class="contents" style="float:left;width:calc(50% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Link Shares",
      "tablename"=>DB_FBSHARE,
      "countfield"=>"share",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"App Shares vs Time","",REPORT_INTERVAL);?>
        </div>
        -->
        <!--
        <div class="contents" style="float:left;width:calc(50% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Facebook Invite",
      "tablename"=>DB_FBINVITE,
      "countfield"=>"invite",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"Facebook Invite vs Time","",REPORT_INTERVAL);?>
        </div>
        -->
        <div class="contents" style="float:left;width:calc(100% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Referrals",
      "tablename"=>DB_REFERRAL,
      "countfield"=>"referral",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"Succesful Referral vs Time","",REPORT_INTERVAL);?>
        </div>
        <div class="contents" style="float:left;width:calc(100% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Gameplays",
      "tablename"=>gameplay_table_count_for_insight,
      "countfield"=>"gameplay",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"Gameplays vs Time","",REPORT_INTERVAL);?>
        </div>
        
         <div class="contents" style="float:left;width:calc(100% - 38px)">
        <?php $reportManager->getHighChartTimelineAndBar(
     array(
      "title"=>"Prizes distributed",
      "tablename"=>DB_WINNER,
      "countfield"=>"prize",
      "timefield"=>"tt",
      "accumulate"=>true,
      "amountfield"=>'',
      "filter"=>''
    ),"Prizes vs Time","",REPORT_INTERVAL);?>
        </div>
        
        
      <div class="clear"></div>      
    </div>
    </section>     
    
