if (typeof(Citruscart) === 'undefined') {
    var Citruscart = {};
}

Citruscart.setupPaoFilters = function() {
    CitruscartJQ('.Citruscart-paofilter-buttons .go').on('click', function(event){
        event.preventDefault();
        CitruscartJQ('#paofilters-form').submit();
    });
    
    CitruscartJQ('.Citruscart-paofilter').on('click', function(event){
        el = CitruscartJQ(this);
        event.preventDefault();
        
        name = el.attr('data-name');        
        CitruscartJQ(".Citruscart-paofilter-options-wrapper").hide();
        CitruscartJQ(".Citruscart-paofilter-options-wrapper[data-name='" + name + "']").show();
        
    });

    CitruscartJQ('.Citruscart-paofilter-option').on('click', function(event){
        el = CitruscartJQ(this);
        event.preventDefault();
        if (el.hasClass('selected')) {
            el.removeClass('selected');
            Citruscart.removePaoFilters(el.attr('data-ids'), el.attr('data-group'));
        } else {
            el.addClass('selected');
            Citruscart.addPaoFilters(el.attr('data-ids'), el.attr('data-group'));
        }
    });
    
    CitruscartJQ('.show-all a').on('click', function(event){
        el = CitruscartJQ(this);
        event.preventDefault();
        group = el.attr('data-group');
        
        options = CitruscartJQ('.'+group+' .Citruscart-paofilter-option');
        options.each(function(){
            opt = CitruscartJQ(this);
            opt.removeClass('selected');
            Citruscart.removePaoFilters(opt.attr('data-ids'), opt.attr('data-group'));
        });
        
        if (!CitruscartJQ('#filter_pao_id-all-'+group).length) {
            CitruscartJQ('<input id="filter_pao_id-all-'+group+'" name="filter_pao_id_groups['+group+'][]" value="" type="hidden" class="filter_pao_id" />').appendTo('#paofilters-form');
        }
    });
}

Citruscart.removePaoFilters = function(ids_json, group) {
    var ids = CitruscartJQ.parseJSON( ids_json );
    CitruscartJQ.each(ids, function(index, value){
        CitruscartJQ('#filter_pao_id-'+value).remove();
    });
}

Citruscart.addPaoFilters = function(ids_json, group) {
    var ids = CitruscartJQ.parseJSON( ids_json );
    CitruscartJQ('#filter_pao_id-all-'+group).remove();
    CitruscartJQ.each(ids, function(index, value){
        if (!CitruscartJQ('#filter_pao_id-'+value).length) {
            CitruscartJQ('<input id="filter_pao_id-'+value+'" name="filter_pao_id_groups['+group+'][]" value="'+value+'" type="hidden" class="filter_pao_id" />').appendTo('#paofilters-form');
        }
    });    
}