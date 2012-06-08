(function($){
    $(function(){
        $('select.paginationslb').change(function(){
            var page = $(this).val();
            var $form = $('#filterform');
            $(':input[name="p"]', $form).val(page);
            $form.submit();
        });
    });
})(jQuery);