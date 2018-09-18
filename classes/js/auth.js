/**
 * Display the welcome-restricted page.
 * 
 * This page will be displayed if AUTH_STATUS is false and
 * ".unauthed" element exists.
 */
if (Boolean($('.unauthed').length) && !AUTH_STATUS) {
    $('#app-exit').click(()=>{
        location.href = 'https://c.wikia.com';
    })

    $('button#app-join').click(()=>{
        if (AUTH_STATUS) return;
        $('.modal#appjoin').modal();

        $('.modal#appjoin #appjoin-form').submit(e=>{
            e.preventDefault();
            const AUTH_KEY = $('#appjoin-form input#auth').val();
            console.log(AUTH_KEY);
            if (!AUTH_KEY) return;
            $.post('./classes/session.php',{action:'setTempAuthKey', authkey: AUTH_KEY}).done(()=>{
                location.href = './dex_auth';
            });
        })
    })
}

// app.js (main JS script) will be loaded if AUTH_STATUS is true.
if (AUTH_STATUS) {
    console.log(AUTH_STATUS);
    $('body > script:last-child').before('<script src="./classes/js/app.js"></script>');
}