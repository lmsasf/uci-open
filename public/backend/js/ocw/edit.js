function saveOCW(data){
   $.ajax( {
        url: "/admin/ocw/save",
        data: data ,
        dataType: "json",global: true, cache:false,
        ifModified: false, processData:true,
        success: function(datos)
        {
        	//$.jGrowl('Data saved successfully', { header: 'Success', sticky: false, theme: 'success' });
        	$(".loader").show();
        	window.setTimeout(function() {
        		$(".loader").hide();
        		displayMessage( 'success', 'Data saved successfully', 'Success', '' );
        	}, 2000);
        },
        error: function(request, status, error)
        {
            mensaje = request.responseText != '' ? request.responseText : 'Timed out or interrupted session, you may be having problems in the network' ;
            //$.jGrowl(mensaje, { header: 'Error', sticky: true, theme: 'error' });
            displayMessage( 'error', mensaje, 'Error', '' );
        },
        timeout: 20000,
        type: "POST"
    } );
}

function getList(elementoID){
	var lista	= new Array();
	$('#'+ elementoID + ' li').each(function (index) {
		var stringId 	= $(this).attr('id')
		var arrayId 	= stringId.split('_');
		lista.push({ id : parseInt(arrayId[1]), order: index });
	});
	return lista;
}

function removeAuthor(id, text){
	$('#listAuthors').append('<option value="'+id+'">'+ text +'</option>');
	$("#listAuthors").trigger("liszt:updated");

	var idx = idauthors.indexOf(id); // Localizamos el indice del elemento en array
	idauthors.splice(idx, 1);
}

function addAuthor(){
	var itemselected = $('#listAuthors option:selected');
	var id = $("#listAuthors option:selected").attr("id");
	var n=id.split(",");
	var divcheck = document.createElement("div");
	divcheck.setAttribute("style","float:left;");
	var check = null;
	var label = null;
	var muestracheck = false;
	var id_persona = itemselected.val();
	for(i=0;i<n.length;i++){
		if(n[i] != ""){

			var pos=n[i].indexOf("_");
			var nombre = n[i].substr(0,pos);
			var ideg = n[i].substr(pos+1);
			//alert(ideg);
			var checkbox = document.createElement('input');
			var idper = document.createElement('input');
			checkbox.type = "checkbox";
			checkbox.name = "idDeg";
			checkbox.value = ideg; //nombre del grado
			checkbox.id = id_persona+'_'+ideg;
			checkbox.setAttribute("style","float:left;  margin-top: 5px;  margin-right: 5px;");
			var label = document.createElement('label');
			label.setAttribute("style","float:left; margin-right: 10px;");
			label.htmlFor = ideg;
			label.appendChild(document.createTextNode(nombre));
			//checkbox.appendChild(document.createTextNode(n[i]));
			divcheck.appendChild(checkbox);
			divcheck.appendChild(label);
			muestracheck = true;
		}
	}
	if(itemselected.val() !== '') {
		if(idauthors.length > 0){
			for(i=0; i<idauthors.length; i++){
				if(idauthors[i] == itemselected.val()){
					//$.jGrowl( 'Plese, select another author, this already exists', { header: 'Error', sticky: false, theme: 'error' } );return;
			        mensaje = 'Plese, select another author, this already exists';
			        displayMessage( 'error', mensaje, 'Error', '' );
			        return;
				}
			}
		}

		var li = $('<li class="ui-state-default dd-item dd3-item" id="Author_' + itemselected.val() + '"></li>');
		var licat = $('<div class="dd-handle dd3-handle"></div>');
		var liLeft = $('<div class="liLeft"style="margin-left:30px;" title="Drag and Drop to order items"><div style="float:left;margin-right:25px;">' + itemselected.text() + '</div></div>');

		if(muestracheck == true){
			liLeft.append(divcheck);
		}

		var liRight = $('<div class="liRight"></div>');
		var button = $('<a class="smallButton buttonLi" title="remove Author" href="javascript:removeAuthor('+ itemselected.val() +', \'' + itemselected.text() + '\' )"><img alt="" src="/backend/images/icons/color/cross.png"></a>');
		var clear= $('<div class="clear"></div>');
		idauthors.push(itemselected.val());
		button.on('click',function(){
			$(this).parents('li').remove();
		});
		liRight.append(button);
		li.append(licat).append(liLeft).append(liRight).append(clear);
		$('#sortable-author').append(li);
		itemselected.remove();
		$("#listAuthors").trigger("liszt:updated");

	}else{
		//$.jGrowl( 'Plese, select an option', { header: 'Error', sticky: false, theme: 'error' } );
        mensaje = 'Plese, select an option';
        displayMessage( 'error', mensaje, 'Error', '' );
	}
}

function addCategory(){

	var itemselected = $('#listCategories option:selected');

	if(itemselected.val() !== '') {
		if(idcats.length > 0){
			for(i=0; i<idcats.length; i++){
				if(idcats[i] == itemselected.val()){
					//$.jGrowl( 'Plese, select another category, this already exists', { header: 'Error', sticky: false, theme: 'error' } );return;
					mensaje = 'Plese, select another category, this already exists';
					displayMessage( 'error', mensaje, 'Error', '' );
					return;
				}
			}
		}
		var li = $('<li class="ui-state-default dd-item dd3-item" id="Category_' + itemselected.val() + '"></li>');
		var licat = $('<div class="dd-handle dd3-handle"></div>');
		var liLeft = $('<div class="liLeft" style="margin-left:30px;" title="Drag and Drop to order items">' + itemselected.attr('title') + '</div>');
		var liRight = $('<div class="liRight"></div>');
		var button = $('<a class="smallButton buttonLi" title="remove category" href="javascript:;"><img alt="" src="/backend/images/icons/color/cross.png"></a>');
		var clear= $('<div class="clear"></div>');
		idcats.push(itemselected.val());
		//console.debug(idcats);
		button.on('click',function(){
			$(this).parents('li').remove();
		});
		liRight.append(button);
		li.append(licat).append(liLeft).append(liRight).append(clear);
		$('#sortable-categories').append(li);
		//itemselected.remove();
		//$("#listCategories").trigger("liszt:updated");

	}else{
		//$.jGrowl( 'Plese, select an option', { header: 'Error', sticky: false, theme: 'error' } );
		mensaje = 'Plese, select an option';
		displayMessage( 'error', mensaje, 'Error', '' );
	}
}

function viewoption(){
	var muestra = document.getElementById("extradata").style.display;
	if(muestra == 'none'){
		document.getElementById("extradata").style.display='block';
		document.getElementById("imagendown").className  = "aimgdown";
	}else{
		document.getElementById("extradata").style.display='none';
		document.getElementById("imagendown").className  = "aimgup";
	}

}

function toggleCont( divToToggle , aTag) {
     $('#'+ divToToggle).toggle();
     $('#'+ aTag).children().toggle();
    //  if ($('#'+ divToToggle).css('display') == 'none') {
    //       $('#'+ aTag).html('Show more &#9660');
    //  }
    //  else {
    //       $('#'+ aTag).html('Show less &#9650');
    //  }
}
function cancel(){
	window.location = '/admin/ocw';
}
