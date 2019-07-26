<?php
$jsonGetURL = SERVER_PATH.ADMIN_FOLDER.$page["name"]."/"; // use for init and list table
$jsonPostURL = $jsonGetURL;
$ncTable = "tracking";
?>
<script type="text/javascript" src="<?=$commonfolder?>js/vendor/bootstrap/bootstrap.min.js"></script>
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
		$('html,body').animate({scrollTop:$(target).position().top},'fast');
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
		if(confirm("Are you really want to delete admin - "+data.username+"( id:"+data.aid +") ")){ //message, can customize
			$.ajax({
				type:"POST",
				url:"<?=$jsonPostURL?>",
				data:{method:"del",nctable:"<?=$ncTable?>", id:data.aid},
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
	function updateform(type, data){ // form fields customize
		if(type=="add"){
			$form.find("h1 span").text("Add New Admin");
			$form.find("h1 button").addClass("hide");
			$form.find("#username").val('');
			$form.find("#password").val('');
			$form.find("#confirmpassword").val('');
			$form.find("#accesslevel").val('');
			$form.find('input[name="aid"]').val('');
			$form.find('button[name="submit"]').val('add').text('add');
			$form.find('input[name="method"]').val('add');
			$form.find("#add").addClass("hide");
		}else{
			$form.find("h1 span").text("Edit Admin");
			$form.find("h1 button").removeClass("hide");
			$form.find("#username").val(data.username);
			$form.find("#password").val(data.password);
			$form.find("#confirmpassword").val('');
			$form.find("#accesslevel").val(data.level);
			$form.find('input[name="aid"]').val(data.aid);
			$form.find('button[name="submit"]').val('edit').text("edit");
			$form.find('input[name="method"]').val('edit');			
			$form.find("#add").removeClass("hide");
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
				$("#form1").removeClass("hide");
			}else{
				$("#form1").addClass("hide");
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
            exportfilename:"usertable",
			showColumns:true,
			search:true,
			showRefresh:true,
			showToggle:true,
			searchOnEnterKey:true,
			showExportAll:!(findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs"),
			sidePagination:"server",
			pagination:[5,10],
			cardView:findBootstrapEnvironment() == "sm" || findBootstrapEnvironment() == "xs",
			sortName:"last_date",
			sortOrder:"desc",
			showHeader:true,
			queryParams: function(params){
				return $.extend({},params,{method:"getTable", nctable:"<?=$ncTable?>"});
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
	
	$(function(){
		initTable();
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
	});

</script>