// JavaScript Document
function CvPHP(){
	this.post = function(url,data,callback){
		layer.open({type: 2});
        $.ajax({
            type: 'POST',
            async: true,
            data: data,
            url: url,
            dataType:'json',
            success: function(d,state){
            	layer.closeAll();
            	if(state!='success'){
            		cvphp.msg({content:'请求数据出错'});
            	}else{
            		callback(d);
            	}
            },
            error: function(e){
            	layer.closeAll();
                cvphp.msg({content:"发起请求出错" + e});
            }
        });
	},
	this.mpost = function(url,data,callback){
        $.ajax({
            type: 'POST',
            async: true,
            data: data,
            url: url,
            dataType:'json',
            success: function(d,state){
            	if(state!='success'){
            		cvphp.msg({content:'请求数据出错'});
            	}else{
            		callback(d);
            	}
            },
            error: function(e){
                cvphp.msg({content:"发起请求出错" + e});
            }
        });
	},	
	this.get = function(url,data,callback){
		layer.open({type: 2});
        $.ajax({
            type: 'GET',
            async: true,
            data: data,
            url: url,
            dataType:'json',
            success: function(d,state){
            	layer.closeAll();
            	if(state!='success'){
            		cvphp.msg({content:"请求数据出错"});
            	}else{
            		callback(d);
            	}
            },
            error: function(e){
            	layer.closeAll();
                cvphp.msg({content:"发起请求出错" + e});
            }
        });
	},
	this.submit = function(obj,callback){
		layer.open({type: 2});
		$(obj).ajaxSubmit({
			type: $(obj).attr('method'),
			success:function(data){
				layer.closeAll();
				callback(data);
			}
		});
		//$(obj).resetForm();
	},
	this.msg = function(par){
		var time = 3;
		if($.inArray(time,par) != -1) time = par.time;
		layer.open({
			content: par.content,
			skin: 'msg',
			shadeClose: false,
			yes: function(index){
				if($.inArray(yes,par) != -1){
					var yesFun = par.yes;
					yesFun(index);
				}
			},
			no: function(index){
				if($.inArray(no,par) != -1){
					var noFun = par.no;
					noFun(index);
				}
			},
			time: time
		});
	},
	this.ismobile = function(number){
		number = number||'';
		if(number.length != 11) return false;
	    if(!(/^1[34578]\d{9}$/.test(number)))
		    return false;
	    else
	    	return true;
	},
	this.getmoney = function(Float_Val){
		Float_Val = parseFloat(Float_Val);
		if(Float_Val > Float_Val.toFixed(2)){
			Float_Val = parseFloat(Float_Val.toFixed(2) + 0.01);
		}else{
			Float_Val = parseFloat(Float_Val.toFixed(2));
		}
		var xsd = Float_Val.toString().split(".");
		if(xsd.length==1){
			Float_Val = Float_Val.toString()+".00";
			return parseFloat(Float_Val).toFixed(2);
		}
		if(xsd.length>1){
			if(xsd[1].length<2){
				Float_Val = Float_Val.toString()+"0";
			}
			return parseFloat(Float_Val).toFixed(2);
		}
	}
	

}

cvphp = new CvPHP();
