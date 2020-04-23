YUI.add('moodle-availability_payallways-form', function (Y, NAME) {

M.availability_payallways = M.availability_payallways || {};
 
M.availability_payallways.form = Y.Object(M.core_availability.plugin);

M.availability_payallways.form.initInner = function(the_options) {
	if(the_options.cm != null) {
		this.cm = the_options.cm;
	}
	if(the_options.access_cost != 0) {
		this.access_cost = the_options.access_cost;
	} else {
		this.access_cost = 0;
	}
	if(the_options.section != null) {
		this.section = the_options.section;
	}	
	this.course_id = the_options.course_id;
	this.access_options = the_options.options;
	this.headline = the_options.headline;
	this.is_section = the_options.is_section;
	console.log(this);
};
 
M.availability_payallways.form.getNode = function(json) {
    title = M.util.get_string('title', 'availability_payallways');
    access_cost = M.util.get_string('access_cost', 'availability_payallways');
    
	html = '<label class="form-group"><span class="p-r-1">' + this.headline + '</span></label>';
		
	//price text input
	html += '<br /><span class="availability-payallways">' + access_cost + ' <input type="text" name="access_cost" /> UAH</span>';
    	
    var node = Y.Node.create('<span>' + html + '</span>');
	 
    // Set initial values 
	if (json.creating === undefined) {
		
		if (json.access_cost !== undefined) {
            node.one('input[name=access_cost]').set('value', json.access_cost);
        } else if (json.access_cost === undefined) {
            node.one('input[name=access_cost]').set('value', '0');
        }
    }
 
	// Add event handlers (first time only).
 
    if (!M.availability_payallways.form.addedEvents) {
        M.availability_payallways.form.addedEvents = true;
        var root = Y.one('.availability-field');
        
		root.delegate('change', function() {
            M.core_availability.form.update();
        }, '.availability_payallways input');
    }
 
    return node;
};
 
M.availability_payallways.form.fillValue = function(value, node) {
    // This function gets passed the node (from above) and a value
    // object. Within that object, it must set up the correct values
    // to use within the JSON data in the form. Should be compatible
    // with the structure used in the __construct and save functions
    // within condition.php.
    value.is_paid = 1;
    value.cm = this.cm;
	value.access_cost = node.one('input[name=access_cost]').get('value').trim();
	value.course_id = this.course_id;
	value.section = this.section;
	value.is_section = this.is_section;
	
};
 
M.availability_payallways.form.fillErrors = function(errors, node) {
    // If the user has selected something invalid, this optional
    // function can be included to report an error in the form. The
    // error will show immediately as a 'Please set' tag, and if the
    // user saves the form with an error still in place, they'll see
    // the actual error text.
	var value = {};
    this.fillValue(value, node);
	
	if(isNaN(value.access_cost) || parseInt(value.access_cost) <= 0) {
		errors.push('availability_payallways:cost_error');
	}
};

}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
