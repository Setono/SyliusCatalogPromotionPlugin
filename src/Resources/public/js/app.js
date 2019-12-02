(function($) {
    $(function () {
        setTimeout(function(){
          $('select[name^="setono_sylius_catalog_promotion_promotion[rules]"][name$="[type]"]').change();
        }, 50);

        $('#rules a[data-form-collection="add"]').on('click', function () {
            setTimeout(function(){
                $('select[name^="setono_sylius_catalog_promotion_promotion[rules]"][name$="[type]"]').last().change();
            }, 50);
        });
    });
})(jQuery);
