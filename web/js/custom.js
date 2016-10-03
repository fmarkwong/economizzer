// /category/create form

$('.field-parent-category-form').hide();

$("input[name='Category[parent_or_sub]']").on('click', function() {
    if ($(this).val() == 'sub') {
        $('.field-parent-category-form').show();
        $('#parent-category-form option:contains("None")').remove();
        $('.field-category-type_id').hide();
    }

    if ($(this).val() == 'parent') {
        $('#parent-category-form').prepend("<option value selected>None</option>");
        $('.field-parent-category-form').hide();
        $('.field-category-type_id').show();
    }
});

