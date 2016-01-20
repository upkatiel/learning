jQuery(document).ready(function($){
    picker = $('input#edit-date-picker');
    $("input[name=date]").change(function () {
        val = $(this).val();
        if (val != 2) {
            picker.hide();
        } else {
            picker.show();
        }
    });
});