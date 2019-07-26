<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $jsonGetURL;
$ncTable = "redeemlog";
$idLabel = "id";
?>

<script src="<?=$ownfolder?>js/bootstrap.min.js"></script>
<script src="<?=$ownfolder?>js/bootstrap-table.js"></script>
<script src="<?=$ownfolder?>js/bootstrap-table-export.js"></script>
<script src="<?=$ownfolder?>js/tableExport.js"></script>
<script src="<?=$ownfolder?>js/bootstrap-table-editable.js"></script>
<script src="<?=$ownfolder?>js/bootstrap-editable.js"></script>
<script>
    var $table = $('#table'),
        $remove = $('#remove'),
        selections = [],
		$form = $("#form1");
	function scrollto(target){
		$('html,body').animate({scrollTop:$(target).position().top - $(".herobanner ").outerHeight()},'fast');
	}
	function cdisplayMessage(t,d){
		t.find('.error_container').empty();
		if(d){
			var status = d.success ? "success":"danger";
			t.find('.error_container').append(
				$("<div>").addClass("alert alert-"+status)
					.text(d.msg)
			);
		}
	}
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
	function deleteItem(data){
		if(confirm("Are you really want to delete admin - "+data.username+"( id:"+data.id +") ")){ //message, can customize
			$.ajax({
				type:"POST",
				url:"<?=$jsonPostURL?>",
				data:{method:"del",nctable:"<?=$ncTable?>", id:data.id},
				dataType:'json',
				timeout:5000,
				success:function(d){
					displayMessage(d);
					$table.bootstrapTable("refresh");
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
		}else{
			//do nothing
		}
	}
	
	
	function createActionButton(ar){
		var a = '';
		$.each(ar, function(index, value){
			switch(value){
				case "edit":
					a += '<a class="edit" href="javascript:void(0)" title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>'					
				break;
				case "delete":
					a += '<a class="remove" href="javascript:void(0)" title="Remove"><i class="glyphicon glyphicon-remove"></i></a>'
				break;
			}
		});
		return a;
	};

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
	
	function createTable(data){
		var columndata = [];
		if(data.bulkactions && data.bulkactions.length){
			columndata.push( {
                        field: 'state',
                        checkbox: true,
                        align: 'center',
                        valign: 'middle'
                    });
		}
		columndata = $.merge(columndata, data.fields);
		if(data.actions && data.actions.length){
			/*
			return [
            '<a class="like" href="javascript:void(0)" title="Like">',
            '<i class="glyphicon glyphicon-heart"></i>',
            '</a>  ',
            '<a class="remove" href="javascript:void(0)" title="Remove">',
            '<i class="glyphicon glyphicon-remove"></i>',
            '</a>'
        ].join('');
			*/
			if($.inArray("add", data.actions) !== -1){
				$("#form1, #add").removeClass("hide");
			}else{
				$("#form1, #add").addClass("hide");
			}
			columndata.push( {
                        field: 'operate',
                        title: 'Actions',
                        align: 'center',
						valign: 'middle',
                        events: {
							'click .edit': function (e, value, row, index) {
								//update 
								updateform("edit",row);
								displayMessage();
								scrollto('#form1');
							},
							'click .remove': function (e, value, row, index) {
								deleteItem(row);
							}
						},
                        formatter: function(value, row, index){
							return createActionButton(data.actions);
						}
                    });
		}
		$table.bootstrapTable({
            height: getHeight(),
            columns: [
                columndata
            ],
            url:"<?=$jsonGetURL?>",
            exportfilename:"prizetable",
			showColumns:true,
			search:true,
			showRefresh:true,
			showToggle:true,
			searchOnEnterKey:true,
			showExportAll:!(findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs"),
			sidePagination:"server",
			pagination:[5,10],
			cardView:findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs",
			sortName:"<?=$idLabel?>",
			sortOrder:"desc",
			showHeader:true,
			queryParams: function(params){
				return $.extend({},params,{method:"getTable", nctable:"<?=$ncTable?>", wid:$("#redeem_wid").val()});
			}
        });
        // sometimes footer render error.
        setTimeout(function () {
            $table.bootstrapTable('resetView');
        }, 200);

	}
    function initTable() {
		
		$.ajax({
				type:"GET",
				url:'<?=$jsonGetURL?>',
				data:{method:"init",nctable:"<?=$ncTable?>"},
				dataType:'json',
				timeout:5000,
				success:function(d){
					createTable(d);
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
		return;
		
        
        $table.on('check.bs.table uncheck.bs.table ' +
                'check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);

            // save your data, here just save the current page
            selections = getIdSelections();
            // push or splice the selections if you want to save all data selections
        });
        $table.on('expand-row.bs.table', function (e, index, row, $detail) {
            $detail.html('Loading from ajax request...');
        });
        $table.on('all.bs.table', function (e, name, args) {
            console.log(name, args);
        });
        $remove.click(function () {
            var ids = getIdSelections();
            $table.bootstrapTable('remove', {
                field: 'id',
                values: ids
            });
            $remove.prop('disabled', true);
        });
        $(window).resize(function () {
            $table.bootstrapTable('resetView', {
                height: getHeight()
            });
        });
    }

    function getIdSelections() {
        return $.map($table.bootstrapTable('getSelections'), function (row) {
            return row.id
        });
    }

    function responseHandler(res) {
        $.each(res.rows, function (i, row) {
            row.state = $.inArray(row.id, selections) !== -1;
        });
        return res;
    }

    function detailFormatter(index, row) {
        var html = [];
        $.each(row, function (key, value) {
            html.push('<p><b>' + key + ':</b> ' + value + '</p>');
        });
        return html.join('');
    }

    function operateFormatter(value, row, index) {
        return [
            '<a class="like" href="javascript:void(0)" title="Like">',
            '<i class="glyphicon glyphicon-heart"></i>',
            '</a>  ',
            '<a class="remove" href="javascript:void(0)" title="Remove">',
            '<i class="glyphicon glyphicon-remove"></i>',
            '</a>'
        ].join('');
    }

    function totalTextFormatter(data) {
        return 'Total';
    }

    function totalNameFormatter(data) {
        return data.length;
    }

    function totalPriceFormatter(data) {
        var total = 0;
        $.each(data, function (i, row) {
            total += +(row.price.substring(1));
        });
        return '$' + total;
    }

    function getHeight() {
        var h = $(window).height() - $('h1').outerHeight(true);
		return h > 400 ? h : 400;
    }

    function getRedeemHistory(wid){
		$.ajax({
			type:"POST",
			url:'<?=$jsonPostURL?>',
			data:{

			},
			dataType:'json',
			timeout:5000,
			success:function(d){
				displayMessage(d);
				$table.bootstrapTable("refresh");
			},
			error:function(request,status,err){
				console.log(status);
			}
		});
    }

    function searchByCode(){
    	$.ajax({
				type:"POST",
				url:'<?=$jsonPostURL?>',
				data:{
					method:"searchByCode",
					code:$("#code").val()
				},
				dataType:'json',
				timeout:5000,
				success:function(d){
					if(d){
						var ta = "#redemption_form ",
							a = d;
						$(ta + "#profile_image img").attr({src:'https://graph.facebook.com/'+a.fbid+'/picture?width=200&height=200', width:200, height:200});

						$(ta+ "#winner_name span").text(a.username);
						$(ta+ "#winner_email span").text(a.email);
						$(ta+ "#winning_code span").text(a.code);
						$(ta+ "#prize_id span").text(a.goodieid);
						$(ta+ "#prize_won span").text(a.prizename);
						$(ta+ "#won_time span").text(a.tt);
						$(ta+ "#redeem_status span").text(parseInt(a.redeem,10)?"Redeemed":"Active");


						if(parseInt(a.redeem, 10)){
							$(ta+ "#redeem_status span").removeClass("active").addClass("redeemed");
							$(ta+ "#location span").text(a.location);
							$(ta+ "#location span").removeClass("hide");
							$(ta+ "#location_select").addClass("hide");
							$(ta+ "button[value='redeem']").addClass("hide");
							$(ta+ "button[value='unredeem']").removeClass("hide");
						}else{
							$(ta+ "#redeem_status span").addClass("active").removeClass("redeemed");
							$(ta + "#location_select").val(a.locationid);
							$(ta+ "#location span").addClass("hide");
							$(ta+ "#location_select").removeClass("hide");
							$(ta+ "button[value='redeem']").removeClass("hide");
							$(ta+ "button[value='unredeem']").addClass("hide");
						}

						$("#redeem_wid").val(a.wid);

						$("#redemption_form").removeClass("hide");
						$("#redeemlogtable").removeClass("hide");
						$table.bootstrapTable("refresh");
					}else{
						cdisplayMessage($("#search_form"),{
							status:false,
							msg:"No matches found on the code: "+$("#code").val()
						});
					}
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
    }

	$(function(){
		initTable();
		$("#search_form form").submit(function(e){
			e.preventDefault();
			searchByCode();
		});

		$("#redemption_form button[value='redeem']").click(function(e){
			e.preventDefault();
			var data = {
				method:"redeem",
				locationid:$("#location_select_force").length?$("#location_select_force").val():$("#location_select").val(),
				wid:$("#redeem_wid").val()
			};
			if(!data.locationid){
				cdisplayMessage($("#redemption_form"),{
					status:false,
					msg:"please select a location"
				});
				return;
			}
			$.ajax({
				type:"POST",
				url:'<?=$jsonPostURL?>',
				data:data,
				dataType:'json',
				timeout:5000,
				success:function(d){
					cdisplayMessage($("#redemption_form"),d);
					searchByCode();
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
		});

		$("#redemption_form button[value='unredeem']").click(function(e){
			e.preventDefault();
			console.log(data);
			var data = {
				method:"unredeem",
				wid:$("#redeem_wid").val()
			};
			$.ajax({
				type:"POST",
				url:'<?=$jsonPostURL?>',
				data:data,
				dataType:'json',
				timeout:5000,
				success:function(d){
					cdisplayMessage($("#redemption_form"),d);
					searchByCode();
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
		});

		$("#redemption_form form").submit(function(e){
			e.preventDefault();
		});

		/*
		$form.find('form').submit(function(e) {
			e.preventDefault();
            var serializedata = $(this).serializeArray();
			var data = {};
			$.each(serializedata, function(index, value){
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
					$table.bootstrapTable("refresh");
				},
				error:function(request,status,err){
					console.log(status);
				}
			});
        });
		$("#add").click(function(e){
			e.preventDefault();
			displayMessage();
			updateform("add");
		});
		*/
	});

</script>