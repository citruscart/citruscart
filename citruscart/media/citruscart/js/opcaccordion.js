CitruscartOpcAccordion = CitruscartClass.extend({
    /**
     * @memberOf CitruscartOpcAccordion
     */
    __construct: function() {
        this.defaults = {
            clickableEntity: '.opc-section-title', 
            checkAllow: true
        };
        this.disallowAccessToNextSections = true;
        this.currentSection = false;
    },
    
    init: function (element, options) {
        this.__construct();
        this.element = citruscartJQ(element);
        this.options = jQuery.extend( true, {}, this.defaults, options || {} );

        this.checkAllow = this.options.checkAllow;
        this.sections = citruscartJQ(element + ' .opc-section');        
        var headers = citruscartJQ(element + ' .opc-section ' + this.options.clickableEntity);

        var self = this;
        headers.each(function() {
            citruscartJQ(this).click(function(event){
                self.sectionClicked(event);
            });
        });
    },

    sectionClicked: function(event) {
        event.preventDefault();
        section_id = citruscartJQ(event.target).closest('.opc-section').attr('id');
        this.openSection(section_id);
        event.stopPropagation();
    },

    openSection: function(section) {
        var sectionObj = citruscartJQ('#'+section);

        if (this.checkAllow && !sectionObj.hasClass('allow')) {
            return;
        }

        if(sectionObj.attr('id') != this.currentSection) {
            this.closeExistingSection();
            this.currentSection = sectionObj.attr('id');
            citruscartJQ('#' + this.currentSection).addClass('active').removeClass('past');
            var contents = citruscartJQ('.opc-section-body', sectionObj);
            contents.show();
            
            if (this.disallowAccessToNextSections) {
                var pastCurrentSection = false;
                for (var i=0; i<this.sections.length; i++) {
                    if (pastCurrentSection) {
                        citruscartJQ(this.sections[i]).removeClass('allow').removeClass('past');
                    } else {
                        citruscartJQ(this.sections[i]).addClass('past');
                    }
                    if (citruscartJQ(this.sections[i]).attr('id') == sectionObj.attr('id')) {
                        pastCurrentSection = true;
                    }
                }
            }
        }
    },

    closeSection: function(section) {
        var sectionObj = citruscartJQ('#'+section);
        sectionObj.removeClass('active');
        var body = citruscartJQ('.opc-section-body', sectionObj);
        body.hide();
    },

    openNextSection: function(setAllow){
        for (section in this.sections) {
            var nextIndex = parseInt(section)+1;
            if (this.sections[section].attr('id') == this.currentSection && this.sections[nextIndex]){
                if (setAllow) {
                    citruscartJQ(this.sections[nextIndex]).addClass('allow');
                }
                this.openSection(this.sections[nextIndex]);
                return;
            }
        }
    },

    openPrevSection: function(setAllow){
        for (section in this.sections) {
            var prevIndex = parseInt(section)-1;
            if (this.sections[section].attr('id') == this.currentSection && this.sections[prevIndex]){
                if (setAllow) {
                    citruscartJQ(this.sections[prevIndex]).addClass('allow');
                }
                this.openSection(this.sections[prevIndex]);
                return;
            }
        }
    },

    closeExistingSection: function() {
        if(this.currentSection) {
            this.closeSection(this.currentSection);
        }
    }
});