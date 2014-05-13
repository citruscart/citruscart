if (typeof(Citruscart) === 'undefined') {
    var Citruscart = {};
}

Citruscart.setupPaoFilters = function() {
    citruscartJQ('.citruscart-paofilter-buttons .go').on('click', function(event){
        event.preventDefault();
        citruscartJQ('#paofilters-form').submit();
    });
    
    citruscartJQ('.citruscart-paofilter').on('click', function(event){
        el = citruscartJQ(this);
        event.preventDefault();
        
        name = el.attr('data-name');        
        citruscartJQ(".citruscart-paofilter-options-wrapper").hide();
        citruscartJQ(".citruscart-paofilter-options-wrapper[data-name='" + name + "']").show();
        
    });

    citruscartJQ('.citruscart-paofilter-option').on('click', function(event){
        el = citruscartJQ(this);
        event.preventDefault();
        if (el.hasClass('selected')) {
            el.removeClass('selected');
            Citruscart.removePaoFilters(el.attr('data-ids'), el.attr('data-group'));
        } else {
            el.addClass('selected');
            Citruscart.addPaoFilters(el.attr('data-ids'), el.attr('data-group'));
        }
    });
    
    citruscartJQ('.show-all a').on('click', function(event){
        el = citruscartJQ(this);
        event.preventDefault();
        group = el.attr('data-group');
        
        options = citruscartJQ('.'+group+' .citruscart-paofilter-option');
        options.each(function(){
            opt = citruscartJQ(this);
            opt.removeClass('selected');
            Citruscart.removePaoFilters(opt.attr('data-ids'), opt.attr('data-group'));
        });
        
        if (!citruscartJQ('#filter_pao_id-all-'+group).length) {
            citruscartJQ('<input id="filter_pao_id-all-'+group+'" name="filter_pao_id_groups['+group+'][]" value="" type="hidden" class="filter_pao_id" />').appendTo('#paofilters-form');
        }
    });
}

Citruscart.removePaoFilters = function(ids_json, group) {
    var ids = citruscartJQ.parseJSON( ids_json );
    citruscartJQ.each(ids, function(index, value){
        citruscartJQ('#filter_pao_id-'+value).remove();
    });
}

Citruscart.addPaoFilters = function(ids_json, group) {
    var ids = citruscartJQ.parseJSON( ids_json );
    citruscartJQ('#filter_pao_id-all-'+group).remove();
    citruscartJQ.each(ids, function(index, value){
        if (!citruscartJQ('#filter_pao_id-'+value).length) {
            citruscartJQ('<input id="filter_pao_id-'+value+'" name="filter_pao_id_groups['+group+'][]" value="'+value+'" type="hidden" class="filter_pao_id" />').appendTo('#paofilters-form');
        }
    });    
}