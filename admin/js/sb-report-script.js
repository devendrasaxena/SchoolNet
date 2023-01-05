const report_helper_url = 'report-helper.php';

  
var store = [];
function plusTree(t){
    store = [];

            var list_html = "";
            $(t).text($(t).text()=='+'?'-':'+');
            var type = $(t).attr('data-type');
            var id = $(t).attr('data-id');
            var payload = {
                type,input:id,
                state: jQuery('#center_id').val(),
                district: jQuery('#district_id').val(),
                tehsil: jQuery('#tehsil_id').val(),

            };
        
            if(store.indexOf(type+'-'+id) == -1){
                $.post(report_helper_url,payload,res=>{
                    if(!res.err){
                        console.log(res);
                            store.push(type+'-'+id);
                            for(d in res.data){
                                var tmpObj = res.data[d];
                            list_html += '<tr>\
                                        <td style="width:16.66%;padding-left: 10px;">\
                                        <button onclick="plusTreeTehsil(this)" class="tree-btn" data-type="district" data-id="'+tmpObj.district_id+'">+</button> '+tmpObj.district_name+' \
                                    </td>\
                                    <td style="width:16.66% ">-</td>\
                                    <td style="width:16.66%">-</td>\
                                    <td style="width:16.66%">-</td>\
                                    <td style="width:16.66%">'+tmpObj.studentReg+'</td>\
                                    <td style="width:16.66%">'+tmpObj.teacherReg+'</td>\
                                </tr>';


                                list_html += '<tr style="display: none;" class="district-child-'+tmpObj.district_id+'">\
                <td class="nav nav-list tree" colspan="6" style="padding-top: 0; ">\
                        <table class="table">\
                        \
                            <tbody>\
                                \
                            </tbody>\
                        </table>\
                    </td>\
            </tr>';  
                            }

                    $('.'+type+'-child-'+id+' tbody').html(list_html); 
                    $('.'+type+'-child-'+id).toggle(300);      
                    }
                    
                });
            }else{
                $('.'+type+'-child-'+id).toggle(300);     
            }


        
    }



function plusTreeTehsil(t){
        var list_html = "";
    $(t).text($(t).text()=='+'?'-':'+');
    var type = $(t).attr('data-type');
    var id = $(t).attr('data-id');
    store = [];

    var payload = {
        type,input:id,
        state: jQuery('#center_id').val(),
        district: jQuery('#district_id').val(),
        tehsil: jQuery('#tehsil_id').val(),

    };

    if(store.indexOf(type+'-'+id) == -1){
        $.post(report_helper_url,payload,res=>{
            //alert(JSON.stringify(res));
            if(!res.err){
                    store.push(type+'-'+id);
                    for(d in res.data){
                        var tmpObj = res.data[d];
                    list_html += '<tr>\
                                <td style="padding-left: 15px;">'+tmpObj.tehsil_name+' \
                                \
                            </td>\
                            <td style="width:16.66%">-</td>\
                            <td style="width:16.66%">-</td>\
                            <td style="width:16.66%">-</td>\
                            <td style="width:16.66%">'+tmpObj.studentReg+'</td>\
                            <td style="width:16.66%">'+tmpObj.teacherReg+'</td>\
                        </tr>';
                    }

            $('.'+type+'-child-'+id+' tbody').html(list_html); 
            $('.'+type+'-child-'+id).toggle(300);      
            }
            
        });
    }else{
        $('.'+type+'-child-'+id).toggle(300);     
    }

}


/*

function plusTreeClass(t){
    store = [];

        var list_html = "";
    $(t).text($(t).text()=='+'?'-':'+');
    var type = $(t).attr('data-type');
    var id = $(t).attr('data-id');
    

    if(store.indexOf(type+'-'+id) == -1){
        $.get(report_helper_url+'?type='+type+'&input='+id,res=>{
            alert(JSON.stringify(res));
            if(!res.err){
                    store.push(type+'-'+id);
                    for(d in res.data){
                        var tmpObj = res.data[d];
                    list_html += '<tr>\
                                <td>\
                                '+tmpObj.tehsil_name+' \
                            </td>\
                            <td>\
                                '+tmpObj.created_date+' \
                            </td>\
                        </tr>';
                    }

            $('.'+type+'-child-'+id+' tbody').html(list_html); 
            $('.'+type+'-child-'+id).toggle(300);      
            }
            
        });
    }else{
        $('.'+type+'-child-'+id).toggle(300);     
    }



}*/









          
function setState(t) {

	var options = '<option value="" selected disabled>Select State</option>';
	jQuery('#center_id').html(options);
	jQuery('#district_id').html('<option value="" selected disabled>Select District</option>');
	jQuery('#tehsil_id').html('<option value="" selected disabled>Select Tehsil</option>');

    var centre_id = jQuery(t).val();
   
	$.get(report_helper_url+'?type=centre&input='+centre_id,res=>{
		 options += '<option value="0">All</option>';
		if(!res.err){
			for(i in res.data){
				var tmpObj = res.data[i];
				options += '<option value="'+tmpObj.center_id+'">'+tmpObj.name+'</option>';
				
			}

			jQuery('#center_id').html(options);
		}
			
	})
}
          
function setDistrict(t) {

	var options = '<option value="" selected disabled>Select District</option>';
	jQuery('#district_id').html(options);
	jQuery('#tehsil_id').html('<option value="" selected disabled>Select Tehsil</option>');

    var state_id = jQuery(t).val();
    var payload = {type:'state',input:state_id};
	$.post(report_helper_url,payload,res=>{
		 options += '<option value="0">All</option>';
		if(!res.err){
			for(i in res.data){
				var tmpObj = res.data[i];
				options += '<option value="'+tmpObj.district_id+'">'+tmpObj.district_name+'</option>';
				
			}

			jQuery('#district_id').html(options);
		}
			
	})
}

function setTehsil(t) {
	var options = '<option value="" selected disabled>Select Tehsil</option>';
	jQuery('#tehsil_id').html(options);
	var state = jQuery('#center_id').val();
	var district_id = jQuery(t).val();
	var payload = {type:'district',input:district_id,state};
	$.post(report_helper_url,payload,res=>{
		
		 options += '<option value="0">All</option>';
		if(!res.err){
			for(i in res.data){
				var tmpObj = res.data[i];
				options += '<option value="'+tmpObj.tehsil_id+'">'+tmpObj.tehsil_name+'</option>';
				
			}

			jQuery('#tehsil_id').html(options);
		}
			
	})
}


