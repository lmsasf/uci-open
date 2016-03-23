function postTestimonial( ocwTitleEncode ) {
	var form = $('<form>').attr({
        name	:'testimonialform', 
        method	: 'post', 
        action	: '/testimonials/post'
    });
    $('<input>').attr({
         type: 'hidden',
         id: 'ocwTitleEncode',
         name: 'ocwTitleEncode',
         value: ocwTitleEncode
     }).appendTo(form);
     form.appendTo($('body'));
     form.submit();	
	
}