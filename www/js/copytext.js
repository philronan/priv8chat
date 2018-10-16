function copy_textarea_to_clipboard(text_elem, button_elem) {
    success = true;
    if (!navigator.clipboard) {
        text_elem.focus();
        text_elem.select();
        try {
            success = document.execCommand('copy');
        }
        catch(err) {
            success = false;
        }
        text_elem.blur();
        copy_feedback(button_elem, success);
    }
    else {
        navigator.clipboard.writeText(text_elem.val()).then(function(button_elem){
            try {
                copy_feedback(button_elem, true);
            }
            catch(err) {
                copy_feedback(button_elem, false);
            }
            text_elem.blur();
        });
    }
}

function copy_feedback(button_elem, success) {
    var ot = button_elem.text();
    button_elem.prop('disabled', true);
    button_elem.removeClass('btn-primary');
    button_elem.addClass(success ? 'btn-success' : 'btn-danger');
    button_elem.text(success ? 'Copied!' : 'Sorry, unable to copy :-(');
    setTimeout(function(){
        button_elem.removeClass('btn-success');
        button_elem.removeClass('btn-danger');
        button_elem.addClass('btn-primary');
        button_elem.text(ot);
        button_elem.prop('disabled', false);
    }, 2000);
}
