(function(){
    var self = lightning.modules.OptinAndShare = {
        complete: function(response) {
            if (response && response.post_id) {
                // Share successful.
                $('form#shared').submit();
            }
        }
    };
})();