function togglePasswordReveal() {
    if ($('#passwd-toggle').attr('data-state') == 'hide') passwordShow();
    else passwordHide();
    return false;
}

function passwordShow() {
    $('#passwd-toggle').attr('data-state', 'show');
    $('#password').attr('type', 'text');
    $('#passwd-toggle').html('(Hide)');
    return false;
}

function passwordHide() {
    $('#passwd-toggle').attr('data-state', 'hide');
    $('#password').attr('type', 'password');
    $('#passwd-toggle').html('(Reveal)');
    return false;
}

function checkUserName() {
    var n = $('#username').val();
    if (n == '') {
        $('#spin-name').attr('data', '/img/blank.svg');
        $('#feedback-name').html('This is a required field').removeClass('ok').addClass('notok');
        return;
    }
    if (/^[A-Za-z0-9_]{6,64}$/.test(n)) {
        $('#spin-name').attr('data', '/img/spinner.svg');
        $('#feedback-name').html('&nbsp;').removeClass('notok').addClass('ok');
        $.ajax({
          url: "../db/username-check.php",
          data: { "n": n, "nonce": nonce },
          dataType: "json"
        }).done(function(res) {
          if (res.name == $('#username').val()) {
              if (res.comment) {
                  $('#feedback-name').html(res.comment);
              }
              if (res.valid) {
                  $('#feedback-username').removeClass('notok').addClass('ok');
                  $('#spin-name').attr('data', '/img/tick.svg');
              }
              else {
                  $('#feedback-username').removeClass('ok').addClass('notok');
                  $('#spin-name').attr('data', '/img/cross.svg');
              }
          }
          // $( this ).addClass( "done" );
        });
    }
    else {
        $('#spin-name').attr('data', '/img/cross.svg');
        $('#feedback-name').html($('#username').val().length < 4 ? 'Too short' : 'Contains invalid characters').removeClass('ok').addClass('notok');
    }
}

function checkPassword() {
    var feedback_colours = ['#f88', '#f88', '#fc8', '#ff6', '#4f4'];
    if ($('#password').val() == '')  {
        $('#spin-pass').attr('data', '/img/blank.svg');
        $('#feedback-password').html('This is a required field').removeClass('ok').addClass('notok');
        $('#password').removeClass('ps0').removeClass('ps1').removeClass('ps2').removeClass('ps3').removeClass('ps4');
        return;
    }
    var result = zxcvbn($('#password').val(), [$('#username').val()]);
    console.log($('#password').val(), [$('#username').val()]);
    console.log(result);
    if ($('#password').val() == 'correct horse battery staple' || $('#password').val() == 'correcthorsebatterystaple') {
        result.score = 0;
        result.feedback.suggestions.push("That one's been used before!");
    }
    $('#password').removeClass('ps0').removeClass('ps1').removeClass('ps2').removeClass('ps3').removeClass('ps4').addClass('ps'+result.score);
    if (result.score > 2) {
        $('#spin-pass').attr('data', '/img/tick.svg');
        $('#feedback-password').html(result.feedback.suggestions.length ? result.feedback.suggestions.join(' ') : ['Good', 'Excellent!'][result.score-3]).removeClass('notok').addClass('ok');
    }
    else {
        $('#spin-pass').attr('data', '/img/cross.svg');
        if ($('#username').val().length > 3 && $('#password').val().indexOf($('#username').val()) >= 0) result.feedback.suggestions.push("Don't include the user name.");
        $('#feedback-password').html(result.feedback.suggestions.length ? result.feedback.suggestions.join(' ') : 'Try a better password').removeClass('ok').addClass('notok');
    }
    checkPasswordConfirm();
}

function checkPasswordConfirm() {
    if ($('#password-confirm').val() == '')  {
        $('#spin-conf').attr('data', '/img/blank.svg');
        $('#feedback-password-confirm').html('This is a required field').removeClass('ok').addClass('notok');
        return;
    }
    if ($('#password').val() == $('#password-confirm').val()) {
        $('#spin-conf').attr('data', '/img/tick.svg');
        $('#feedback-password-confirm').html('Passwords match').removeClass('notok').addClass('ok');
    }
    else {
        $('#spin-conf').attr('data', '/img/cross.svg');
        $('#feedback-password-confirm').html('Passwords do not match').removeClass('ok').addClass('notok');
    }
}

function checkEmail() {
    if ($('#email').val() == '') {
        $('#spin-email').attr('data', '/img/blank.svg');
        $('#feedback-email').html('This is a required field').removeClass('ok').addClass('notok');
        return;
    }
    if (/^[^\x00-\x20@\\]+@[\w-]+(\.[\w-]+){0,3}?\.\w{2,25}$/.test($('#email').val())) {
        $('#spin-email').attr('data', '/img/tick.svg');
        $('#feedback-email').html('Looks OK, but please check for typos or you won’t be able to sign up').removeClass('notok').addClass('ok');
    }
    else {
        $('#spin-email').attr('data', '/img/cross.svg');
        $('#feedback-email').html('Sorry, but that doesn’t look like a valid email address').removeClass('ok').addClass('notok');
    }
}

function checkRegForm() {
    // I'm being lazy and just checking the icons. Should work, though.
    if ( $('#spin-name').attr('data')=='/img/tick.svg' &&
         $('#spin-pass').attr('data')=='/img/tick.svg' &&
         $('#spin-conf').attr('data')=='/img/tick.svg' &&
         $('#spin-email').attr('data')=='/img/tick.svg'
    ) {
        // Prevent double submission
        if ($('#regform').data('submitted')) return false;
        $('#regform').data('submitted', true);
        return true;
    }
}
