jQuery.fn.RastorPictures = function(ops){
    options = {
        preview: {
            width: 0,
            height: 0
        },
        fixedThumbSize: true,
        sortable: false,
        crop: true,
        type: 'picture',
        callback: function(){},
        uploadUrl: '/admin/core/cmspictures/upload/',
        cropUrl: '/admin/core/cmspictures/crop/',
        deleteUrl: '/admin/core/cmspictures/delete/',
        data: '[]',
        postData: {},
        showDataKey: '',
        translation: {
            view: '',
            remove: '',
            edit: '',
            pictureEdit: '',
            upload: '',
            addToGallery: '',
            removeDialogTitle: '',
            removeDialogContent: '',
            imageDialogTitle: '',
            imageCropDialogTitle: '',
            buttonYes: '',
            buttonNo: '',
            buttonCancel: ''
        }
    };
    
    jQuery.extend(options, ops);

    var self = this;
    var picture = '';
    var request = '';

    var galleryObj = {
        id: 'gallery_holder',
        addId: 'add_photo',
        data: [],
        init: function(){
            this.data = $.parseJSON(options.data);
            this.id = getNewId('gallery_holder');
            this.addId = getNewId('add_photo');
            $(self).before('<ul class="photos" id="' + this.id + '"></ul><div><button type="button" id="' + this.addId + '">' + options.translation.addToGallery + '</button></div>');
            $('#' + this.addId).click(function(){
                addClick();
            });
            if ($(self).val().length > 2) {
                this.data = $.parseJSON($(self).val());
            }
            this.rebuild();
        },
        toString: function(){
            return $.toJSON(this.data);
        },
        add: function(picture, thumb, params){
            if (!(typeof(thumb) == 'string')){
                params = thumb;
                thumb = undefined;
            }
            
            if (thumb === undefined) {
                picture = {
                    'picture': picture,
                    'sort' : this.getNewSortIndex()
                };
            } else {
                picture = {
                    'picture': picture,
                    'thumb': thumb,
                    'sort': this.getNewSortIndex()
                };
            }
            
            if (params !== undefined) {
                for (key in params) {
                    picture[key] = params[key];
                }
            }
            
            this.data.push(picture);
            this.rebuild();
        },
        update: function(index, data){
            this.data[index] = data;
            this.rebuild();
        },
        getNewSortIndex: function(){
            max = -1;
            if (this.data.length > 0){
                max = this.data[this.data.length - 1].sort;
            }
            return ++max;
        },
        rebuild: function(){
            var gObj = this;
            
            $('#' + this.id).html('');
            
            for (var i = 0; i < this.data.length; i++){
                
                if (this.data[i].thumb === undefined){
                    thumb = this.data[i].picture;
                } else {
                    thumb = this.data[i].thumb;
                }
                
                if (options.crop) {
                    links = '<a class="view" rel="' + this.id + '" href="' + this.data[i].picture + '"><img src="/design/cms/img/icons/view.png" title="' + options.translation.view + '" alt="' + options.translation.view + '" /></a>';
                } else {
                    links = '';
                }
                links += '<img class="edit" src="/design/cms/img/icons/edit.png" title="' + options.translation.pictureEdit + '" alt="' + options.translation.pictureEdit + '" /><img class="delete" src="/design/cms/img/icons/delete.png" title="' + options.translation.remove + '" alt="' + options.translation.remove + '" />';
                
                if (options.postData[options.showDataKey] !== undefined) {
                    linksTop = '<div class="links top">' + this.data[i][options.showDataKey] + '<img class="edit_text" src="/design/cms/img/icons/edit.png" title="' + options.translation.edit + '" alt="' + options.translation.edit + '" /></div>';
                } else {
                    linksTop = '';
                }
                
                if ((this.data[i].picture !== undefined) && (this.data[i].thumb !== undefined)){
                    $('#' + this.id).append('<li indx="' + i + '">' + linksTop + '<div class="holder"><table><tr><td style="width: ' + options.preview.width + 'px; height: ' + options.preview.height + 'px; text-align: center; vertical-align: middle;"><img src="' + thumb + '" alt="" /></td></tr></table></div><div class="links">' + links + '</div></li>');
                } else {
                    $('#' + this.id).append('<li indx="' + i + '">' + linksTop + '<img src="' + thumb + '" alt="" /><div class="links">' + links + '</div></li>');
                }
                
                $('#' + this.id).find('lthis.rebuild();i:last .delete').click(function(){
                    var indx = $(this).parent().parent().attr('indx');
                    gObj.remove(indx);
                    return false;
                    this.rebuild();
                });
                
                $('#' + this.id).find('li:last .edit_text').click(function(){
                    var indx = $(this).parent().parent().attr('indx');
                    buildDataEditDialog(indx);
                    return false;
                });
                
                $('#' + this.id).find('li:last .edit').click(function(){
                    var indx = $(this).parent().parent().attr('indx');
                    editClick(indx);
                    return false;
                });
                this.save();
                if (options.sortable){
                    $('#' + this.id).sortable({
                        stop: function(event, ui){
                            gObj.reSort();
                        }
                    }).disableSelection();
                }
            }
            
            $('#' + this.id).find('a.view').fancybox();
            
            this.save();
        },
        save: function(){
            $(self).val(this.toString());
        },
        remove: function(index){
            gObj = this;
            $('#dialog').html(options.translation.removeDialogContent);
            $('#dialog').dialog({
                title: options.translation.removeDialogTitle,
                width: 300,
                height: 150,
                resizable: false,
                autoOpen: true,
                hide: 'fade',
                buttons: [
                {
                    text: options.translation.buttonYes,
                    click: function() {
                        deleteFile(gObj.data[index].picture);
                        deleteFile(gObj.data[index].thumb);
                        gObj.data.splice(index, 1);
                        gObj.rebuild();
                        $(this).dialog('close');
                    }
                },   
                {
                    text: options.translation.buttonCancel,
                    click: function() {
                        $(this).dialog("close");
                    }
                }
                ]
            });
        },
        reSort: function(){
            var newdata = [];
            var gObj = this;
            $('#' + this.id).find('li').each(function(indx, element){
                var index = $(element).attr('indx');
                newdata[indx] = gObj.data[index];
                newdata[indx].sort = indx;
            });
            this.data = newdata;
            this.rebuild();
        },
        getDataForm: function(value){
            var result = '';
            for (key in options.postData) {
                var val = '';
                if ((value !== undefined) && (value[key] !== undefined)){
                    val = value[key];
                }
                result += '<dt><label>' + options.postData[key] + '</label></dt><dd><input type="text" name="' + key + '" value="' + val + '" /></dd>';
            }
            if (result.length){
                return '<form id="post_form">' + result + '</form>';
            }
            return false;
        }
    }
    
    var pictureObj = {
        id: 'picture_holder',
        addId: 'add_picture',
        data: {},
        init: function(){
            if ((typeof(options.data) == 'string') && (options.data.length > 0)){
                this.data = $.parseJSON(options.data);
            }
            this.id = getNewId('gallery_holder');
            this.addId = getNewId('add_photo');
            $(self).before('<ul class="photos" id="' + this.id + '"></ul><div><button type="button" id="' + this.addId + '">' + options.translation.upload + '</button></div>');
            $('#' + this.addId).click(function(){
                addClick();
            });
            if ($(self).val().length > 2) {
                this.data = $.parseJSON($(self).val());
            }
            this.rebuild();
        },
        toString: function(){
            return $.toJSON(this.data);
        },
        set: function(picture, thumb){
            if (thumb === undefined) {
                this.data = {
                    'picture': picture
                };
            } else {
                this.data = {
                    'picture': picture,
                    'thumb': thumb
                };
            }

            this.rebuild();
        },
        rebuild: function(){
            var pObj = this;
            
            $('#' + this.id).html('');
            
            if (this.data.thumb === undefined){
                thumb = this.data.picture;
            } else {
                thumb = this.data.thumb;
            }
                
            if (options.crop) {
                links = '<a class="view" href="' + this.data.picture + '"><img src="/design/cms/img/icons/view.png" title="' + options.translation.view + '" alt="' + options.translation.view + '" /></a>';
            } else {
                links = '';
            }
            links += '<img class="delete" src="/design/cms/img/icons/delete.png" title="' + options.translation.remove + '" alt="' + options.translation.remove + '" />';
                
            if ((this.data.picture !== undefined) && (this.data.thumb !== undefined) && (this.data.thumb.length)){
                $('#' + this.id).append('<li><div class="holder"><table><tr><td style="width: ' + options.preview.width + 'px; height: ' + options.preview.height + 'px; text-align: center; vertical-align: middle;"><img src="' + thumb + '" alt="" /></td></tr></table></div><div class="links">' + links + '</div></li>').disableSelection();
            } else if ((this.data.thumb !== undefined) && (this.data.thumb.length > 0)) {
                $('#' + this.id).append('<li><img src="' + thumb + '" alt="" /><div class="links">' + links + '</div></li>').disableSelection();
            }
                
            $('#' + this.id).find('li:last .delete').click(function(){
                var indx = $(this).parent().parent().attr('indx');
                pObj.remove();
                return false;
            });
            
            $('#' + this.id).find('a.view').fancybox();
            
            this.save();
        },
        save: function(){
            $(self).val(this.toString());
        },
        remove: function(){
            gObj = this;
            $('#dialog').html(options.translation.removeDialogContent);
            $('#dialog').dialog({
                title: options.translation.removeDialogTitle,
                width: 300,
                height: 150,
                resizable: false,
                autoOpen: true,
                hide: 'fade',
                buttons: [
                {
                    text: options.translation.buttonYes,
                    click: function() {
                        deleteFile(gObj.data.picture);
                        deleteFile(gObj.data.thumb);
                        gObj.data = {};
                        gObj.rebuild();
                        $(this).dialog('close');
                    }
                },   
                {
                    text: options.translation.buttonCancel,
                    click: function() {
                        $(this).dialog("close");
                    }
                }
                ]
            });
        }
    }

    function getNewId(val){
        id = 0;
        do{
            id++;
        } while ($('#'+val+'_'+id).size() != 0);
        
        return val+'_'+id;
    }
    
    function updatePreviewFixed(c){        
        if (parseInt(c.w) > 0)
        {
            var rx = options.preview.width / c.w;
            var ry = options.preview.height / c.h;

            $('#preview').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
            
            picture = filename;
            request = filename+':'+Math.round(imgRateX * c.x)+':'+Math.round(imgRateX * c.y)+':'+Math.round(imgRateX * c.w)+':'+Math.round(imgRateX * c.h)+ ':' + options.preview.width + ':' + options.preview.height;
        }
    }
    
    function updatePreview(c){        
        if ((parseInt(c.w) > 0) && (parseInt(c.h) > 0)) {

            if (c.w / c.h > options.preview.width / options.preview.height){
                newWidth = options.preview.width;
                newHeight = Math.round(options.preview.width * c.h / c.w);
                $('#preview_holder').css({
                    width: newWidth + 'px',
                    height: newHeight + 'px',
                    left: '0px',
                    top: Math.round((options.preview.height - (options.preview.width * c.h / c.w)) / 2)+'px'
                });
                rx = options.preview.width / c.w;
                ry = newHeight / c.h;
            } else {
                newWidth = Math.round(options.preview.height * c.w / c.h);
                newHeight = options.preview.height;
                $('#preview_holder').css({
                    width: newWidth + 'px',
                    height: newHeight + 'px',
                    top: '0px',
                    left: Math.round((options.preview.width - (options.preview.height * c.w / c.h)) / 2)+'px'
                });
                rx = newWidth / c.w;
                ry = options.preview.height / c.h;
            }

            $('#preview').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
            
            picture = filename;
            request = filename+':'+Math.round(imgRateX * c.x)+':'+Math.round(imgRateX * c.y)+':'+Math.round(imgRateX * c.w)+':'+Math.round(imgRateX * c.h)+ ':' + newWidth + ':' + newHeight;
        }
    }
    
    function setBlocksSizes(res){
        max_width = window.innerWidth - options.preview.width - 100;
        max_height = window.innerHeight - 140;
        
        if ((res.width > max_width) && (res.height > max_height)){
            if ((max_width / res.width) > (max_height / res.height)){
                $('#target').css({
                    width: res.width * (max_height / res.height),
                    height: res.height * (max_height / res.height)
                });
                                        
                $('#dialog').dialog({
                    width: ($('#target').width() + options.preview.width + 30),
                    height: ($('#target').height() + 120)
                });
            } else {
                $('#target').css({
                    width: res.width * (max_width / res.width),
                    height: res.height * (max_width / res.width)
                });
                                        
                $('#dialog').dialog({
                    width: ($('#target').width() + options.preview.width + 30),
                    height: ($('#target').height() + 120)
                });
            }
        } else if (res.width > max_width){
            $('#target').css({
                width: res.width * (max_width / res.width),
                height: res.height * (max_width / res.width)
            });
                                        
            var dheight = ($('#target').height() + options.preview.width + 30);
            if (dheight < 200 + options.preview.height){
                dheight = 200 + options.preview.height;
            }
                                        
            $('#dialog').dialog({
                width: ($('#target').width() + options.preview.width + 30),
                height: dheight
            });
        } else if (res.height > max_height){
            $('#target').css({
                width: res.width * (max_height / res.height),
                height: res.height * (max_height / res.height)
            });
                        
            var dwidth = ($('#target').width() + options.preview.width + 30);
            if (dwidth < 200 + options.preview.width){
                dwidth = 200 + options.preview.width;
            }
                        
            $('#dialog').dialog({
                width: dwidth,
                height: ($('#target').height() + 120)
            });
        } else {
            $('#target').css({
                width: res.width,
                height: res.height
            });
                        
            $('#dialog').dialog({
                width: $('#target').width() + options.preview.width + 30,
                height: $('#target').height() + 120
            });
        }
    }
    
    function buildCropDialog(){
        $('#dialog').dialog({
            title: options.translation.imageCropDialogTitle,
            resizable: false,
            draggable: false,
            modal: true,
            hide: 'fade',
            autoOpen: true,
            buttons: [
            { 
                text: options.translation.buttonYes,
                click: function() {
                    $('#dialog').html('<table><tr><td width="' + $('#dialog').width() + '" height="' + ($('#dialog').height() - 30) + '" style="text-align: center; vertical-align: middle;"><img src="/design/cms/img/loading.gif" alt="" /></td></tr></table>');
                    $.getJSON(options.cropUrl, {
                        'params': request
                    }, function(data){
                        if (data.error){
                            alert(data.error);
                            $('#dialog').dialog('close');
                        } else if (data.filename){
                            thumb = data.filename;
                            
                            form  = galleryObj.getDataForm();
                            if (!form) {
                                if (options.type == 'picture'){
                                    pictureObj.set(picture, thumb);
                                } else if (options.type == 'gallery'){
                                    galleryObj.add(picture, thumb);
                                }
                                $('#dialog').dialog('close');
                            } else {
                                $("#dialog").html(form);
                                buildDataDialog(picture, thumb);
                            }
                        }
                    });
                }
            },
            { 
                text: options.translation.buttonNo,
                click: function() {
                    $(this).dialog('close');
                }
            }
            ]
        });
    }
    
    
    function buildEditCropDialog(index){
        $('#dialog').dialog({
            title: options.translation.imageCropDialogTitle,
            resizable: false,
            draggable: false,
            modal: true,
            hide: 'fade',
            autoOpen: true,
            buttons: [
            { 
                text: options.translation.buttonYes,
                click: function() {
                    $('#dialog').html('<table><tr><td width="' + $('#dialog').width() + '" height="' + ($('#dialog').height() - 30) + '" style="text-align: center; vertical-align: middle;"><img src="/design/cms/img/loading.gif" alt="" /></td></tr></table>');
                    $.getJSON(options.cropUrl, {
                        'params': request
                    }, function(data){
                        if (data.error){
                            alert(data.error);
                            $('#dialog').dialog('close');
                        } else if (data.filename){
                            thumb = data.filename;
                            
                            if (options.type == 'gallery'){
                                var data = galleryObj.data[index];
                                data.picture = picture;
                                data.thumb = thumb;
                                galleryObj.update(index, data);
                            }
                            $('#dialog').dialog('close');
                        }
                    });
                }
            },
            { 
                text: options.translation.buttonNo,
                click: function() {
                    $(this).dialog('close');
                }
            }
            ]
        });
    }
    
    function setStartRectFixed(response){
        imgRateX = response.width / $('#target').width();
        imgRateY = response.height / $('#target').width();
        boundx = $('#target').width();
        boundy = $('#target').height();
        imgRateXb = boundx / options.preview.width;
        imgRateYb = boundy / options.preview.height;
        if (options.preview.height * imgRateXb > $('#target').height()) {
            newWidth = options.preview.width * imgRateYb;
            newHeight = boundy;
            posX = (boundx - newWidth) / 2;
            posY = 0;
        } else {
            newWidth = boundx;
            newHeight = options.preview.height * imgRateXb;
            posY = (boundy - newHeight) / 2;
            posX = 0;
        }
        filename = response.filename;
        updatePreviewFixed({
            w: newWidth, 
            h: newHeight, 
            x: posX, 
            y: posY
        });
        
        return [posX, posY, newWidth + posX, newHeight + posY];
    }
    
    function setStartRect(response){
        imgRateX = response.width / $('#target').width();
        imgRateY = response.height / $('#target').width();
        boundx = $('#target').width();
        boundy = $('#target').height();
        imgRateXb = boundx / options.preview.width;
        imgRateYb = boundy / options.preview.height;
        if (options.preview.height * imgRateXb > $('#target').height()) {
            newWidth = options.preview.width * imgRateYb;
            newHeight = boundy;
            posX = (boundx - newWidth) / 2;
            posY = 0;
        } else {
            newWidth = boundx;
            newHeight = options.preview.height * imgRateXb;
            posY = (boundy - newHeight) / 2;
            posX = 0;
        }
        filename = response.filename;
        updatePreview({
            w: boundx, 
            h: boundy, 
            x: 0, 
            y: 0
        });
        
        return [0, 0, boundx, boundy];
    }
    
    function buildUploadDialog(){
        $('#dialog').html('<form id="dialog_upload_form" method="post" action="' + options.uploadUrl + '" enctype="multipart/form-data"><input type="file" name="upload_file" id="upload_file"></form>');
        $("#upload_file").uniform();
        $('#dialog').dialog({
            width: 300,
            height: 140,
            title: options.translation.imageDialogTitle,
            resizable: false,
            autoOpen: true,
            hide: 'fade',
            modal: true,
            buttons: [
            {
                text: options.translation.buttonCancel,
                click: function() {
                    $(this).dialog('close');
                }
            }
            ]
        });
    }
    
    function buildDataEditDialog(indx){
        $('#dialog').dialog('destroy');
        $("#dialog").html(galleryObj.getDataForm(galleryObj.data[indx]));
        $('#dialog').dialog({
            title: '',
            height: 'auto',
            resizable: false,
            draggable: false,
            modal: true,
            hide: 'fade',
            autoOpen: true,
            buttons: [
            { 
                text: options.translation.buttonYes,
                click: function() {
                    var data = $('#post_form').serializeArray();
                    var value = galleryObj.data[indx];
                    for (var i = 0; i < data.length; i++){
                        value[data[i].name] = data[i].value;
                    }
                    galleryObj.update(indx, value);
                    $('#dialog').dialog('close');
                }
            },
            {
                text: options.translation.buttonNo,
                click: function() {
                    $(this).dialog('close');
                }
            }
            ]
        });
    }
    
    function buildDataDialog(picture, thumb){
        $('#dialog').dialog('destroy');
        $('#dialog').dialog({
            title: '',
            height: 'auto',
            resizable: false,
            draggable: false,
            modal: true,
            hide: 'fade',
            autoOpen: true,
            buttons: [
            { 
                text: options.translation.buttonYes,
                click: function() {
                    var data = $('#post_form').serializeArray();
                    params = Object();
                    for (var i = 0; i < data.length; i++){
                        params[data[i].name] = data[i].value;
                    }
                    if (options.crop) {
                        galleryObj.add(picture, thumb, params);
                    } else {
                        galleryObj.add(picture, params);
                    }
                    $('#dialog').dialog('close');
                }
            },
            { 
                text: options.translation.buttonNo,
                click: function() {
                    $(this).dialog('close');
                }
            }
            ]
        });
    }
    
    function editClick(index){
        buildUploadDialog();
        
        $('#upload_file').change(function() {
            $('#dialog_upload_form').iframePostForm({
                json : true,
                complete : function (response){
                    if (response.error){
                        alert(response.error);
                    } else if (response.filename){
                        $('#dialog').dialog('destroy');
                        if (options.crop){
                            if (options.fixedThumbSize){
                                $('#dialog').html('<img id="target" src="'+response.filename+'" alt="" /><div style="width:'+options.preview.width+'px;height:'+options.preview.height+'px;overflow:hidden; position: absolute; top: 6px; right: 10px;border: 1px solid #bbb;"><img src="' + response.filename + '" id="preview" alt="" /></div>');
                                setBlocksSizes(response);
                                buildEditCropDialog(index);
                                $('#target').Jcrop({
                                    onChange: updatePreviewFixed,
                                    onSelect: updatePreviewFixed,
                                    setSelect: setStartRectFixed(response),
                                    aspectRatio: options.preview.width / options.preview.height
                                });
                            } else {
                                $('#dialog').html('<img id="target" src="'+response.filename+'" alt="" /><div style="width:'+options.preview.width+'px;height:'+options.preview.height+'px;overflow:hidden; position: absolute; top: 6px; right: 10px; border: 1px solid #bbb;"><div id="preview_holder" style="position: absolute; overflow: hidden;"><img src="' + response.filename + '" id="preview" alt="" /></div></div>');
                                setBlocksSizes(response);
                                buildEditCropDialog(index);
                                $('#target').Jcrop({
                                    onChange: updatePreview,
                                    onSelect: updatePreview,
                                    setSelect: setStartRect(response)
                                });
                            }
                        } else {
                            if (options.type == 'picture') {
                                pictureObj.set(response.filename, response.filename);
                            } else if (options.type == 'gallery') {
                                var data = galleryObj.data[index];
                                data.picture = response.filename;
                                galleryObj.update(index, data);
                            }
                            options.callback();
                        }
                    } else {
                        alert('Error during upload!');
                    }
                }
            });
            $('#dialog_upload_form').submit();
        });
        
    }
    
    function addClick(){
        buildUploadDialog();
        
        $('#upload_file').change(function() {
            $('#dialog_upload_form').iframePostForm({
                json : true,
                complete : function (response){
                    if (response.error){
                        alert(response.error);
                    } else if (response.filename){
                        $('#dialog').dialog('destroy');
                        if (options.crop){
                            if (options.fixedThumbSize){
                                $('#dialog').html('<img id="target" src="'+response.filename+'" alt="" /><div style="width:'+options.preview.width+'px;height:'+options.preview.height+'px;overflow:hidden; position: absolute; top: 6px; right: 10px;border: 1px solid #bbb;"><img src="' + response.filename + '" id="preview" alt="" /></div>');
                                setBlocksSizes(response);
                                buildCropDialog();
                                $('#target').Jcrop({
                                    onChange: updatePreviewFixed,
                                    onSelect: updatePreviewFixed,
                                    setSelect: setStartRectFixed(response),
                                    aspectRatio: options.preview.width / options.preview.height
                                });
                            } else {
                                $('#dialog').html('<img id="target" src="'+response.filename+'" alt="" /><div style="width:'+options.preview.width+'px;height:'+options.preview.height+'px;overflow:hidden; position: absolute; top: 6px; right: 10px; border: 1px solid #bbb;"><div id="preview_holder" style="position: absolute; overflow: hidden;"><img src="' + response.filename + '" id="preview" alt="" /></div></div>');
                                setBlocksSizes(response);
                                buildCropDialog();
                                $('#target').Jcrop({
                                    onChange: updatePreview,
                                    onSelect: updatePreview,
                                    setSelect: setStartRect(response)
                                });
                            }
                        } else {
                            if (options.type == 'picture') {
                                pictureObj.set(response.filename, response.filename);
                            } else if (options.type == 'gallery') {
                                form  = galleryObj.getDataForm();
                                if (!form) {
                                    galleryObj.add(response.filename);
                                } else {
                                    $("#dialog").html(form);
                                    buildDataDialog(response.filename);
                                }
                            }
                            options.callback();
                        }
                    } else {
                        alert('Error during upload!');
                    }
                }
            });
            $('#dialog_upload_form').submit();
        });
    }

    function deleteFile(filename){
        if (filename !== undefined){
            jQuery.get(options.deleteUrl, {
                'filename': filename
            });
        }
    }

    if (options.type == 'picture') {
        pictureObj.init();
    } else if (options.type == 'gallery') {
        galleryObj.init();
    }
};