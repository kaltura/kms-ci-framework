
// this should be set to the relative location of kms-ci-framework root path
// usually it will be something like ../../../vendor/kaltura/kms-ci-framework/
var kmsCiFrameworkPath = '../../../';
var kmsci = require(kmsCiFrameworkPath + '/js/kmsci.js');
kmsci.casper.init(kmsci);
kmsci.casper.validateParams(['param1']);

var testinclude = require(kmsci.integration.getIntegrationFilename('test.inc.casper.js'));

casper.test.begin('test', 9, function suite(test) {
    casper.start().then(function(){
        test.assertEquals(testinclude, 'FOO');
        test.assertEquals(kmsci.casper.getParam('param1'), 'FOO!');
        test.assertEquals(kmsci.runner.getConfig('FOO'), 'BAR');
        test.assert(kmsci.runner.isArg('FOO'));
        test.assertEquals(kmsci.runner.getArg('FOO'), 'BAR');
        test.assertEquals(kmsci.integration.getIntegrationId(), kmsci.casper.getParam('integId'));
        test.assertEquals(kmsci.integration.getIntegrationPath(), kmsci.casper.getParam('integPath'));
        test.assertEquals(kmsci.integration.getOutputPath(), kmsci.casper.getParam('integOutput'));
        test.assertEquals(kmsci.integration.getIntegrationFilename('test.js'), kmsci.casper.getParam('integPath')+'/test.js');
    }).run(function(){
        test.done();
    });
});
