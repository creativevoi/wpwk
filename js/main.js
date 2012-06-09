(function($){
    _wk.container = $('#wkform');
    _wk.currentForm = window.location.hash.substring(1);
    _wk.init = function() {
        if (_wk.currentForm=='' || jQuery.inArray(_wk.currentForm,_wk.forms)==-1) {
            _wk.currentForm = _wk.forms[0];
        }
        _wk.loadForm();
    };
    _wk.loadForm = function() {
        $.post(_wk.ajaxurl,{
            action: _wk.prefix + 'load_form',
            form: _wk.currentForm
        },function(rs){
            _wk.container.html(rs);
        });
    };
    $(function(){
        _wk.init();
        $('#willkit a.navbtn').live('click',function(){
            _wk.currentForm = $(this).attr('rel');
            _wk.init();
        });
    });
})(jQuery);