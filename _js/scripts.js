/*
 * Date: 2013/05/16 - 9:41 AM
 */
$(document).ready(function () {
	$(document).on('click', '.btn-row-details', function (e) {
		var $this = $(this), $table = $this.closest("table");
		var $clicked = $(e.target).closest("tr.btn-row-details");
		var active = true;

		if ($this.hasClass("active") && $clicked) active = false;

		$("tr.btn-row-details.active", $table).removeClass("active");
		if (active) {
			$this.addClass("active");
		}

		var show = $("tr.btn-row-details.active", $table).nextAll("tr.row-details");

		$("tr.row-details", $table).hide();
		if (show.length) {
			show = show[0];
			$(show).show();
		}

	});
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

	$('.scroll').smoothScroll();

	$(document).on("click", 'a.loginModal', function (e) {
		e.preventDefault();
		var $this = $(this);
		var link = $this.attr("href");

		$("#loginModal").load(link, function () {
			$(this).modal("show");
		})
	});

	$(document).on("shown", '#loginModal', function (e) {
		var $this = $(this);
		var $first_input = $this.find("input[value='']:first");
		$first_input = $first_input.length ? $first_input : $this.find("#password");
		$first_input.focus();
	});
// login page
	$(document).on("click", '#btn-forgot-password', function (e) {
		e.preventDefault();
		var $this = $(this);
		var email = $this.closest("form").find("#email").val();
		email = email ? email : "";

		var url = $this.attr("href");
		window.location = url + "&email=" + email;

	});

	$(document).on("click", '.fancybox', function () {
		$this = $(this);
		$.fancybox({
			height: '100%',
			href  : $this.attr('href'),
			width : '100%'
		});
		return false;
	});

	$(".lazyload[data-src]").each(function(){
		var $this = $(this);
//		$this.hide();
		$this.attr("src",$this.attr("data-src"))
	});

	hashChange();
	$(window).bind('hashchange', function (e) {
		hashChange();
	});

});

function hashChange() {
	var scrollTo = $.bbq.getState("scrollTo");
	if ($(scrollTo).length) {
		$.smoothScroll({
			scrollTarget: scrollTo
		});
	}
//console.log(scrollTo);

}
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