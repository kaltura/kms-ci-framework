var kmsci;

module.exports = {

    init: function(_kmsci)
    {
        kmsci = _kmsci;
        var env = this.getParam('kmscienv', '');
        if (env == '') {
            env = {
                'config': {},
                'args': {}
            };
        } else {
            env = JSON.parse(env);
        }
        kmsci.runner.init(kmsci, env);
        var integenv = this.getParam('kmsciint', '');
        if (integenv == '') {
            integenv = {
                'config': {},
                'args': {}
            };
        } else {
            integenv = JSON.parse(integenv);
        }
        kmsci.integration.init(kmsci, integenv);
    },

    validateParams: function(params)
    {
        for (var i in params) {
            var name = params[i];
            if (!casper.cli.has(name)) {
                throw 'param '+name+' must be provided';
            }
        }
    },

    getParam: function(name, def)
    {
        if (typeof(def) == 'undefined') def = '';
        if (casper.cli.has(name)) {
            return casper.cli.get(name);
        } else {
            return def
        }
    }

};
