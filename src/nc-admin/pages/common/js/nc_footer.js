// JavaScript Document
// Requires: jQuery v1.7+, basceFB.js
if(typeof __ncfooter === "undefined"){
	__ncfooter = {
		createSocialDiscovery:function(selector, endingtext, users){
			$(selector).empty();
			if(users && users.total > 7 ){
				var friendcnt = $('<div>').addClass('fans_img');
				for(var i = 0; i < 5; i++){
					friendcnt.append(__ncfooter._SocialDiscoveryPP(users.data[i]));
				}
				// "and "+(__ss.appuser.total - __ss.appuser.data.length)+" others
				$(selector).append(friendcnt)
							.append($('<p>').attr("style","line-height: 25px;margin: 0;padding: 0;")
											.append("and ")
											.append(
												$("<a>").attr({href:"#"})
														.text((users.total - 5)+" others")
														.ncfbidDialog({csslink:"../common/sc.css",data:(users.data).splice(5, users.data.length-5), dialogtitle:"Application Users"})
											)
											.append(document.createTextNode(" "+endingtext))
									)
							.append($('<div>').addClass('clear'));
			}
		},
		_SocialDiscoveryPP:function(data){
			return $('<div>').addClass('pp')
							.css('float','left')
							.css('padding-right',5)
							.append($('<a>').attr({target:"_blank", href:"https://www.facebook.com/profile.php?id="+data.fbid})
											.append($('<img>').attr({width:25,height:25,title:data.name,src:"https://graph.facebook.com/"+data.fbid+"/picture?type=square"})
													)
									);
		}
	};
}


	$(document).ready(function(){
		var ncfooterpreviousy = 0;
		$(".tnc_container, .policy_container, .nc-bubble").hide();
		$('#tnc_expand').click(function(e){
			e.preventDefault();
			if($(".tnc_container").is(':hidden')){
				if(typeof __basceFB !== "undefined"){__basceFB.scrollToAnimate(e.pageY,100)};
				$(".policy_container, .nc-bubble").hide();
			}
			$(".tnc_container").toggle();
		});
		$('#policy_expand').click(function(e){
			e.preventDefault();
			if($(".policy_container").is(':hidden')){
				if(typeof __basceFB !== "undefined"){__basceFB.scrollToAnimate(e.pageY,100)};
				$(".tnc_container, .nc-bubble").hide();
			}
			$(".policy_container").toggle();
			/*
			$(".policy_container").slideToggle('fast',function(){
				if(!$(".policy_container").is(':hidden')){
					if(typeof __basceFB !== "undefined"){__basceFB.scrollTo(e.pageY)}
				}
			});
			*/
		});
		/*
		$('#nc_icon').one('mouseover', function(e){
			if($(".nc-bubble").is(':hidden')){
				if(typeof __basceFB !== "undefined"){__basceFB.scrollTo(e.pageY)}
				$(".tnc_container, .policy_container").hide();
			}
			$(".nc-bubble").toggle();
		});
		*/	
		$('#nc_icon').click(function(e){
			e.preventDefault();
			if($(".nc-bubble").is(':hidden')){
				if(typeof __basceFB !== "undefined"){__basceFB.scrollToAnimate(e.pageY,100)}
				$(".tnc_container, .policy_container").hide();
			}
			$(".nc-bubble").toggle();
		});
	});