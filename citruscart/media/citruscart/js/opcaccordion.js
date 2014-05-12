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
        this.element = CitruscartJQ(element);
        this.options = jQuery.extend( true, {}, this.defaults, options || {} );

        this.checkAllow = this.options.checkAllow;
        this.sections = CitruscartJQ(element + ' .opc-section');        
        var headers = CitruscartJQ(element + ' .opc-section ' + this.options.clickableEntity);

        var self = this;
        headers.each(function() {
            CitruscartJQ(this).click(function(event){
                self.sectionClicked(event);
            });
        });
    },

    sectionClicked: function(event) {
        event.preventDefault();
        section_id = CitruscartJQ(event.target).closest('.opc-section').attr('id');
        this.openSection(section_id);
        event.stopPropagation();
    },

    openSection: function(section) {
        var sectionObj = CitruscartJQ('#'+section);

        if (this.checkAllow && !sectionObj.hasClass('allow')) {
            return;
        }

        if(sectionObj.attr('id') != this.currentSection) {
            this.closeExistingSection();
            this.currentSection = sectionObj.attr('id');
            CitruscartJQ('#' + this.currentSection).addClass('active').removeClass('past');
            var contents = CitruscartJQ('.opc-section-body', sectionObj);
            contents.show();
            
            if (this.disallowAccessToNextSections) {
                var pastCurrentSection = false;
                for (var i=0; i<this.sections.length; i++) {
                    if (pastCurrentSection) {
                        CitruscartJQ(this.sections[i]).removeClass('allow').removeClass('past');
                    } else {
                        CitruscartJQ(this.sections[i]).addClass('past');
                    }
                    if (CitruscartJQ(this.sections[i]).attr('id') == sectionObj.attr('id')) {
                        pastCurrentSection = true;
                    }
                }
            }
        }
    },

    closeSection: function(section) {
        var sectionObj = CitruscartJQ('#'+section);
        sectionObj.removeClass('active');
        var body = CitruscartJQ('.opc-section-body', sectionObj);
        body.hide();
    },

    openNextSection: function(setAllow){
        for (section in this.sections) {
            var nextIndex = parseInt(section)+1;
            if (this.sections[section].attr('id') == this.currentSection && this.sections[nextIndex]){
                if (setAllow) {
                    CitruscartJQ(this.sections[nextIndex]).addClass('allow');
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
                    CitruscartJQ(this.sections[prevIndex]).addClass('allow');
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