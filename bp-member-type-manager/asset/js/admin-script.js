(function($){

    $(document).ready(function(){
        $('.bmtm-wrap').find('#member-type-name').focusout(function(){
            var data = {
                action: 'bmtm_make_slug',
                name: $(this).val()
            }
            $.post(ajaxurl, data, function(slug){
                $('.bmtm-wrap').find('#member-type-slug').val(slug);
            })
        });
    })

})(jQuery);
