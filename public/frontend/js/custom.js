// JavaScript Document

<!-- Floating Navigation Bar Script -->
$(function() {
                // Stick the #nav to the top of the window
                var nav = $('#nav');
                var navHomeY = nav.offset().top;
                var isFixed = false;
                var $w = $(window);
                $w.scroll(function() {
                                var scrollTop = $w.scrollTop();
                                var shouldBeFixed = scrollTop > navHomeY;
                                if (shouldBeFixed && !isFixed) {
                                                nav.css({
                                                                position: 'fixed',
                                                                top: 0,
                                                                left: nav.offset().left,
                                                                width: nav.width()
                                                });
                                                isFixed = true;
                                }
                                else if (!shouldBeFixed && isFixed)
                                {
                                                nav.css({
                                                                position: 'static'
                                                });
                                                isFixed = false;
                                }
                });
});
                
$(document).ready(function() {
  // override default options (also overrides global overrides)
  $('div.span7.expander').expander({
    slicePoint:                      1300,  // default is 100
  });
  
  $('div.span4.expander').expander({
    slicePoint:                     722,  // default is 100
  });

});

<!-- For OCW Blog -->
$(document).ready(function () {
                  $('#ocw-blog').rssfeed('http://sites.uci.edu/opencourseware/feed/', {
                                limit: 3,
                                header: false
});
});
