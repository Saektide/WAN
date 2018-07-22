class Wikia {
    constructor() {
        return console.warn('Wikia is a static class!');
    }

    static RC(w, func, err) {
        $.ajax({
            type: 'POST',
            url: `https://${w}.wikia.com/api.php?action=query&list=recentchanges&rclimit=1&rcprop=user|title|ids|loginfo|sizes|timestamp|comment|sizes&rcshow=!bot&format=json`,
            crossDomain: true,
            dataType: 'jsonp',
        })
        .done(func)
        .fail(err);
    }
}