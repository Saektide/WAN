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

    $('#app-join').click(()=>{
        let AUTH_KEY = prompt('Please enter an auth key');
        if (AUTH_KEY.length == 0) return;

        if (AUTH_STATUS) return;
        $.post('./classes/session.php',{action:'setTempAuthKey', authkey: AUTH_KEY}).done(()=>{
            location.href = './dex_auth';
        });
    })
}

// app.js (main JS script) will be loaded if AUTH_STATUS is true.
if (AUTH_STATUS) {
    console.log(AUTH_STATUS);
    $('body > script:last-child').before('<script src="./classes/js/app.js"></script>');
}