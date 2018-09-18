// Remember: Don't change/modify/delete variable that looks UPPERCASED.
/**
 * For "cheaters", AUTH_STATUS will be provided by PHP.
 * if it false, app.js will be auto-removed.
 * If it is't, app.js will be loaded, without returning any error.
 * 
 * Note: AUTH_STATUS is a const variable.
 */
if (!AUTH_STATUS) {
    $('script[src="./classes/js/app.js"]').remove();
    throw new Error('app.js :: Script file was injected but auth status is false! Removing script...');
}

/**
 * The main object for webapp
 * 
 * @type {object}
 * @property {boolean} statusFocus Indicates the current status of tab/window.
 * @property {boolean} isNotifyAllowed Indicates the current status of notifies (allowed or not).
 * @property {boolean} rememberedNotifies Indicates if user was warned about notifies.
 * @property {array} wikis Wikis will be listed here.
 * @property {object} lastRC WikisRC will be stored here.
 * @property {number} MAX_WIKIS_NUMBER (UPPERCASED var) is the max. number of wikis that can
 * be stored on "wikis" array.
 */
var wan = {
    statusFocus: false,
    isNotifyAllowed: false,
    rememberedNotifies: false,
    wikis: [],
    lastRC: {},
    MAX_WIKIS_NUMBER: 5
}

// Verify if permission for Notification is granted or not.
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
        new Notification(i18n[wan.preferedLang].wanIsRunning, {body: i18n[wan.preferedLang].wanIsRunning2});
        wan.rememberedNotifies = true;
    }
})

/**
 * Main class for Modal/Dialog window
 * 
 * @class Modal
 */
class Modal {
    /**
     * Invokes a new modal
     * 
     * @constructor
     * @param {string} title
     * @param {string} body
     */
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
    /**
     * Hides the invoked modal
     * 
     * @function hide
     * @static
     */
    static hide() {
        // Set classes for Modal Window
        $('.modal').removeClass('active');
        $('.modal').addClass('hidden');
        // Set classes for Modal Background
        $('.warpmodal').removeClass('active');
        $('.warpmodal').addClass('hidden');
    }
}
/**
 * Session controller. This class actually is static.
 * 
 * @class Session
 */
class Session {
    /**
     * User will be warned for invoke this class.
     * 
     * @constructor
     */
    constructor() {
        return console.warn('Session is a static class!');
    }

    /**
     * Destroy current session (auth, wikis, etc)
     * 
     * @static
     * @callback func
     */
    static destroySession(func) {
        $.post('./classes/session.php',{action:'destroy'}).done(func);
    }

    /**
     * Store wiki in current session
     * 
     * @static
     * @param {string} domain
     * @callback func
     */
    static saveWiki(domain, func) {
        $.post('./classes/session.php',{action:'saveWiki', wiki: domain}).done(func);
    }

    /**
     * Remove wiki in current session
     * 
     * @static
     * @param {number} id
     */
    static removeWiki(id) {
        $(`.wikirc#${id}`).remove();
        wan.wikis.splice(id, 1);
        $.post('./classes/session.php',{action:'removeWiki', id: id}).done(()=>{
            if (wan.wikis.length < wan.MAX_WIKIS_NUMBER) $('#addwiki').removeProp('disabled');
        });
    }
}

/**
 * Wiki controller.
 * 
 * @class Wiki
 */
class Wiki {
    /**
     * If Wiki class is invoked, this will add the wiki
     * 
     * @constructor
     * @param {string} dom Interwiki
     */
    constructor(dom) {
        new Modal(
            i18n[wan.preferedLang].addWiki,
            i18n[wan.preferedLang].addWikiProcess
        )
        // Prevent wiki add abuse
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) {
            new Modal(
                i18n[wan.preferedLang].addWiki,
                i18n[wan.preferedLang].addWikiReachedLimit
            )
            return false;
        }
        wan.wikis.push(dom); // Add wiki to list
        $.get(`./classes/templates/${wan.preferedLang}/wikirc.html`).done(wiki=>{
            var reElement = wiki
            .replace(/\$1/g,wan.wikis.indexOf(dom))
            .replace(/\$2/g,dom);

            $('.wikislist').append($.parseHTML(reElement));

            new Modal(
                i18n[wan.preferedLang].addWiki,
                i18n[wan.preferedLang].addWikiSavingSession
            )

            Session.saveWiki(dom,(data)=>{
                console.log(data);
                new Modal(
                    i18n[wan.preferedLang].addWiki,
                    i18n[wan.preferedLang].addWikiDone
                )
            })
        })
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled', 'true');
    }

    /**
     * Add manually a wiki
     * 
     * @static
     * @param {string} dom Interwiki
     */
    static add(dom) {
        $.post(`./classes/templates/${wan.preferedLang}/wikirc.html`).done(wiki=>{
            var reElement = wiki
            .replace(/\$1/g,wan.wikis.indexOf(dom))
            .replace(/\$2/g,dom);
    
            $('.wikislist').append($.parseHTML(reElement));
        })
        if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled', 'true');
    }

    /**
     * Remove manually a wiki
     * 
     * @static
     * @param {string} dom Interwiki
     */
    static remove(dom) {
        Session.removeWiki(wan.wikis.indexOf(dom));
    }

    /**
     * Update the RC info for a wiki
     * 
     * @static
     * @param {number} id WikiRC ID
     * @param {string} title Article title
     * @param {string} user User that made changes
     * @param {string} type Type of change (edit|log|new)
     * @param {string} summary Revision's summary
     * @param {string} w Interwiki
     */
    static updateInfo(id, title, user, type, summary, diff, w) {
        let x = `.wikirc#${id}`;
        let displaytitle = title;
        if (Boolean(displaytitle.match(/@comment-/g))) displaytitle = i18n[wan.preferedLang].aMessage;

        $(`${x} .lastrc > .lasttitle`).html(`<a href="http://${w}.wikia.com/wiki/${title}" target="_blank">${displaytitle}</a>`);
        $(`${x} .lastrc > .lastuser > a`).text(user);
        $(`${x} .lastrc > .lastuser > a`).attr('href', `http://${w}.wikia.com/wiki/User:${user}`)
        $(`${x} .lastrc > .lasttype`).text(type);
        $(`${x} .lastsumm span`).text(summary);
        $(`${x} .lastdiff table`).html(diff);

        console.log(`Wiki #${id} RC Info has been updated!`);
    }
}

/**
 * IO controller. This class is static.
 * 
 * @class IO
 */
class IO {
    /**
     * User will be warned for invoke this class.
     * 
     * @constructor
     */
    constructor() {
        return console.warn('IO is a static class!');
    }

    /**
     * Starts to monitoring wikis in the list (wan.wikis)
     * 
     * @static
     */
    static start() {
        console.log('WAN IS NOW START TO MONITORING TARGERED WIKIS')
        let intRC = setInterval(()=>{
            if (wan.wikis.length === 0) return;
            Wikia.RC(wan.wikis.join('|'), (raw)=>{
                Object.keys(raw.wikisRC).forEach(wiki => {
                    let ROOT = raw.wikisRC[wiki].rc;
                    let DIFF;
                    if (raw.wikisRC[wiki].diff) {
                        DIFF = raw.wikisRC[wiki].diff['*'];
                    }

                    if (!ROOT) {
                        console.warn(`[RC] RC is null on ${wiki} - Status code: ${raw.wikisRC[wiki].status}`);
                        if (raw.wikisRC[wiki].status == 410) {
                            new Modal (
                                i18n[wan.preferedLang].closedWiki,
                                i18n[wan.preferedLang].closedWikiBody.replace(/\$1/g, wiki)
                            )
                            Wiki.remove(wiki);
                        }
                        return;
                    }

                    if (!DIFF) {
                        DIFF = null;
                    }
                    // New wiki
                    if (!wan.lastRC[wiki]) {
                        Wiki.updateInfo(wan.wikis.indexOf(wiki),
                        ROOT.title,
                        ROOT.user,
                        ROOT.type,
                        ROOT.comment,
                        DIFF,
                        wiki
                        );
                        return wan.lastRC[wiki] = ROOT;
                    }
                    // Added wiki, verify for changes
                    if (JSON.stringify(ROOT) == JSON.stringify(wan.lastRC[wiki])) console.log('- Pure / No changes -');
                    else {
                        console.log('- Custom / Changes detected -');
                        new Notification(i18n[wan.preferedLang].newChanges.replace(/\$1/g, wiki));
                        wan.lastRC[wiki] = ROOT;
                        Wiki.updateInfo(wan.wikis.indexOf(wiki),
                        ROOT.title,
                        ROOT.user,
                        ROOT.type,
                        ROOT.comment,
                        DIFF,
                        wiki
                        );
                    }
                })
            },
            (err)=>{
                console.log(err);
            });
        },4000)
    }
}

// Button actions

$('#addwiki').click(function(){
    new Modal(
        i18n[wan.preferedLang].addWiki,
        i18n[wan.preferedLang].loading
    )
    $.post(`./classes/templates/${wan.preferedLang}/addwikiform.html`).done(data=>{
        new Modal(
            i18n[wan.preferedLang].addWiki,
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
        i18n[wan.preferedLang].faq,
        i18n[wan.preferedLang].loading
    )
    $.post(`./classes/templates/${wan.preferedLang}/faq.html`).done(data=>{
        new Modal(
            i18n[wan.preferedLang].faq,
            data
        )
    })
})

$('#whatisnew').click(function(){
    new Modal(
        i18n[wan.preferedLang].updates,
        i18n[wan.preferedLang].loading
    )
    $.post(`./classes/templates/${wan.preferedLang}/whatisnew.html`).done(data=>{
        new Modal(
            i18n[wan.preferedLang].updates,
            data
        )
    })
})

$('#aboutwan').click(function(){
    new Modal(
        i18n[wan.preferedLang].aboutWAN,
        i18n[wan.preferedLang].loading
    )
    $.post(`./classes/templates/${wan.preferedLang}/aboutwan.html`).done(data=>{
        new Modal(
            i18n[wan.preferedLang].aboutWAN,
            data
        )
    })
})

window.onload = function() {
    if (location.hostname != 'localhost') {
        if (location.protocol != 'https:') {
            new Modal(
                'Wikia Activity Notifier',
                i18n[wan.preferedLang].redirectingToHTTPS
            );
            setTimeout(()=>{
                window.location = 'https://saektide.com/wan';
            },2500)
            return;
        }
    }
    


    let count = 0;
    wan.preWikis.forEach(wikiDom => {
        count++
        setTimeout(()=>{
            console.log('Loading Wiki: '+wikiDom)
            wan.wikis.push(wikiDom);
            Wiki.add(wikiDom);
            if (wan.wikis.length >= wan.MAX_WIKIS_NUMBER) $('#addwiki').prop('disabled', 'true');
        }, 1000 * count)
    });

    setInterval(()=>{
        if ($('.wikirc').length > wan.MAX_WIKIS_NUMBER) {
            $('.wikirc').remove();
            wan.wikis = [];
            Session.destroySession();
            new Modal(
                i18n[wan.preferedLang].abuseDetected,
                i18n[wan.preferedLang].abuseDetectedBody
            )
        }
    },1000)

    IO.start(); // Start!
    
    new Modal(
        'Wikia Activity Notifier',
        i18n[wan.preferedLang].welcome
    );

    setTimeout(Modal.hide, 2000);
}