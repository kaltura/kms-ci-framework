var kmsci;

module.exports = {

    _env: {},

    init: function(_kmsci, env)
    {
        kmsci = _kmsci;
        this._env = env;
    },

    getOutputPath: function()
    {
        return this._env.outputPath;
    },

    getIntegrationPath: function()
    {
        return this._env.integrationPath;
    },

    getIntegrationId: function()
    {
        return this._env.integrationId;
    },

    getIntegrationFilename: function(filename)
    {
        return this.getIntegrationPath() + '/' + filename;
    }

};
