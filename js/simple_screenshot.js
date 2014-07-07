/**
 * take a screenshot of a url
 */

var fs = require('fs');
var system = require('system');

var main = function(cb, url, width, height, img_file, html_file) {
    var page = require('webpage').create();
    page.settings.resourceTimeout = 40000;
    page.viewportSize = {'width': width, 'height': height};
    console.log('rendering '+url+' to '+img_file);
    var page_status = '';
    var page_headers = '';
    var page_is_timeout = false;
    page.onResourceReceived = function(resource) {
        if (resource.url == url) {
            page_status = resource.status;
            page_headers = JSON.stringify(resource.headers);
        }
    };
    page.onResourceTimeout = function(resource) {
        console.log('timeout on resource', resource.url);
        page_is_timeout = true;
    };
    var start_time = Date.now();
    page.open(url, function(a, b, c) {
        page.render(img_file);
        fs.write(html_file, page.content, 'w');
        fs.write(html_file+'.headers.json', page_headers, 'w');
        var run_time_secs = Math.round((Date.now()-start_time)/1000);
        if (run_time_secs > 5) {
            console.log('run time = '+run_time_secs+' seconds');
            console.log('warning: run time is longer then 5 seconds');
        }
        if (page_is_timeout) {
            cb(false);
        } else if (page_status != 200) {
            if (page_status == 301 || page_status == 302) {
                var loc = '';
                page_headers = JSON.parse(page_headers);
                for (var i in page_headers) {
                    var page_header = page_headers[i];
                    if (page_header.name.toLowerCase() == 'location') {
                        loc = page_header.value;
                        break;
                    }
                }
                if (loc == '') {
                    console.log('got redirect status but could not find location header');
                    cb(false);
                } else {
                    if (loc.indexOf('http') != 0) {
                        var hostname = url.split('//')[1].split('/')[0];
                        var prot = url.split('//')[0];
                        loc = prot+'//'+hostname+loc;
                    }
                    main(cb, loc, width, height, img_file, html_file);
                }
            } else {
                console.log('got http error '+page_status);
                cb(false);
            }
        } else {
            cb(true);
        }
    });
};

if (system.args.length != 6) {
    console.log('usage: phantomjs simple_screenshot.js (URL) (WIDTH) (HEIGHT) (IMG_FILE) (HTML_FILE)');
    phantom.exit();
} else {
    var url = system.args[1];
    var width = system.args[2];
    var height = system.args[3];
    var img_file = system.args[4];
    var html_file = system.args[5];
    main(function(ret) {
        if (ret) {
            phantom.exit(0);
        } else {
            phantom.exit(1);
        }
    }, url, width, height, img_file, html_file);
}
