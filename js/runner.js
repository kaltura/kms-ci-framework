var kmsci;

module.exports = {

    _config: {},
    _args: {},

    init: function(_kmsci, env)
    {
        kmsci = _kmsci;
        this._config = env['config'];
        this._args = env['args'];
    },

    getConfig: function(key, def)
    {
        if (typeof(def) == 'undefined') def = '';
        if (typeof(this._config[key]) == 'undefined') {
            return def;
        } else {
            return this._config[key];
        }
    },

    isArg: function(key)
    {
        if (typeof(this._args[key]) != 'undefined' && this._args[key]) {
            return true;
        } else {
            return false;
        }
    },

    getArg: function(key, def)
    {
        if (typeof(def) == 'undefined') def = '';
        if (typeof(this._args[key]) == 'undefined') {
            return def;
        } else {
            return this._args[key];
        }
    }

};
