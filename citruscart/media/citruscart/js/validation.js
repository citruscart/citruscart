CitruscartValidation = CitruscartClass.extend({
    /**
     * @memberOf CitruscartValidation
     */
    __construct: function() {
        this.defaults = {
        };
    },
    
    init: function (element, options) {
        this.__construct();
        this.element = CitruscartJQ(element);
        this.options = jQuery.extend( true, {}, this.defaults, options || {} );
        
        this.setupForm();
    },
    
    getFormElements: function(el) {
        if (el === undefined) {
            el = this.element;
        }
        var elements = el.find('*').filter(':input');
        return elements;
    },
    
    setupForm: function(el) {
        arrElements = this.getFormElements(el);
        for (i=0,len=arrElements.length; i<len; i++) {
            var formElement = CitruscartJQ(arrElements[i]);
            if (formElement.hasClass("required")) {
                formElement.data("required", true);
            }
        }
    },
    
    validateForm: function (el) {
        var validations = new Array();
        arrElements = this.getFormElements(el);
        for (i=0,len=arrElements.length; i<len; i++) {
            var fieldElement = CitruscartJQ(arrElements[i]);
            validations.push(this.validateField(fieldElement, el));
        }
        
        if (CitruscartJQ.inArray( false, validations ) != '-1') {
            return false;
        }
        return true;
    },
    
    validateField: function (fieldElement, el) {
        if (fieldElement.data('required') && this.isFieldEmpty(fieldElement, el)) {
            fieldElement.parents(".control-group").addClass("error").removeClass('warning').removeClass('info').removeClass('success');
            return false;
        } else {
            fieldElement.parents(".control-group").removeClass('error').removeClass('warning').removeClass('info').removeClass('success');
            return true;
        }
    },
    
    isFieldEmpty: function (field, el) {
        if (el === undefined) {
            el = this.element;
        }
        var type = field.attr('type');
        switch(type) {
            case "radio":
                var val = el.find('input:radio[name='+ field.attr('name') +']:checked').val();
                if (!val) {
                    return true;
                }
                break;
            default:
                if (field.val() === null || !field.val()) {
                    return true;
                }
                break;
        }

        return false;
    }
});


/**
 * Overriding core submitbutton task to perform our onsubmit function
 * without submitting form afterwards
 * for J1.6+
 * 
 * On any pages that require ajax form validation, add this:
 * JHTML::_('script', 'validation.js', 'media/citruscart/js/');
 * 
 * @param task
 * @return
 */
Joomla.submitform = function(task, form) {
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

    // Submit the form.
    if (typeof form.onsubmit == 'function') {
        form.onsubmit();
    }
    if (typeof form.fireEvent == 'function') {
        form.fireEvent('submit');
    }

};