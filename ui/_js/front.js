(function ($) {

	$(function () {


			var $win = $(window), $body = $('body'), $nav = $('.subnav'), navHeight = $('.navbar').first().height(), subnavHeight = $('.subnav').first().height(), subnavTop = $('.subnav').length && $('.subnav').offset().top - navHeight, marginTop = parseInt($body.css('margin-top'), 10), $footer = $("#page-footer"), bodyHeight = $win.height();
			isFixed = 0;

			//console.log(subnavTop);
			processScroll();

			$win.on('scroll', processScroll);

		// fix sub nav on scroll


		function processScroll() {
			var i, scrollTop = $win.scrollTop();

			if ((bodyHeight - $footer.outerHeight()) > $footer.offset().top) {
				$footer.addClass("navbar-fixed-bottom")
			} else {
				$footer.removeClass("navbar-fixed-bottom")
			}

			if (scrollTop >= subnavTop && !isFixed) {
				isFixed = 1;
				$nav.addClass('subnav-fixed');
				$body.css('margin-top', marginTop + subnavHeight + 'px');
			} else if (scrollTop <= subnavTop && isFixed) {
				isFixed = 0;
				$nav.removeClass('subnav-fixed');
				$body.css('margin-top', marginTop + 'px');
			}
		}


	});

})(window.jQuery);


