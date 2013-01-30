//信息发布时间友好格式化
var friendlyDate = function(posttime) {
	if(posttime!=null && posttime!=undefined && posttime.toString().length==10) posttime = posttime * 1000;
	var returnStr = '';
	var noteStr = '';
	var span = '';
	var da = new Date();
	sda = new Date(posttime);
	var currtime = da.getTime();
	var time = 0;
	if (posttime > currtime) {
		time = posttime - currtime;
		sda = da;
	} else {
		time = currtime - posttime;
	}
	if (time < 1000){
		returnStr = "刚刚";
		span = 'redspan';
	}
	time = parseInt(time / 1000);
	if (time > 86400) {
		var day = parseInt(time / (24 * 60 * 60));
		if (day == 1) {
			returnStr = "昨天";//(" + sda.getHours() + ":" + sda.getMinutes() + ")";
//			returnStr = "昨天(" + sda.getHours() + ":" + sda.getMinutes() + ")";
		} else if (day < 30) {
			returnStr = day + "天前";
		} else if (day < 360) {
			var moth = parseInt(day / 30);
			returnStr = moth + "个月前";
		} else {
			var year = parseInt(day / 360);
			returnStr = year + "年前";
		}
	} else if (time > 3600) {
		var hour = parseInt(time / (60 * 60));
		noteStr = hour + "小时前";
		returnStr = "今天";
		span = 'redspan';
	} else if (time > 60) {
		var hour = parseInt(time / 60);
		returnStr = "今天";
		noteStr = hour + "分钟前";
		span = 'redspan';
	} else {
		noteStr = time + "秒前";
		returnStr = "今天";
		span = 'redspan';
	}
	
	document.write('<span class="'+span+'" title="'+noteStr+'">'+returnStr+'</span>');
};