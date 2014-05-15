
if (typeof(Dsc) === 'undefined') {
	var Dsc = {};
}

/**
 * Simple function to refresh a page.
 */
Dsc.update = function()
{
    location.reload(true);
}
    
/**
 * Resets the filters in a form.
 * 
 * @param form
 * @return
 */
Dsc.resetFormFilters = function(form)
{
    // loop through form elements
    var str = new Array();
    for(i=0; i<form.elements.length; i++)
    {
        var string = form.elements[i].name;
        if (string.substring(0,6) == 'filter')
        {
            form.elements[i].value = '';
        }
    }
    form.submit();
}
    
/**
 * 
 * @param order
 * @param dir
 * @param form
 */
Dsc.gridOrdering = function( order, dir, form ) 
{
	if (!form) {
		form = document.adminForm;
	}
     
	form.filter_order.value     = order;
	form.filter_direction.value	= dir;

	form.submit();
}
	
/**
 * 
 * @param id
 * @param change
 * @return
 */
Dsc.gridOrder = function(id, change, form) 
{
	if (!form) {
		form = document.adminForm;
	}
	
	form.id.value= id;
	form.order_change.value	= change;
	form.task.value = 'order';
	
	form.submit();
}

/**
 * Sends form values to server for validation and outputs message returned.
 * Submits form if error flag is not set in response
 * @param url
 * @param container
 * @param task
 * @param form
 * @param doModal
 * @param msg
 * @param onCompleteFunction
 */
Dsc.formValidation = function( url, container, task, form, doModal, msg, onCompleteFunction ) 
{	
	if (doModal != false) { Dsc.newModal(msg); }
	
    // loop through form elements and prepare an array of objects for passing to server
    var str = new Array();
    for(i=0; i<form.elements.length; i++)
    {
        if (form.elements[i].name) {
            postvar = {
                    name : form.elements[i].name,
                    value : form.elements[i].value,
                    checked : form.elements[i].checked,
                    id : form.elements[i].id
                };
            str[i] = postvar;
        }
    }
    
    // execute request to server
    var a = new Request({
		
        url: url,
        method:"post",
        data:{"elements":JSON.encode(str)},
        onSuccess: function(response){
            var resp = JSON.decode(response, false);
            if (resp.error != '1')
            {
                if (typeof onCompleteFunction == 'function') {
                    onCompleteFunction();
                }
                form.task.value = task;
                form.submit();
            } else {
                if (document.id(container)) { document.id(container).set( 'html', resp.msg); }
            }
            if (doModal != false) { (function() { document.body.removeChild( document.getElementById('dscModal') ); }).delay(500); }
        }
    }).send();

}

/**
 * Submits form using onsubmit if present
 * @param task
 * @return
 */
Dsc.submitForm = function(task, form)
{
	if (!form) {
		form = document.adminForm;
	}

    form.task.value = task;

    if (typeof form.onsubmit == "function") 
    {
        form.onsubmit();
    }
        else
    {
        form.submit();
    }
}

/**
 * 
 * @param {Object} divname
 * @param {Object} spanname
 * @param {Object} showtext
 * @param {Object} hidetext
 */
Dsc.displayDiv = function(divname, spanname, showtext, hidetext) { 
	var div = document.getElementById(divname);
	var span = document.getElementById(spanname);

	if (div.style.display == "none")	{
		div.style.display = "";
		span.innerHTML = hidetext;
	} else {
		div.style.display = "none";
		span.innerHTML = showtext;
	}
}

/**
 * 
 * @param {Object} prefix
 * @param {Object} newSuffix
 */
Dsc.switchDisplayDiv = function( prefix, newSuffix )
{
	var newName = prefix + newSuffix;
	var currentSuffixDiv = document.getElementById('currentSuffix');
	var currentSuffix = currentSuffixDiv.innerHTML;	
	var oldName = prefix + currentSuffix;
	var newDiv = document.getElementById(newName);
	var oldDiv = document.getElementById(oldName);

	currentSuffixDiv.innerHTML = newSuffix;
	newDiv.style.display = "";
	oldDiv.style.display = "none";
}

/**
 * 
 * @param divname
 */
Dsc.showHideDiv = function(divname)
{
	var divObject = document.getElementById(divname);
	if (divObject == null){return;}
	if (divObject.style.display == "none"){
		divObject.style.display = "";
	}
	else{
		divObject.style.display = "none";
	}
}

/**
 * 
 * @param {String} url to query
 * @param {String} document element to update after execution
 * @param {String} form name (optional)
 * @param {String} msg message for the modal div (optional)
 */
Dsc.doTask = function( url, container, form, msg, doModal, onCompleteFunction ) 
{
	if (doModal != false) { Dsc.newModal(msg); }
	
	
	// if url is present, do validation
	if (url && form) 
	{	
		// loop through form elements and prepare an array of objects for passing to server
		var str = new Array();
		for(i=0; i<form.elements.length; i++)
		{
			postvar = {
				name : form.elements[i].name,
				value : form.elements[i].value,
				checked : form.elements[i].checked,
				id : form.elements[i].id
			};
			str[i] = postvar;
		}
		
        var a = new Request({
            url: url,
            method:"post",
            data:{"elements":JSON.encode(str)},
            onSuccess: function(response){
                var resp = JSON.decode(response, false);
                if (document.id(container)) { document.id(container).set( 'html', resp.msg); }
                if (doModal != false) { (function() { document.body.removeChild( document.getElementById('dscModal') ); }).delay(500); }
                if (typeof onCompleteFunction == 'function') {
                    onCompleteFunction();
                }
                return true;
            }
        }).send();
        
	}
		else if (url && !form) 
	{
	        var a = new Request({
	            url: url,
	            method:"post",
	            onSuccess: function(response){
	                var resp = JSON.decode(response, false);
	                if (document.id(container)) { document.id(container).set( 'html', resp.msg); }
	                if (doModal != false) { (function() { document.body.removeChild( document.getElementById('dscModal') ); }).delay(500); }
	                if (typeof onCompleteFunction == 'function') {
	                    onCompleteFunction();
	                }
	                return true;
	            }
	        }).send();
	}
	return a;
}

/**
 * 
 * @param {String} msg message for the modal div (optional)
 */
Dsc.newModal = function(msg)
{
    if (typeof window.innerWidth != 'undefined') {
        var h = window.innerHeight;
        var w = window.innerWidth;
    } else {
        var h = document.documentElement.clientHeight;
        var w = document.documentElement.clientWidth;
    }
    var t = (h / 2) - 15;
    var l = (w / 2) - 15;
	var i = document.createElement('img');
	var s = window.location.toString();
	var src = Dsc.jbase + 'media/citruscart/images/ajax-loader.gif';
	i.src = src;
	i.style.position = 'absolute';
	i.style.top = t + 'px';
	i.style.left = l + 'px';
	i.style.backgroundColor = '#000000';
	i.style.zIndex = '100001';
	var d = document.createElement('div');
	d.id = 'dscModal';
	d.style.position = 'fixed';
	d.style.top = '0px';
	d.style.left = '0px';
	d.style.width = w + 'px';
	d.style.height = h + 'px';
	d.style.backgroundColor = '#000000';
	d.style.opacity = 0.5;
	d.style.filter = 'alpha(opacity=50)';
	d.style.zIndex = '100000';
	d.appendChild(i);
    if (msg != '' && msg != null) {
	    var m = document.createElement('div');
	    m.style.position = 'absolute';
	    m.style.width = '200px';
	    m.style.top = t + 50 + 'px';
	    m.style.left = (w / 2) - 100 + 'px';
	    m.style.textAlign = 'center';
	    m.style.zIndex = '100002';
	    m.style.fontSize = '1.2em';
	    m.style.color = '#ffffff';
	    m.innerHTML = msg;
	    d.appendChild(m);
	}
	document.body.appendChild(d);
}

/**
 * Submits form after clicking boolean item in lists,
 * such as a tick mark or a red x un an enabled/disabled column
 *
 * @param id
 * @param task
 * @return
 */
Dsc.listItemTask = function (id, task, form) {
    if (!form) {
        var f = document.adminForm;
    } else {
        var f = form;
    }
    
    var cb = f[id];
    if (cb) {
        for (var i = 0; true; i++) {
            var cbx = f['cb'+i];
            if (!cbx)
                break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        Dsc.submitbutton(task);
    }
    return false;
}

/**
 * Overriding core submitbutton task to perform onsubmit function
 * without submitting form afterwards
 * 
 * @param task
 * @return
 */
Dsc.submitbutton = function(task, form) 
{
    if (typeof(form) === 'undefined') {
        form = document.getElementById('adminForm');
        /**
         * Added to ensure Joomla 1.5 compatibility
         */
        if(!form){
            form = document.adminForm;
        }
    }
    
    if (typeof(task) !== 'undefined' && '' !== task) {
        form.task.value = task;
    }

    if (typeof form.onsubmit == "function") 
    {
        form.onsubmit();
    }
        else
    {
        if (typeof form.onsubmit == "function") {
            form.onsubmit();
        }
        if (typeof form.fireEvent == "function") {
            form.fireEvent('submit');
        }
        form.submit();
    }
}

/*
 * Method to get value from all form inputs and put it in an array which will be passed via AJAX request
 *
 * @param form		Form with inputs
 *
 * @return Array with all data from all inputs on the form
 */
Dsc.getFormInputData = function(form) {
	var str = new Array();
	for ( i = 0; i < form.elements.length; i++) {
		postvar = {
			name : form.elements[i].name,
			value : form.elements[i].value,
			checked : form.elements[i].checked,
			id : form.elements[i].id
		};
		str[i] = postvar;
	}
	return str;
}

/**
 * Overriding core submitbutton task to perform onsubmit function
 * without submitting form afterwards
 * 
 * @param task
 * @return
 */
function submitbutton(task) 
{
    Dsc.submitbutton(task);
}
