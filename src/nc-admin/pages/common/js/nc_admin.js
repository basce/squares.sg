/*
nc_admin contains all the core functions necessary for the admin panel to function normally
v1.00 [2014-02-19]
------------------------------------------------------------------------------
Changelog
------------------------------------------------------------------------------
2014-02-19 - Wei Li
	- Initial version

*/
var nc_admin = {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//DETECTS AND INIT RELEVANT FUNCTIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	init: function(){
		nc_admin.graphs.init();
		if($("#appnotif_publishtime").size()==1) nc_admin.appnotification.init();
		if($('#account').size()==1) nc_admin.account.init();
		if($('#overlay').size()==1) nc_admin.overlay.init();
	},
	//====================================================================================
	post: function(e, t, n){
		$.post(e, t, function(e) {
			n(e)
		}, "json");
	},
	//====================================================================================
	users_table: function(){
		this.data_table.get('#tbl_codes', 1, function(css_id, pg, order, asc){
			nc_admin.post('adminjson.php', {method: 'getUsers', page:pg, order:order, asc:asc, filter:$(css_id).attr('data-filter')}, function(results){
				nc_admin.data_table.update(css_id, results);
				$('#tbl_users .winner_cb input').off('click').on('click', function(e){
					e.preventDefault();
					e.stopPropagation();
					
					var setWinner = $(this).prop('checked');
					
					$(this).prop({checked:!setWinner});
					if(confirm('Are you sure you want to '+((setWinner)?'set':'remove')+' this user as a winner?')){
						if(setWinner){
							nc_admin.post('adminjson.php', {method:'setWinner', uid:$(this).parents('tr').attr('data-id')}, function(){
								alert('Winner set');
								nc_admin.users_table();
							});
						}else{
							nc_admin.post('adminjson.php', {method:'removeWinner', uid:$(this).parents('tr').attr('data-id')}, function(){
								alert('Winner removed');
								nc_admin.users_table();
							});
						}
					}
				});
			});
		});
	},
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//GENERAL UI FUNCTIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	appnotification:{
		init: function(){
			$("#appnotif_publishtime").editInPlace({
					bg_over:"#666",
					field_type: "startdate",
					callback: function(unused, enteredText, original) {
						var now = new Date().getTime();
						var enter = new Date(enteredText.split(' ').join('T')).getTime();
						if(enter < now){
							alert("Invalid Publish Date.");
							return original;
						}
					}
				});
				
			$("#appnotif_submit").click(function(e){
				e.preventDefault();
				var publishtime = $("#appnotif_publishtime").text();
				var msg = $("#appnotif_msg").val();
				var target = $("#appnotif_target").val();
				if(confirm("Notification will be sent to selected target, are you confirmed? ( spamming too frequently is not recommended )")){
					nc_admin.appnotification.send(publishtime, msg, target);
				}
			});
		},
		send: function(publishtime, msg, target){
			nc_admin.post('adminjson.php', {method: 'sendnotification', publishtime:publishtime, msg:msg, target:target}, function(results){
				//clear fields
				$("#appnotif_msg").val("");
				$("#appnotif_target").val("");
				nc_admin.data_table.get('#appnotif_table', 1, function(css_id, pg){
					nc_admin.post('adminjson.php', {method: 'getnotification', page:pg}, function(results){
						nc_admin.data_table.update(css_id, results);
					});
				});
			});
		}
	},
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//USER ACCOUNT TAB
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	account:{
		init: function(){
			$('#account .add_user').click(function(){
				nc_admin.overlay.show('.add_user');
				return false;
			});
			$('#account .change_password').click(function(){
				nc_admin.overlay.show('.change_password');
				return false;
			});
		}
	},
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//DATA TABLE
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	data_table: {
		ordertype:'',
		ascending:true,
		currentpage:1,
		get_func: [],
		//make request to retrieve contents
		get: function(css_id, pg, behaviour){
			if(behaviour!=null && typeof(behaviour) === 'function'){
				this.get_func[css_id] = behaviour;
				behaviour(css_id, pg, this.ordertype, this.ascending);
			}else if(this.get_func[css_id] && typeof(this.get_func[css_id])==='function') this.get_func[css_id](css_id, pg, this.ordertype, this.ascending);
		},
		//====================================================================================
		//update contents and pagination
		update: function(css_id, data, onComplete, keep_tbody){
			//check if header got data-o
			if(!$(css_id+' thead th[data-o]').length && data.orderdata && data.orderdata.length){
				var counter = 0;
				var tempthis = this;				
				$(css_id+' thead th').each(function(index, element) {
					if(counter < data.orderdata.length && data.orderdata[counter] && data.orderdata[counter] !=""){
						$(element).addClass("active")
									.attr("data-o",data.orderdata[counter])
									.click(function(e){
										e.preventDefault();
										if(tempthis.ordertype == $(this).attr("data-o")){
											if($(this).attr("data-content") == "▲"){
												tempthis.ascending = true;
												$(this).attr("data-content","▼");
											}else{
												tempthis.ascending = false;
												$(this).attr("data-content","▲");
											}
										}else{
											tempthis.ordertype = $(this).attr("data-o");
											if(tempthis.ascending){
												$(this).attr("data-content","▼");
											}else{
												$(this).attr("data-content","▲");
											}
										}
										$(this).siblings().attr("data-content","");
										//tempthis.get(css_id, tempthis.currentpage);
										tempthis.get(css_id, 1);								
									});
					}
					counter++;
                });
			}
			
			//update contents
			var total_records = data.data.length, 
				i, j, k = 1,
				tbl_data = $(css_id+' tbody'),
				temp_record, temp_row, record_l, colspan,
				userlevel = $('body').attr('data-level');
			if(!keep_tbody) tbl_data.empty();
			
			for(i=0; i<total_records;++i){
				temp_record = data.data[i];
				record_l = temp_record.row.length;
				temp_row = $('<tr>');
				if(temp_record.css_class=='main')k++;
				temp_row.addClass((k%2==0)?'even':'odd');
				temp_row.addClass(temp_record.css_class); 
				temp_row.attr({'data-id':temp_record.id});
				for(j=0; j<record_l;j++){
					if(j==0){
					}
					colspan = (temp_record.row[j].colspan>0)?temp_record.row[j].colspan:1;
					switch(temp_record.row[j].type){
						//-------------------------------------------------
						case 'datetime':
							temp_row.append($('<td>').addClass("date_col").attr({colspan:colspan})
							  .append($('<div>').addClass('date_col')
								.append($('<span>').addClass((userlevel!=1)?'datestart':'').attr('data-o',temp_record.row[j].value)
								  .text(temp_record.row[j].startdate)
								)
								.append($('<span>').text(" - "))
								.append($('<span>').addClass((userlevel!=1)?'dateend':'').attr('data-o',temp_record.row[j].value)
								  .text(temp_record.row[j].enddate)
								)
							  )
							);
						break;
						//-------------------------------------------------
						case 'inputtext':
							temp_row.append($('<td>').attr({colspan:colspan}).append($('<div>').attr('class',(userlevel!=1)?'editme':'').attr('data-o',temp_record.row[j].value).text(temp_record.row[j].multiplier)));
						break;
						//-------------------------------------------------
						case 'text':
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).text(temp_record.row[j].value));
						break;
						//-------------------------------------------------
						case 'image':
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).append($('<img>').attr({src:temp_record.row[j].value})));
						break;
						//-------------------------------------------------
						case 'url':
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).append($('<a>').addClass('link').attr({href:temp_record.row[j].value, target:'_blank'}).text((temp_record.row[j].label)?temp_record.row[j].label:temp_record.row[j].value)));
						break;
						//-------------------------------------------------
						//for displaying a column with multiple sub records
						//will create a table
						case 'json':
							var json_table = $('<table>'), json = JSON.parse(temp_record.row[j].value), json_l = json.length, json_row;
							json_table.addClass('tbl_data_sub');
							//.......................
							//thead
							var json_head = $('<thead>');
							json_head.append($('<tr>'));
							for(var index in json[0]){
								json_head.find('tr').append($('<th>').text(index));
							}
							json_table.append(json_head);
							//.......................
							for(var l=0; l<json_l;++l){
								json_row = $('<tr>').addClass((l%2==0)?'even':'odd');
								for(var index in json[l]){
									json_row.append($('<td>').text(json[l][index]));
								}
								json_table.append(json_row);
							}
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).append(json_table));
						break;
						//-------------------------------------------------
						//for displaying a column with update options of multiple values
						case 'select':
							var select_json = JSON.parse(temp_record.row[j].value),
							select_field = $('<select>'),
							select_l = select_json.length;
							for(var l=0; l<select_l;++l){
								select_field.append(
									$('<option>').val(select_json[l].value).text(select_json[l].name).prop({selected:select_json[l].selected})
								);
							}
							temp_row.append(
								$('<td>').attr({colspan:colspan}).addClass(temp_record.row[j].css_class)
									.append(select_field)
									.append($('<button>').addClass('btn_update btn').text('Update'))
									.append($('<div>').addClass('status'))
							);
						break;
						//-------------------------------------------------
						case 'checkbox':
							temp_row.append(
								$('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan})
									.append($('<input>').attr({type:'checkbox'}).prop({checked:(temp_record.row[j].value!=1)?false:true}))
							);
						break;
						//-------------------------------------------------
						case 'html':
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).html(temp_record.row[j].value));
						break;
						//-------------------------------------------------
						case 'btn':
							temp_row.append($('<td>').addClass(temp_record.row[j].css_class).attr({colspan:colspan}).append($('<a>').attr({href:'javascript:;'}).addClass('a_btn '+temp_record.row[j].css_class).text(temp_record.row[j].value)));
						break;
						//-------------------------------------------------
					}
				}
				tbl_data.append(temp_row);
				
				$(".datestart").editInPlace({
					bg_over:"#666",
					field_type: "startdate",
					callback: function(unused, enteredText, original) {
						var goodieid = $(this).attr("data-o");
						nc_admin.post('adminjson.php', {method:'updateStartdate', goodieid:goodieid, tt:enteredText}, function(results){
							alert("Start date successfully updated");
							return results.updatedtext; 
						});
					}
				});
				
				$(".dateend").editInPlace({
					bg_over:"#666",
					field_type: "enddate",
					callback: function(unused, enteredText, original) {
						var goodieid = $(this).attr("data-o");
						nc_admin.post('adminjson.php', {method:'updateEnddate', goodieid:goodieid, tt:enteredText}, function(results){
							alert("End date successfully updated");
							return results.updatedtext; 
						});
					}
				});
				
				$(".editme").editInPlace({
					bg_over:"#666",
					callback: function(unused, enteredText, original) {
						var myRegExp = new RegExp(/^[0-9]{1}$/);
						if(!enteredText.replace(/\-/g,'').match(myRegExp)){
							alert("Please enter a single numeric");
							return original;
						} else {
							if (confirm('Confirm update multiplier?')) {
								var goodieid = $(this).attr("data-o");
								var previous_td = $(this).parent().prev();
								nc_admin.post('adminjson.php', {method:'updateMultiplier', goodieid:goodieid, tt:enteredText}, function(results){
									alert("Multiplier successfully updated");
									$(previous_td).text("1 ("+results.updatedtext+") / 1000000");
									return results.updatedtext; 
								});
							} else {
								alert("Multiplier not updated");
								return original;
							}
						}
					}
				});
				
			}
			//----------------------------------------------------------------
			//more buttons
			$(css_id+' a.more').click(function(){
				var nextRow = $(this).parents('tr.main').next('tr.details');
				$(this).text((nextRow.css('display')=='none')?'Hide':'More');
				$(this).parents('tr.main').next('tr.details').toggle();
			});
			//----------------------------------------------------------------
			//publish / unpublish buttons
			//data-eid can be changed
			$(css_id+' a.publish').click(function(){
				var tempThis = $(this);
				if(tempThis.text()=='Unpublish it'){
					nc_admin.post('adminjson.php', {method:'hidePost', eid:tempThis.parents('tr').attr('data-id')}, function(){
						tempThis.text('Publish it');
					});
				}else{
					nc_admin.post('adminjson.php', {method:'showPost', eid:tempThis.parents('tr').attr('data-id')}, function(){
						tempThis.text('Unpublish it');
					});
				}
			});
			//----------------------------------------------------------------
			//callback
			if(typeof onComplete === 'function') onComplete(data);
			//----------------------------------------------------------------
			//update pagination
			if(data.totalpage<2) return;
			var page = data.page, 
			total_page = data.totalpage, 
			records = data.data, 
			pagination_holder = $(css_id).parents('section.panel').find('.pagination');
			pagination_holder.empty();
			this.currentpage = page;			
			//.........................
			//previous page
			pagination_holder.append($('<li>').append(
				$('<a>').attr({href:'javascript:;', 'data-o':Math.max(page-1, 1), title:'Previous Page'}).addClass('').text('<'))
			);
			//.........................
			//in between pages
			if(page > 1){
				if (page <= total_page - 1) {
					for (i = page - 1; i < page + 2; i++) {
						if (i != page) {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#", 'data-o': i}).text(i))
							);
						}else{
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#"}).addClass('selected').text(i))
							);
						}
					}
				}else{
					var pg_start = total_page - 2 > 1 ? total_page - 2: 1;
					var pg_end = total_page + 1;
					for (i = pg_start; i < pg_end; i++) {
						if (i != total_page) {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#",'data-o': i}).text(i))
							);
						} else {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#"}).addClass("selected").text(i))
							);
						}
					}
				}
			}else{
				if(total_page<3){
					for (i = 1; i < total_page + 1; i++) {
						if (i != page) {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#", 'data-o': i}).text(i))
							);
						}else{
							pagination_holder.append(
								$('<li>').append(
									$('<a>').attr({href: "#"}).addClass("selected").text(i)
								)
							);
						}
					}
				}else{
					for (i = 1; i < 4; i++) {
						if (i != page) {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#",'data-o': i}).text(i))
							);
						} else {
							pagination_holder.append(
								$('<li>').append($("<a>").attr({href: "#"}).addClass("selected").text(i))
							);
						}
					}
				}
			}
			//.........................
			//next page
			pagination_holder.append($('<li>').append(
				$('<a>').attr({href:'javascript:;', 'data-o':Math.min(page+1, total_page), title:'Next Page'}).addClass('').text('>'))
			);
			//.........................
			//click functionality
			pagination_holder.find('a').click(function(){
				if(!$(this).hasClass('selected')){
					nc_admin.data_table.get(css_id, $(this).attr('data-o'));
				}
				return false;
			});
			//----------------------------------------------------------------
		}
	},
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//GRAPHS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	graphs: {
		init: function(){
			$(window).resize(function(e){
				$.each($('#holder .annotatedtimelinetable'), function(){
					$(this).find('td, div, embed').width($(this).parents('.contents').width());
				});
			});
		}
	},
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//OVERLAY
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	overlay:{
		init: function(){
			$('#overlay').click(function(e){
				if(e.target.id=='overlay'){
					nc_admin.overlay.hide();
					return false;
				}
			});
			$('#overlay .btn_close').click(function(e){
				nc_admin.overlay.hide();
				return false;
			});
		},
		//====================================================================================
		show: function(selector){
			$('#overlay .contents').css({display:'none'}).find('input[type=text], input[type=password], input[type=radio], select').val('');
			$('#overlay').css({display:'block', opacity:0}).stop().animate({opacity:1}, 300).find(selector).css({display:'inline-block'});
		},
		//====================================================================================
		hide: function(){
			$('#overlay').stop().animate({opacity:0}, 300, function(){
				$(this).css({display:'none'});
			});
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
};