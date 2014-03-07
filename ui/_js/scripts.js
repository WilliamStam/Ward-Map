/*
 * Date: 2013/05/16 - 9:41 AM
 */
$(document).ready(function () {
	
	$('body').tooltip({
		selector : '*[rel=tooltip]',
		live     : true,
		container: 'body'

	}).popover({
			selector : '*[rel=popover]',
			offset   : 5,
			live     : true,
			container: 'body',
			html     : true
		});

	

	

});

function file_size(size) {
	if (!size) {
		return 0;
	}
	var origSize = size;
	var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	var i = 0;
	while (size >= 1024) {
		size /= 1024;
		++i;
	}

	if (origSize > 1024) {
		size = size.toFixed(1)
	}
	return size + ' ' + units[i];
}

function updatetimerlist(d, page_size) {
	//d = jQuery.parseJSON(d);

	if (!d || !typeof d == 'object') {
		return false;
	}

	var data = d['timer'];
	var page = d['page'];
	var models = d['models'];

//console.log(models)

	var pageSize = (page && page['size']) ? page['size'] : page_size;

	if (data) {
		var highlight = "";
		if (page['time'] > 0.5)    highlight = 'style="color: red;"';

		var th = '<tr class="heading" style="background-color: #fdf5ce;"><td >' + page['page'] + ' : <span class="g">Size: ' + file_size(pageSize) + '</span></td><td class="s g"' + highlight + '>' + page['time'] + '</td></tr>', thm;
		if (models) {
			thm = $("#template-timers-tr-models").jqote(models, "*");
		} else {
			thm = "";
		}
		//console.log(thm)

		$("#systemTimers").prepend(th + $("#template-timers-tr").jqote(data, "*") + thm);

		// console.log($("#systemTimers").prepend(th + $("#template-timers-tr").jqote(data, "*")));
	}

}
function quote_to_single(str){
	str = str.replace(/"/g,"'");

	return str;
}