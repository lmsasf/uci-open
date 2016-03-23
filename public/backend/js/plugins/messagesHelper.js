(function($){
    $.fn.extend({
        //plugin name - animatemenu
        message: function(options) {
 
            var defaults = {
                type: '',
                frase: '',
                prefrase: ''
            };
             
            var options = $.extend(defaults, options);
         
            return this.each(function() {
                  var o = options;
                  var obj = $(this); 
                  var prefrase;
                  
                  obj.click(function(e){
                    var elemento= $(this).find('.hideit');
                    elemento.fadeTo(200, 0.00, function(){ //fade
                        elemento.slideUp(300, function() { //slide up
                            elemento.remove(); //then remove from the DOM
                        });
                    });
                  });
                  switch( o.type )
                    {
                    case 'info': 
                        prefrase = o.prefrase === '' ? 'INFO' : o.prefrase;
                        obj.append('<div class="nNote nInformation hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;
                    case 'success': 
                        prefrase = o.prefrase === '' ? 'INFO' : o.prefrase;
                        obj.append('<div class="nNote nSuccess hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;   
                    case 'error': 
                        prefrase = o.prefrase === '' ? 'ERROR' : o.prefrase;
                        obj.append('<div class="nNote nFailure hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;     
                    case 'warning': 
                        prefrase = o.prefrase === '' ? 'ATENCION!' : o.prefrase;
                        obj.append('<div class="nNote nWarning hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;       
                    case 'idea': 
                        prefrase = o.prefrase === '' ? 'IDEA' : o.prefrase;
                        obj.append('<div class="nNote nLightbulb hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;   
                    case 'email': 
                        prefrase = o.prefrase === '' ? 'MENSAJE' : o.prefrase;
                        obj.append('<div class="nNote nMessages hideit"><p><strong>'+ prefrase +': </strong>'+ o.frase +'</p></div> ');
                        break;                     
                    default:
                        alert('No hay clase definida para el tipo de mensaje "' + o.type + '"');
                    }
            });
        }
    });
})(jQuery);
