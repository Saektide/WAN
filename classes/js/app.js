var wan = {
    statusFocus: false,
    isNotifyAllowed: false,
    rememberedNotifies: false,
    wikis: [],
    lastRC: {},
    MAX_WIKIS_NUMBER: 5
}

if (Notification.permission !== 'granted') {
    Notification.requestPermission().then(function(state){
        if (state !== 'granted') wan.isNotifyAllowed = false;
        else wan.isNotifyAllowed = true;
    })
} else wan.isNotifyAllowed = true;

$(window).focus(function(){
    wan.statusFocus = true;
}).blur(function(){
    wan.statusFocus = false;
    if (!wan.rememberedNotifies && wan.isNotifyAllowed) {
        new Notification('WAN is running...', {body: 'WAN still running until you close the tab. We\'ll notify you about recent changes on target wikis.'});
        wan.rememberedNotifies = true;
    }
})

class Modal {
    constructor(title, body) {
        $('.modal > h3').text(title);
        if (body == null) $('.modal > .body').html('');
        else $('.modal > .body').html(body);
        // Set classes for Modal Window
        $('.modal').removeClass('hidden');
        $('.modal').addClass('active');
        // Set classes for Modal Background
        $('.warpmodal').removeClass('hidden');
        $('.warpmodal').addClass('active');

        $('#closemodal').click(Modal.hide)
    }

    static hide() {
        // Set classes for Modal Window
        $('.modal').removeClass('active');
        $('.modal').addClass('hidden');
        // Set classes for Modal Background
        $('.warpmodal').removeClass('active');
        $('.warpmodal').addClass('hidden');
    }
}

class Session {
    constructor() {
        return console.warn('Session is a static class!');
    }

    static destroySession(func) {
        $.post('./classes/session.php',{action:'destroy'}).done(func);
    }

    static saveWiki(domain, func) {
        $.post('./classes/session.php',{action:'saveWiki', wiki: domain}).done(func);
    }

    static removeWiki(id) {
        new Modal(
            'Remove Wiki',
            'Removing from your session...'
        )
        $(`.wikirc#${id}`).remove();
        wan.wikis.splice(id, 1);
        $.post('./classes/session.php',{action:'removeWiki', id: id}).done(()=>{
            new Modal(
                'Remove Wiki',
                'Done! You can close now this window.'
            )
            if (wan.wikis.length < wan.MAX_WIKIS_NUMBER) $('#addwiki').removeProp('disabled');
        });
    }
}

class Wiki {
    constructor(dom) {
        new Modal(
            'Add Wiki',
            'Adding...'
        )
        // Prevent HTTPS denial
        if (Boolean(dom.match(/\./g))) {
            new Modal(
                'Add Wiki',
                'ERROR: This wiki seems hasn\'t HTTPS. WAN won\'t add your wiki until Wikia inject HTTPS in all wikis.'
            )
            return false;
        }
        // Prevent wiki add abuse
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) {
            new Modal(
                'Add Wiki',
                `ERROR: You have reached the max number of wikis to add here (${wan.MAX_WIKIS_NUMBER}).`
            )
            return false;
        }
        wan.wikis.push(dom);
        $.get('./classes/templates/wikirc.html').done(wiki=>{
            var reElement = wiki
            .replace(/\$1/g,wan.wikis.indexOf(dom))
            .replace(/\$2/g,dom);

            $('.wikislist').append($.parseHTML(reElement));

            new Modal(
                'Add Wiki',
                'Saving session...'
            )

            Session.saveWiki(dom,(data)=>{
                console.log(data);
                new Modal(
                    'Add Wiki',
                    'Added wiki! You can close now this window.'
                )
            })
        })
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled');
    }

    static add(dom) {
        $.post('./classes/templates/wikirc.html').done(wiki=>{
            var reElement = wiki
            .replace(/\$1/g,wan.wikis.indexOf(dom))
            .replace(/\$2/g,dom);
    
            $('.wikislist').append($.parseHTML(reElement));
        })
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled');
    }

    static remove(dom) {
        Session.removeWiki(wan.wikis.indexOf(dom));
    }

    static updateInfo(id, title, user, type, summary, w) {
        let x = `.wikirc#${id}`;
        let displaytitle = title;
        if (Boolean(displaytitle.match(/@comment-/g))) displaytitle = 'A message';

        $(`${x} .lastrc > .lasttitle`).html(`<a href="https://${w}.wikia.com/wiki/${title}" target="_blank">${displaytitle}</a>`);
        $(`${x} .lastrc > .lastuser`).text(user);
        $(`${x} .lastrc > .lasttype`).text(type);
        $(`${x} .lastsumm span`).text(summary);

        console.log(`Wiki #${id} RC Info has been updated!`);
    }
}

class IO {
    constructor() {
        return console.warn('IO is a static class!');
    }

    static start() {
        console.log('WAN IS NOW START TO MONITORING TARGERED WIKIS')
        let intRC = setInterval(()=>{
            wan.wikis.forEach(wiki => {
                Wikia.RC(wiki,(dataRC)=>{
                    if (!wan.lastRC[wiki]) {
                        Wiki.updateInfo(wan.wikis.indexOf(wiki),
                        dataRC.query.recentchanges[0].title,
                        dataRC.query.recentchanges[0].user,
                        dataRC.query.recentchanges[0].type,
                        dataRC.query.recentchanges[0].comment,
                        wiki
                        )
                        return wan.lastRC[wiki] = dataRC.query.recentchanges[0];
                    }
                    if (JSON.stringify(dataRC.query.recentchanges[0]) == JSON.stringify(wan.lastRC[wiki])) console.log('- No changes -');
                    else {
                        console.log('- ON changes -');
                        new Notification(`New changes at ${wiki}`);
                        wan.lastRC[wiki] = dataRC.query.recentchanges[0];
                        Wiki.updateInfo(wan.wikis.indexOf(wiki),
                        dataRC.query.recentchanges[0].title,
                        dataRC.query.recentchanges[0].user,
                        dataRC.query.recentchanges[0].type,
                        dataRC.query.recentchanges[0].comment,
                        wiki
                        )
                    }
                    
                },
                (err)=>{
                    console.log(err)
                    if (err.status == 429) {
                        
                        if (!wan.onCoolDown) {
                            clearInterval(intRC);
                            wan.onCoolDown = true;
                            new Notification(
                                'Oops!',
                                {body:'Seems we got a 429 error code in our operation, WAN will be restart in 60 minutes.'}
                            );
                            setTimeout(()=>{
                                new Notification(
                                    'Restarting WAN...'
                                );
                                IO.start();
                                wan.onCoolDown = false;
                            },3600000)
                            
                        }
                    }
                })
            });
        },4000)
    }
}

// -- Main

$('#addwiki').click(function(){
    new Modal(
        'Add Wiki',
        'Loading...'
    )
    $.post('./classes/templates/addwikiform.html').done(data=>{
        new Modal(
            'Add Wiki',
            data
        )
        
        // On form submit
        $('form.addwikiform').submit(function(e){
            e.preventDefault();
            var realEscDom = $('[name="domain"]').val().trim();
            if (realEscDom.length != 0) {
                new Wiki(realEscDom);
            }
        })
    })
})

$('#faq').click(function(){
    new Modal(
        'FAQ',
        'Loading...'
    )
    $.post('./classes/templates/faq.html').done(data=>{
        new Modal(
            'FAQ',
            data
        )
    })
})

$('#whatisnew').click(function(){
    new Modal(
        'Updates',
        'Loading...'
    )
    $.post('./classes/templates/whatisnew.html').done(data=>{
        new Modal(
            'Updates',
            data
        )
    })
})

$('#aboutwan').click(function(){
    new Modal(
        'About WikiaActivityNotifier',
        'Loading...'
    )
    $.post('./classes/templates/aboutwan.html').done(data=>{
        new Modal(
            'About WikiaActivityNotifier',
            data
        )
    })
})

window.onload = function() {
    if (location.protocol != 'https:') {
        new Modal(
            'Wikia Activity Notifier',
            'Redirecting to HTTPS site...'
        );
        window.location = 'https://saektide.com/wan';
        return;
    }


    let count = 0;
    wan.preWikis.forEach(wikiDom => {
        count++
        setTimeout(()=>{
            console.log('Loading Wiki: '+wikiDom)
            wan.wikis.push(wikiDom);
            Wiki.add(wikiDom);
            if (wan.wikis.length >= MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled');
        }, 1000 * count)
    });

    setInterval(()=>{
        if ($('.wikirc').length > wan.MAX_WIKIS_NUMBER) {
            $('.wikirc').remove();
            wan.wikis = [];
            new Modal(
                'Too many wikis to reach RC',
                'We are detecting some unusual on your current session. We cleared all for avoid this abuse. Please don\'t do that again.'
            )
        }
    },1000)

    IO.start(); // Start!
    
    new Modal(
        'Wikia Activity Notifier',
        'Welcome!'
    );

    setTimeout(Modal.hide, 2000);
}

new Modal(
    'Wikia Activity Notifier',
    'Loading...'
);