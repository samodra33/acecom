jQuery(".select2_picker").select2({
	placeholder : "Select...",
	allowClear: true,
	width : "100%",
	sorter: function(data) {
		/* Sort data using lowercase comparison */
		return data.sort(function (a, b) {
			a = a.text.toLowerCase();
			b = b.text.toLowerCase();
			if (a > b) {
				return 1;
			} else if (a < b) {
				return -1;
			}
			return 0;
		});
	}
});


function getDataWithAjax(url_, method_= 'GET', data_ = [])
{
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});

	var ajax_ = $.ajax({
		url : url_,
		method : method_,
		data : data_,
	});

	return ajax_;
}

function setSelectOption(element, selectedId, datas){

	element.html("<option value=''>Select...</option>");
	element.attr("placeholder","Select...");
	$.each(datas, function(key, val){

		var id=selectedId;
		var selected = "";

		if(id != null){
			if(id == key){
				selected = "selected";
			}
		}

		var option = "<option value='"+key+"' "+selected+">"+val+"</option>";
		element.append(option);
	});
}