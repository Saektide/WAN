class Wikia {
    constructor() {
        return console.warn('Wikia is a static class!');
    }

    static RC(w, func, err) {
        $.ajax({
            type: 'POST',
            url: `./classes/wikia.php?w=${w}`,
            crossDomain: true,
            dataType: 'json',
        })
        .done(func)
        .fail(err);
    }
}