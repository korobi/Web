(function($) {
    $.kHash = {
        get: function (name) {
            if (typeof (name) === 'string') {
                return this.query()[name.toLowerCase()];
            }
        },

        query: function () {
            var hash = window.location.hash,
                result = {},
                pair = null;
            if (hash !== null && hash !== undefined) {
                if (hash.indexOf('#') === 0) {
                    hash = hash.substring(1, hash.length);
                }

                var parts = hash.split('&');
                for (var i in parts) {
                    pair = parts[i].split('=');
                    result[pair[0].toString().toLowerCase()] = pair[1];
                }
            }
            return result;
        }
    };
})(jQuery);
