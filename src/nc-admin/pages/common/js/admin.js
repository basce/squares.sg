/*requires nc_admin.js*/
$(document).ready(function(e) {
	nc_admin.init();
	//-------------------------------------------------
	//custom contents to init and apply behaviours
	//if there are custom buttons / queries to be made, add the behaviours here
	nc_admin.data_table.get('#appnotif_table', 1, function(css_id, pg){
		nc_admin.post('adminjson.php', {method: 'getnotification', page:pg}, function(results){
			nc_admin.data_table.update(css_id, results);
		});
	});
	//-------------------------------------------------
	//users_table();
	//-------------------------------------------------
	/*if($('#tbl_videos').size()>0){
		nc_admin.data_table.get('#tbl_videos', 1, function(css_id, pg, order, asc){
			nc_admin.post('adminjson.php', {method: 'getVideos', page:pg, order:order, asc:asc, filter:$(css_id).attr('data-filter')}, function(results){
				nc_admin.data_table.update(css_id, results);
			});
		});
	}*/
	//-------------------------------------------------
	nc_admin.data_table.get('#prize_table', 1, function(css_id, pg){
			nc_admin.post('adminjson.php', {method: 'getPrizes', page:pg}, function(results){
				nc_admin.data_table.update(css_id, results);
			});
		});
	nc_admin.data_table.get('#winner_table', 1, function(css_id, pg, order, asc){
			nc_admin.post('adminjson.php', {method: 'getWinners', page:pg, order:order, asc:asc, filter:$(css_id).attr('data-filter')}, function(results){
				nc_admin.data_table.update(css_id, results);
			});
		});
	//entries table
	//infinite scrolling
	/*
	nc_admin.gettingEntries = false;
	nc_admin.entries_page = 1;
	nc_admin.data_table.ordertype = 'tt'; //order by upload date by default
	nc_admin.data_table.get('#tbl_entries', this.entries_page, function(css_id, pg, order, asc){
		nc_admin.entries_page = 1;
		nc_admin.post('adminjson.php', {method: 'getEntries', page:pg, order:order, asc:asc, filter:$('#tbl_entries').attr('data-filter')}, function(results){
			nc_admin.data_table.update(css_id, results);
			$(window).scroll(function(e){
				if($(window).scrollTop() > $('body').height()-$(window).height()-200 && !nc_admin.gettingEntries){
					nc_admin.gettingEntries = true;
					nc_admin.entries_page++;
					nc_admin.post('adminjson.php', {method: 'getEntries', page:nc_admin.entries_page, order:order, asc:asc, filter:$('#tbl_entries').attr('data-filter')}, function(results){
						if(results.page >= nc_admin.entries_page){
							nc_admin.gettingEntries = false;
							nc_admin.data_table.update(css_id, results, null, true);
						}else{
							$('#sct_entries .foot').text('All entries loaded.');
						}
					});
				}
			});
		});
	});
	*/
	//-------------------------------------------------
});
/*

function users_table(){
	nc_admin.data_table.get('#tbl_users', 1, function(css_id, pg, order, asc){
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
						  users_table();
					  });
				  }else{
					  nc_admin.post('adminjson.php', {method:'removeWinner', uid:$(this).parents('tr').attr('data-id')}, function(){
						  alert('Winner removed');
						  users_table();
					  });
				  }
			  }
		  });
	  });
	});
}
*/