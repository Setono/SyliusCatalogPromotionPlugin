(function($) {
    $(function () {
        setTimeout(function(){
          $('select[name^="setono_sylius_catalog_promotion_promotion[rules]"][name$="[type]"]').change();
        }, 2000);

        $('#rules a[data-form-collection="add"]').on('click', function () {
            setTimeout(function(){
                $('select[name^="setono_sylius_catalog_promotion_promotion[rules]"][name$="[type]"]').last().change();
            }, 2000);
        });
    });
})(jQuery);
