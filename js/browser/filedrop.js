<?php

/** This file is part of KCFinder project
  *
  *      @desc Drag and drop files from desktop to KCFinder files pane
  *   @package KCFinder
  *   @version 2.42-dev
  *    @author Forum user
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  */?>

(function() {

    if ((typeof(XMLHttpRequest) != 'undefined') &&
        (typeof(document.addEventListener) != 'undefined') &&
        (typeof(File) != 'undefined') &&
        (typeof(FileReader) != 'undefined')
    ) {

        $(document).ready(function() {

            // NOT GECKO
            if (!XMLHttpRequest.prototype.sendAsBinary) {
                XMLHttpRequest.prototype.sendAsBinary = function(datastr) {
                    var ords = Array.prototype.map.call(datastr, function(x) {
                        return x.charCodeAt(0) & 0xff;
                    });
                    var ui8a = new Uint8Array(ords);
                    this.send(ui8a.buffer);
                }
            }

            var droparea = $('#files').get(0);
            var filesCount = 0;

            // EVENT BINDING
            droparea.addEventListener('dragover', function(e) {
                if (e.preventDefault) e.preventDefault();
                $('#files').addClass('drag');
                return false;
            }, false);
            droparea.addEventListener('dragenter', function(e) {
                if (e.preventDefault) e.preventDefault();
                return false;
            }, false);
            droparea.addEventListener('dragleave', function(e) {
                if (e.preventDefault) e.preventDefault();
                $('#files').removeClass('drag');
                return false;
            }, false);

            droparea.addEventListener('drop', function(e) {
                if (e.preventDefault) e.preventDefault();
                if (e.stopPropagation) e.stopPropagation();
                $('#files').removeClass('drag');
                filesCount += e.dataTransfer.files.length
                for (var i = 0; i < e.dataTransfer.files.length; i++) {
                    var file = e.dataTransfer.files[i];
                    file.thisTargetDir = browser.dir;
                    uploadQueue.push(file);
                }
                processUploadQueue();
                return false;
            }, false);

            var uploadQueue = [];
            var uploadInProgress = false;

            function updateProgress(evt) {
                var progress = evt.lengthComputable
                    ? Math.round((evt.loaded * 100) / evt.total) + "%"
                    : (evt.loaded / 1024) + " KB";
                $('#loading').html(browser.label("Uploading file {number} of {count}... {progress}", {
                    number: filesCount - uploadQueue.length,
                    count: filesCount,
                    progress: progress
                }));
            }

            var boundary = '------multipartdropuploadboundary' + (new Date).getTime();

            function processUploadQueue() {
                if (uploadInProgress)
                    return false;

                if (uploadQueue && uploadQueue.length) {
                    var file = uploadQueue.shift();
                    $('#loading').html(browser.label("Uploading file {number} of {count}... {progress}", {
                        number: filesCount - uploadQueue.length,
                        count: filesCount,
                        progress: ""
                    }));
                    $('#loading').css('display', 'inline');

                    var reader = new FileReader();
                    reader.thisFileName = file.name;
                    reader.thisFileType = file.type;
                    reader.thisFileSize = file.size;
                    reader.thisTargetDir = file.thisTargetDir;

                    reader.onload = function(evt) {
                        uploadInProgress = true;

                        var postbody = '--' + boundary + '\r\nContent-Disposition: form-data; name="upload[]"';
                        if (evt.target.thisFileName)
                                postbody += '; filename="' + evt.target.thisFileName + '"';
                        postbody += '\r\n';
                        if (evt.target.thisFileSize)
                            postbody += 'Content-Length: ' + evt.target.thisFileSize + '\r\n';
                        postbody += 'Content-Type: ' + evt.target.thisFileType + '\r\n\r\n' + evt.target.result + '\r\n--' + boundary + '\r\nContent-Disposition: form-data; name="dir"\r\n\r\n' + _.utf8encode(evt.target.thisTargetDir) + '\r\n--' + boundary + '\r\n--' + boundary + '--\r\n';

                        var xhr = new XMLHttpRequest();
                        xhr.thisFileName = evt.target.thisFileName;

                        if (xhr.upload) {
                            xhr.upload.thisFileName = evt.target.thisFileName;
                            xhr.upload.addEventListener("progress", updateProgress, false);
                        }
                        xhr.open("POST", browser.baseGetData('upload'), true);
                        xhr.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
                        xhr.setRequestHeader("Content-Length", postbody.length);

                        xhr.onload = function(e) {
                            $('#loading').css('display', 'none');
                            browser.refresh();
                            uploadInProgress = false;
                            processUploadQueue();
                        }

                        xhr.sendAsBinary(postbody);
                    };

                    reader.onerror = function(evt) {
                        $('#loading').css('display', 'none');
                        uploadInProgress = false;
                        processUploadQueue();
                        alert(browser.label("Failed to upload {filename}!", {
                            filename: evt.target.thisFileName
                        }));
                    };

                    reader.readAsBinaryString(file);

                } else
                    filesCount = 0;
            }
        });
    }
})();
