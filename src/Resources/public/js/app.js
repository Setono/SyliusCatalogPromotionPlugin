(function($) {
    $(document).ready(function () {

        setTimeout(function(){
          $('select[name^="setono_sylius_bulk_discount_discount[rules]"][name$="[type]"]').change();
        }, 50);

        $('#rules a[data-form-collection="add"]').on('click', function () {
            setTimeout(function(){
                $('select[name^="setono_sylius_bulk_discount_discount[rules]"][name$="[type]"]').last().change();
            }, 50);
        });

    });
})(jQuery);
