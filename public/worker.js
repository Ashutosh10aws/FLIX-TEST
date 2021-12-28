self.addEventListener('message', function(e) {
    console.log(e);
    self.postMessage("Hello");
    var data = e.data;
    switch (data.type) {
        case 'CalcProgramme':
            // var programmes=data.data;
            // var client_offset = moment(new Date()).utcOffset();
            // Object.keys(programmes).map(function (key) {
            //     var items=programmes[key];
            //     items.map(function (item) {
            //         item.start=convertProgrammeTimeToClientTime(item.start, client_offset);
            //         item.stop=convertProgrammeTimeToClientTime(item.stop, client_offset);
            //     })
            // })
            // self.postMessage({data:programmes});
            self.postMessage("Hello, how are you");
            break;
        case 'stop':
            self.postMessage('WORKER STOPPED: ' + data.msg +
                '. (buttons will no longer work)');
            self.close(); // Terminates the worker.
            break;
        default:
            self.postMessage('Unknown command: ' + data.msg);
    };
}, false);
