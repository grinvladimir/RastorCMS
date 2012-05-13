/*
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011 Budjak Orest
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @version 0.8
 */

jQuery.fn.RastorTree = function(options){
    var options = jQuery.extend({
        nestedSortable: {
            disableNesting: 'no-nest',
            forcePlaceholderSize: true,
            handle: 'div',
            helper: 'clone',
            items: 'li',
            maxLevels: 10,
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div'
        },
        msgShowTime: 3000,
        removeUrl: '',
        reloadUrl: '',
        editUrl: '',
        saveUrl: '',
        addUrl: '',
        data: '',
        startMsg: {
            type: '', 
            msg: ''
        },
        tranlation: {
            removeOne: '',
            removeOneConfirm: '',
            editTitle: '',
            removeTitle: '',
            buttonYes: '',
            buttonNo: '',
            buttonAdd: ''
        }
    },options);
    var main = this;

    // Перемещение на верх документа
    function scrollTop(){
        $('html, body').animate({
            scrollTop: 0
        }, 'slow');
    }
    
    // Отображение диалога удаления и удаление
    function removeDialog(value){
        $('#dialog').html(options.translation.removeOneConfirm);
        $('#dialog').attr('title', options.translation.removeOne);
        $('#dialog').dialog({
            resizable: false,
            autoOpen: false,
            hide: 'fade',
            buttons: [
            {
                text: options.translation.buttonYes,
                click: function() {
                    $.getJSON(options.removeUrl, {
                        'id': value
                    }, function(data){
                        $.getJSON(options.reloadUrl, function(tree){
                            options.data = tree;
                            rebuildTree();
                            showMsg(data.msg, data.type); 
                        });
                    });
                    $(this).dialog('close');
                }
            },   
            {
                text: options.translation.buttonNo,
                click: function() {
                    $(this).dialog("close");
                }
            }
            ]
        });
        $('#dialog').dialog('open');
    }
    
    function buildTree(parentId, depth){
        var added = 0;
        
        if (parentId == 0){
            added = 1;
            result = '<ol class="nestedSortable">';
        } else {
            result = '<ol>';
        }
        
        $(options.data).each(function(indx, val) {
            if ((val.parent_id == parentId) && (val.depth == depth)){
                added = 1;
                if (val.enable == 1) {
                    result = result + '<li id="list_' + val.id + '" itemid="' + val.id + '"><div>' + val.name + '<a href="#" class="remove"><img src="/design/cms/img/icons/delete.png" alt=""></a><a href="'+options.editUrl + val.id + '" class="edit"><img src="/design/cms/img/icons/edit.png" alt=""></a></div>' + buildTree(val.id, depth + 1) + '</li>';
                } else {
                    result = result + '<li id="list_' + val.id + '" itemid="' + val.id + '" class="disable"><div>' + val.name + '<a href="#" class="remove"><img src="/design/cms/img/icons/delete.png" alt=""></a><a href="'+options.editUrl + val.id + '" class="edit"><img src="/design/cms/img/icons/edit.png" alt=""></a></div>' + buildTree(val.id, depth + 1) + '</li>';
                }
            }
        });
        if (added > 0){
            return result + '</ol>';
        } else {
            return '';
        }
    }
    
    // Отображение информационного сообщения
    function showMsg(msg, type){
        if (msg.length){
            id = 0;
            do{
                id++;
            } while ($('#msg'+id).size() != 0);
            $(main).prepend('<div style="margin-bottom: 6px;" id="msg'+id+'" class="msg">'+msg+'</div>');
            $('#msg'+id).addClass(type).click(function() {
                $(this).fadeTo(350, 0);
                $(this).slideUp(350);   
            }).delay(options.msgShowTime).fadeTo(350, 0).slideUp(350);
            scrollTop();
        }
    }
    
    function saveData(){
        arraied = $(main).find('.nestedSortable').nestedSortable('toArray', {
            startDepthCount: -1
        });
        $.get(options.saveUrl, {
            data: arraied
        }, function(){});
    }
    
    function buildSortableTree(){
        $(main).find('.nestedSortable').nestedSortable($.extend(options.nestedSortable, {
            deactivate: function(){
                saveData();
            }
        }));
    }
    
    function rebuildTree(){
        $(main).html(buildTree(0, 0));
        buildSortableTree();
        $(main).find('.disable').fadeTo(0, 0.3);
        $('.nestedSortable .remove').click(function(){
            removeDialog($(this).parent().parent().attr('itemid'));
        });
    }
    
    function getNewId(val){
        id = 0;
        do{
            id++;
        } while ($('#'+val+'_'+id).size() != 0);
        
        return val+'_'+id;
    }
    
    var mainId = getNewId('rastortree');
    var addId = getNewId('add_item');
    $(main).before('<div id="' + mainId + '"></div>').after('<button id="'+ addId +'">'+options.translation.buttonAdd+'</button>');
    main = $('#' + mainId);
    
    $('#' + addId).click(function(){
        document.location = options.addUrl;
        return false;
    });
    
    $.getJSON(options.reloadUrl, function(tree){
        options.data = tree;
        rebuildTree();
        if (options.startMsg.msg.length){
            showMsg(options.startMsg.msg, options.startMsg.type);
        }
    });
};