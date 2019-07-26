// JavaScript Document
function nc_peh_patient(settings){
	var wound, treatment, treatmentArray,
	    defaults = {
			numberOfWoundType:9,
			numberOfWound:3,
			conflicts:[[2,6],[3,4,5],[0,1],[7,8]],
			treatmentsDone:function(){alert("done")}
		};
	settings = $.extend(true, {}, defaults, settings);
	
	function shuffle(o){ //v1.0
		for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	};
	
	function generateWound(){
		var a = [], b, c;
		b = Array.apply(null, {length:settings.numberOfWoundType}).map(Number.call, Number);
		while(a.length < settings.numberOfWound){
			b = shuffle(b);
			c = b.pop();
			a.push(c);
			
			b = $.grep(b, function(value){
				return value != c;
			});
			if(settings.conflicts.length){
				$.each(settings.conflicts, function(index, value){
					if($.inArray(c, value) !== -1){
						//number appear in conflict list. remove the sibling number too
						$.each(value, function(index2, value2){
							b = $.grep(b, function(value3){
								return value3 != value2;
							});
						});
					}
				});
			}
			if(a.length < settings.numberOfWound && b.length == 0){
				throw new RangeError("wound type is less than number of wound to be generated.");
			}
		}
		return a;
	}
	
	function insertTreatment(woundtype, treatmenttype, pos, success){
		treatmentArray.push({
			wt:woundtype,
			tt:treatmenttype,
			p:pos,
			s:success,
			t:new Date().getTime()
		});
		
	}
	this.getWound = function(){
		return wound;
	}
	this.getTreatmentData = function(){
		return treatmentArray;
	}
	this.getTreatment = function(){
		return treatment;
	}
	this.resetPatient = function(){
		wound = generateWound();
		treatment = Array.apply(null,wound).map(Number.prototype.valueOf, -1);
		treatmentArray = [];
	}
	this.curePatientAuto = function(treatmenttype){
		var a = $.inArray(treatmenttype, wound);
		if(a !== -1){
			//match
			if(treatment[a] == -1){
				insertTreatment(-1, treatmenttype, a, true);
				treatment[a] = treatmenttype;
				if($.inArray(-1, treatment) == -1){
					//all treatment done
					if(settings.treatmentsDone)settings.treatmentsDone();
				}
				return 1;
			}else{
				insertTreatment(-1, treatmenttype, -1, false);
				return 2;
			}
		}else{
			insertTreatment(-1, treatmenttype, -1, false);
			return 0;
		}
	}
	this.curePatient = function(treatmenttype, pos){
		if(treatment[pos] == -1){
			insertTreatment(wound[pos], treatmenttype, pos, wound[pos] == treatmenttype);
			if(wound[pos] == treatmenttype){
				treatment[pos] = treatmenttype;
				if($.inArray(-1, treatment) == -1){
					//all treatment done
					settings.treatmentsDone();
				}
				return 1;				
			}else{
				//failed
				return 0;
			}
		}else{
			return 2;
		}
	}
	this.isWound = function(type){
		var a = $.inArray(type, wound);
		return (a !== -1 )&& (treatment[a] == -1)
	}
}

function nc_clockTimer(){
	var begintime, interval, currenttime, maxcount, currentcount, onCounter, onComplete, looping, forcestop;
	
	looping = false;
	forcestop = false;
	function loopCounting(){
		if(forcestop){
			looping = false;
			forcestop = false;
			return;
		}
		if(looping)return;
		looping = true;
		currenttime = new Date().getTime();
		var tempcount = ((currenttime - begintime) / 1000 ) >> 0 ;
		if(tempcount == maxcount){
			//complete
			if(onComplete)onComplete();
		}else{
			
			if(tempcount != currentcount){
				currentcount = tempcount;
				if(onCounter)onCounter(currentcount);
			}
			setTimeout(function(){looping=false;loopCounting()}, 200);
			
		}
	}
	
	this.start = function(f_maxcount, f_onComplete, f_onCounter){
		forcestop = false;
		looping = false;
		begintime = new Date().getTime();
		maxcount = f_maxcount;
		currentcount = 0;
		onCounter = f_onCounter;
		onComplete = f_onComplete;
		loopCounting();
	}
	
	this.stop = function(){
		forcestop = true;
	}
}

function nc_peh_char(settings){
	var char, charpos, sequence, playing,
		defaults = {
			offsetY:20,
			offsetX:10,
			startPos:-210,
			endPos:390,
			charSelector:"#gamechar",
			onAnimationComplete:null
		};
	
	settings = $.extend(true, {}, defaults, settings);
	
	char = $(settings.charSelector);
	sequence = [];
	playing = false;
	function updateCharPos(){
		char.css({bottom:charpos.y+20, left:charpos.x});
	}
	
	function playingAction(){
		if(playing) return;
		playloop();
	}
	
	function playloop(){
		if(!sequence.length){
			playing = false;
			if(settings.onAnimationComplete)settings.onAnimationComplete();
			return;
		}
		playing = true;
		var cursequence = sequence.shift();
		var a,d, duration;
		d = Math.abs(charpos.x - cursequence.x);
		if(cursequence.slow){
			duration = d/150;
		}else{
			duration = d/300;
		}
		setPos(cursequence.x, duration);
		setTimeout(playloop,duration*1000);
	}
	function setPos(x, duration){
		//calculate position
		TweenMax.to(charpos, duration, {x:x, onUpdateParams:charpos, onUpdate:function(){
			var c;
			c = Math.abs(20*Math.sin(Math.PI*(((charpos.x+settings.offsetX)%50)/50)));
			
			charpos.y = c;
			updateCharPos();
		}, ease:Linear.easeNone});
	}
	
	function setClass(base, additional){
		char.find("."+base).removeClass().addClass(base+" "+additional);
	}
	
	function addSequence(obj, onComplete){
		sequence.push(obj);
		settings.onAnimationComplete = onComplete;
		playingAction();
	}
	this.setCharIndex = function(index){
		char.removeClass().addClass("char char"+index);
	}
	this.enterClinic = function(onComplete){
		addSequence({x:190, slow:true}, onComplete);
	}
	this.leaveClinic = function(onComplete){
		addSequence({x:590}, onComplete);
	}
	this.setPatientStat = function(injuredar, curear){
		if($.inArray(-1,curear) === -1){
			//show happy face
			setClass("cface","happy");
		}else{
			//show sad face
			setClass("cface","");
		}
		
		$.each(injuredar, function(index, value){
			if($.inArray(value,curear) !== -1){
				//cure
				switch(value){
					case 0:
						setClass('chead',"headcured");
					break;
					case 1:
						setClass('chead',"facecured");
					break;
					case 2:
						setClass('cshoulder',"cured");
					break;
					case 3:
						setClass('clefthand',"elbowcured");
					break;
					case 4:
						setClass('clefthand',"fingercured");
					break;
					case 5:
						setClass('clefthand',"wristcured");
					break;
					case 6:
						setClass('crighthand',"armcured");
					break;
					case 7:
						setClass('cleftfoot',"cured");
					break;
					case 8:
						setClass('crightfoot',"cured");
					break;
				}
			}else{
				//injured
				switch(value){
					case 0:
						setClass('chead',"headinjured");
					break;
					case 1:
						setClass('chead',"faceinjured");
					break;
					case 2:
						setClass('cshoulder',"injured");
					break;
					case 3:
						setClass('clefthand',"elbowinjured");
					break;
					case 4:
						setClass('clefthand',"fingerinjured");
					break;
					case 5:
						setClass('clefthand',"wristinjured");
					break;
					case 6:
						setClass('crighthand',"arminjured");
					break;
					case 7:
						setClass('cleftfoot',"injured");
					break;
					case 8:
						setClass('crightfoot',"injured");
					break;
				}
			}
		});
		
	}
	this.setClass = function(classname){
		char.removeClass().addClass(classname);
	}
	this.reset = function(){
		charpos = { x: settings.startPos, y : settings.offsetY };
		updateCharPos();
		setClass("chead","");
		setClass("cface","");
		setClass("clefthand","");
		setClass("crighthand","");
		setClass("cshoulder","");
		setClass("cleftfoot","");
		setClass("crightfoot","");
	}
}

function nc_peh_patient_queue(){
	var patients;
	
	//setup patients
	patients = [
			{
				type:parseInt(Math.random()*5,10)+1,
				x:0,
				y:0
			},
			{
				type:parseInt(Math.random()*5,10)+1,
				x:0,
				y:0
			},
			{
				type:parseInt(Math.random()*5,10)+1,
				x:0,
				y:0
			}
				];
	function shuffle(o){ //v1.0
		for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	};
	
	function setPos(index, x, duration){
		//calculate position
		var charpos = patients[index];
		if(charpos.x < -10){ charpos.x = 260; duration:1.2; } //force to 210
		TweenMax.to(charpos, duration, {x:x, onUpdateParams:charpos, onUpdate:function(){
			var c;
			c = Math.abs(5*Math.sin(Math.PI*(((charpos.x-10)%25)/25)));
			
			charpos.y = c;
			patients[index] = charpos;
			updateCharPos();
		}, ease:Linear.easeNone});
		if(x <= -10){
			//back to the queue behind
			setTimeout(function(){
				patients[index] = {
					type:parseInt(Math.random()*5,10)+1,
					x:260,
					y:3
					};
				updateCharPos();
				setTimeout(function(){
					setPos(index, 160, 1.2);
				},700);
			},duration*1000);
		}
	}
	
	function updateCharPos(){
		$(".queue .pat").each(function(index, element) {
            var a = patients[index];
			$(element).removeClass().addClass("sprite pat pat"+a.type).css({right:a.x, bottom:a.y+3});
        });
	}

	function resetPatients(){
		patients = [
					{
						type:parseInt(Math.random()*5,10)+1,
						x:60,
						y:3
					},
					{
						type:parseInt(Math.random()*5,10)+1,
						x:110,
						y:3
					},
					{
						type:parseInt(Math.random()*5,10)+1,
						x:160,
						y:3
					},
				];
		updateCharPos();
	}
	
	function SortByX(a,b){
		return a.x > b.x ? 1 : -1;
	}
	this.init = function(){
		resetPatients();
	}
	this.next = function(){
		var duration = 2, delay;
		delay = 0;
		patients.sort(SortByX);
		$.each(patients, function(index, value){
			delay += 100*Math.random()+100;
			setTimeout(function(){
				if(value.x <= 60){
					setPos(index, value.x-100, 1.2);
				}else{
					setPos(index, value.x-50, 0.6);
				}
			},index*delay);
		});
	}
}

function nc_peh_clinic(settings){
	var patient, char, timer, clinicrecord, queue,
	    defaults = {
			numberOfWoundType:9,
			numberOfWound:3,
			conflicts:[],
			duration:60,
			onTimerUpdate:null,
			onTimerDone:null,
			onGameEnd:null,
			onGameStart:null,
			treatmentsDone:null
		};
	
	settings = $.extend(true, {}, defaults, settings);
	
	patient = new nc_peh_patient(settings);
	char = new nc_peh_char();
	timer = new nc_clockTimer();
	queue = new nc_peh_patient_queue();
	
	function disablepanel(bl){
		$(".room").droppable({disabled:bl});
	}
	
	function newpatient(){
		patient.resetPatient();
		char.reset();
		char.setCharIndex(parseInt(Math.random()*6+1,10));
		char.setPatientStat(patient.getWound(), patient.getTreatment());
		disablepanel(true);
		char.enterClinic(function(){disablepanel(false)});
	}
	
	function getTreatmentType(objClass){
		switch(true){
			case (objClass.indexOf("bandage1") !== -1):
				return 0;
			break;
			case (objClass.indexOf("bandage2") !== -1):
				return 1;
			break;
			case (objClass.indexOf("bandage3") !== -1):
				return 2;
			break;
			case (objClass.indexOf("bandage4") !== -1):
				return 3;
			break;
			case (objClass.indexOf("bandage5") !== -1):
				return 4;
			break;
			case (objClass.indexOf("bandage6") !== -1):
				return 5;
			break;
			case (objClass.indexOf("bandage7") !== -1):
				return 6;
			break;
			case (objClass.indexOf("bandage8") !== -1):
				return 7;
			break;
			case (objClass.indexOf("bandage9") !== -1):
				return 8;
			break;
		}
	}
	
	function getRect(obj){
		return{
			x:obj.offset().left -$(".room").offset().left,
			y:obj.offset().top -$(".room").offset().top,
			width:obj.width(),
			height:obj.height()
		}
	}
	
	function isHit(dropObj, injuredPos){
		//get injured position
		var a;
		switch(injuredPos){
			case 0:
			case 1:
				a = getRect($(".chead"));
			break;
			case 2:
				a = getRect($(".cshoulder"));
			break;
			case 3:
			case 4:
			case 5:
				a = getRect($(".clefthand"));
			break;
			case 6:
				a = getRect($(".crighthand"));
			break;
			case 7:
				a = getRect($(".cleftfoot"));
			break;
			case 8:
				a = getRect($(".crightfoot"));
			break;
		}
		return !(dropObj.x > a.x + a.width || dropObj.x + dropObj.width < a.x || dropObj.y > a.y + a.height || dropObj.y + dropObj.height < a.y )
	}
	
	function displayWrong(helper){
		var a,b;
		a = getRect(helper);
		b = $("<div>").addClass("uncorrectmark").css({
			top:a.y - 10,
			left:a.x + a.width/2
		}).appendTo($(".room"));
		
		TweenMax.to(b[0], 0.7, {top:a.y - 60, autoAlpha:0, scale:2, onCompleteParams:b, onComplete:function(){
			$(b).remove();
		}, ease:Linear.easeNone});
	}
	
	function displayCorrect(ttype){
		var a;
		switch(ttype){
			case 0:
			case 1:
				a = getRect($(".chead"));
			break;
			case 2:
				a = getRect($(".cshoulder"));
			break;
			case 3:
			case 4:
			case 5:
				a = getRect($(".clefthand"));
			break;
			case 6:
				a = getRect($(".crighthand"));
			break;
			case 7:
				a = getRect($(".cleftfoot"));
			break;
			case 8:
				a = getRect($(".crightfoot"));
			break;
		}
		var b = $("<div>").addClass("correctmark").css({
			top:a.y - 10,
			left:a.x + a.width/2
		}).appendTo($(".room"));
		
		TweenMax.to(b[0], 0.7, {top:a.y - 60, autoAlpha:0, scale:2,onCompleteParams:b, onComplete:function(){
			$(b).remove();
		}, ease:Linear.easeNone});
	}
	
	function onDrop(event, ui){
		 var ttype= getTreatmentType(ui.draggable.attr("class"));
		 if(patient.isWound(ttype)){
			 // correct treatment
			 //check if position correct
			var helper = {
				 y:ui.helper.offset().top - $(".room").offset().top,
				 x:ui.helper.offset().left - $(".room").offset().left,
				 width:ui.helper.width(),
				 height:ui.helper.height()
			 	};
			 if(isHit(helper, ttype)){
				 patient.curePatientAuto(ttype);
				 char.setPatientStat(patient.getWound(), patient.getTreatment());
				 displayCorrect(ttype);
				 if($.inArray(-1, patient.getTreatment()) === -1){
					 //finish
					 disablepanel(true);
					 clinicrecord.push(patient.getTreatmentData());
					 setTimeout(function(){
						 char.leaveClinic(newpatient);
					 },500);
					 setTimeout(function(){
						 queue.next();
					 },1000);
				 }
			 }else{
				 displayWrong(ui.helper);
			 }
		 }else{
			 displayWrong(ui.helper);
		 }
	}
	
	this.init = function(){
		$(".bandage").draggable({
			helper: function(){
				return $(this).clone().css({background:"none",color:"#000","text-align":"center"}).append($("<span>").text($(this).attr("title")).addClass("name"));
			},
			cursorAt: { top:42, left:42}
		});
		
		$(".room").droppable({
			accept:".bandage",
			activeClass:"ready",
			hoverClass:"ondragover",
			drop: onDrop
		});
		
		queue.init();
	}
	
	this.getData = function(){
		return clinicrecord;
	}
	this.gameStart = function(){
		disablepanel(false);
		clinicrecord = [];
		queue.init();
		newpatient();
		timer.start(settings.duration, settings.onTimerDone, settings.onTimerUpdate);
	}
}