/**
 * MW IO/requests controller. This class is static.
 * 
 * @class MW
 */
class MW {
    /**
     * User will be warned for invoke this class.
     * 
     * @constructor
     */
    constructor() {
        return console.warn('MW is a static class!');
    }

    /**
     * Get the RC of wiki(s).
     * 
     * @static
     * @param {string} w Interwiki. In case of multiples, can be separated by "|".
     * @callback func Will be called if RC was provided.
     * @callback err Will be called if response is an error.
     */
    static RC(w, func, err) {
        $.ajax({
            type: 'POST',
            url: `./classes/rc.php?w=${w}`,
            crossDomain: true,
            dataType: 'json',
        })
        .done(func)
        .fail(err);
    }
}