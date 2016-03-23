/**
 * Este conjunto de funciones espera un objeto Tabla llamado oTable, un div con id=mensajes
 * Una estructura para los filtros (@see Templates)
 * Una estructura para los breadcrumbs (@see Templates) 
 */


    function contarResultados(){
        if(oTable.fnSettings().fnRecordsTotal() === 5000){
    		$('#mensajes').message({ type: 'warning',  frase: 'La tabla esta mostrando los &uacute;ltimos 5000 registros, para trabajar con el conjunto de datos diferente pulse el &iacute;cono "FILTRO" del men&uacute; de opciones. <br> haga click AQUI para ocultar esta notificaci&oacute;n', prefrase: 'ADVERTENCIA!!'  });
        }
    }
    function getAdvancedFilters(){
        var filters = {};
        // recorro los formularios que estan dentro de body y advancedFilterToggle
        $.each($('.advancedFilterToggle .menu_body form'), function( ){
			var formulario = $(this);
			var type = formulario.find('input[name="type"]').val();
			var op = formulario.find('input[name="op"]').val();
			var label = formulario.find('input[name="label"]').val();
			
			switch (type) {
			case 'MULTIPLE':
				// Tengo que buscar checkbox marcados, válido para options 
				var key='';
				var value = [];
				$.each(formulario.find('input:checked'), function(){
					key = $(this).attr('name');
					value.push( $(this).val() );
				});
				if(key !== ''){
					filters[key] = {label: label, op: op , values: value};
				}
				break;

			case 'DATE':
				if(op == 'GE' || op == 'LE'){
					var key = '';
					var value = null;
					var vacio = false;
					$.each(formulario.find('input[type=text]'), function(){
						key = $(this).attr('name');
						if ($(this).val() === '') {
							vacio = true ; // si algun campo es vacío ignoro el filtro
						}
						value = $(this).val();
					});
					// adicionalmente me fijo si type es DATE valido que la primer fecha sea mas antigua que la segunda
					if(type === 'DATE' && vacio === false) {
				    }
				    if(vacio === false){ 
						filters[key] = {label: label, op: op , values: value};
				    }
				}else{
					var key = '';
					var value = [];
					var vacio = false;
					$.each(formulario.find('input[type=text]'), function(){
						key = $(this).attr('name');
						if ($(this).val() === '') {
							vacio = true ; // si algun campo es vacío ignoro el filtro
						}
						value.push( $(this).val() );
					});
					// adicionalmente me fijo si type es DATE valido que la primer fecha sea mas antigua que la segunda
					if(type === 'DATE' && vacio === false) {
						var sD = value[0].split('-');
						var startDate = new Date( sD[2], sD[1]-1, sD[0] );
						var eD = value[1].split('-');
						var endDate = new Date( eD[2], eD[1]-1, eD[0] );
						if (!/Invalid|NaN/.test(startDate) && !/Invalid|NaN/.test(endDate) ) {
					        if((startDate <= endDate) == false){
						        $.jGrowl('La fecha de inicial debe ser menor o igual a la final', { header: 'Error', sticky: true, theme: 'error' });
						        vacio = true;
					        }
					    } else {
					    	$.jGrowl('fecha no válida', { header: 'Error', sticky: true, theme: 'error' });
						    vacio = true;
					    }
				    }
				    if(vacio === false){
						filters[key] = {label: label, op: op , values: value};
				    }
				}
				break;
				
				
			default:
				// busco imput de texto
				var key = '';
				var value = [];
				var vacio = false;
				$.each(formulario.find('input[type=text]'), function(){
					key = $(this).attr('name');
					if ($(this).val() === '') {
						vacio = true ; // si algun campo es vacío ignoro el filtro
					}
					value.push( $(this).val() );
				});
				// adicionalmente me fijo si type es DATE valido que la primer fecha sea mas antigua que la segunda
				if(type === 'DATE' && vacio === false) {
					var sD = value[0].split('-');
					var startDate = new Date( sD[2], sD[1]-1, sD[0] );
					var eD = value[1].split('-');
					var endDate = new Date( eD[2], eD[1]-1, eD[0] );
					if (!/Invalid|NaN/.test(startDate) && !/Invalid|NaN/.test(endDate) ) {
				        if((startDate <= endDate) == false){
					        $.jGrowl('La fecha de inicial debe ser menor o igual a la final', { header: 'Error', sticky: true, theme: 'error' });
					        vacio = true;
				        }
				    } else {
				    	$.jGrowl('fecha no válida', { header: 'Error', sticky: true, theme: 'error' });
					    vacio = true;
				    }
			    }
			    if(vacio === false){
					filters[key] = {label: label, op: op , values: value};
			    }
				
				break;
			}
        });
        return { filters: filters };
    }
	function displayFilter(){
		$('#f1').slideToggle(150);
	}   

	function clarFilters(){
		// recorro los formularios de body 
		$.each($('.advancedFilterToggle .menu_body form'), function( ){
			var formulario = $(this);
			var type = formulario.find('input[name="type"]').val();
			//var op = formulario.find('input[name="op"]').val();
			
			switch (type) {
			case 'MULTIPLE':
				// Tengo que buscar checkbox marcados y desmarcarlos
				$.each(formulario.find('input:checked'), function(){
					$(this).attr('checked', false);
					$.uniform.update($(this));
				});
				break;

			default:
				// busco imput de texto
				$.each(formulario.find('input[type=text]'), function(){
					$(this).val('');
				});					
				break;
			}				
		});
		reloadFilters();
	}
	function drawfbreadcrumbs(filters){
		if(!is_empty(filters['filters'])){
			if(!$( '#breadcrumbs-wrapper' ).is(':visible')){
				$( '#breadcrumbs-wrapper' ).show();
			}
			$('#fbreadcrumbs').html('');
	 		var append = '';
			$.each( filters['filters'] , function( key, value ) {
				append +='<li><a href="#">' + value['label'] + '</a>';
				append +='<ul>';
				/*$.each( value['values'] , function(k,v) {
						append +='<li><a href="javascript:removeSelectedFilter(\''+ key +'\', \''+ v +'\')">'+ v +'<span class="clearFilter">X</span></a></li>';	
				});*/
				if(is_array(value['values'])){
					$.each( value['values'] , function(k,v) {
						var alt = $('input[name="'+ key + '"][value="'+ v +'"]').attr('alt');
						val = alt ? alt : v;
						append +='<li><a href="javascript:removeSelectedFilter(\''+ key +'\', \''+ v +'\')">'+ val +'<span class="clearFilter">X</span></a></li>';	
					});
				}else{
					var alt = $('input[name="'+ key + '"][value="'+ value['values'] +'"]').attr('alt');
					value['values'] = alt ? alt : value['values'];
					append +='<li><a href="javascript:removeSelectedFilter(\''+ key +'\', \''+ value['values'] +'\')">'+ value['values'] +'<span class="clearFilter">X</span></a></li>';
				}
				append +='</ul>';
				append +='</li>';
				
			});
			$('#fbreadcrumbs').append(append);
			$('#fbreadcrumbs').xBreadcrumbs();
		}else{
			$( '#breadcrumbs-wrapper' ).hide();
		}
	}
	
	
	function is_array(input){
		  return typeof(input)=='object'&&(input instanceof Array);
		}

	function removeSelectedFilter(key, value){

		var elemento = $('input[name="'+ key + '"][value="'+ value +'"]');
		if($(elemento).attr('type') === 'checkbox' ||$(elemento).attr('type') === 'radio' ){
			$(elemento).attr('checked', false);
		} else {
			$('input[name="'+key+'"]').val('');
		}
		$.uniform.update($(elemento));
		reloadFilters();
	}