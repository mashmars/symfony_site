/**
 *
 *@author mash
 *@time 2018-06-30
 *
*/
//iframe层-父子操作
function layer_open(title,width,height,url){
	layer.open({
	  type: 2,
	  title:title,
	  area: [width, height],
	  fixed: false, //不固定
	  maxmin: true,
	  content: url
	});	
}
function layer_close(){	
	//layer.closeAll();
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
	
}