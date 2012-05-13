/*
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011 Budjak Orest
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @version 0.87
 */

jQuery.fn.RastorTable = function(options){
    var options = jQuery.extend({
        sortable: false,
        orderEnabled: true,
        buttons: [],
        columns: ['id'],
        colWidth: [],
        data: [],
        pages: [],
        editLinks: [],
        viewLinks: [],
        msgShowTime: 3000,
        removeUrl: '',
        reloadUrl: '',
        sortUrl: '',
        requestParams: {},
        startMsg: {
            type: '', 
            msg: ''
        },
        onReloadStart: function() {},
        onReloadEnd: function() {},
        tranlation: {
            removeOne: '',
            removeOneConfirm: '',
            removeMany: '',
            removeManyConfirm: '',
            removeChecked: '',
            reload: '',
            saveChanges: '',
            viewTitle: '',
            editTitle: '',
            sortTitle: '',
            backToSort: '',
            removeTitle: '',
            buttonYes: '',
            buttonNo: ''
        }
    },options);
    var main = this;
    var table;
    var currentPage = 1;
    var order = -1;
    var orderDirection = 0;


    // Построение таблицы
    function buildTable(){
        $(main).append('<table><thead></thead><tbody></tbody></table>');
        table = $(main).find('table');

        if (options.buttons.indexOf('remove') != -1){
            $(table).find('thead').append('<tr></tr>').find('tr').disableSelection().append('<th><input type="checkbox" class="checkall" /></th>').find('th:last').css('width', '20px');
        } else {
            $(table).find('thead').append('<tr></tr>');
        }

        $(options.columns).each(function(indx, val) {
            if (options.colWidth[indx] != 0){
                last = $(table).find('thead tr').append('<th orderindex="'+indx+'">'+val+'</th>').find('th:last').css('width',options.colWidth[indx]).css('text-align', 'center');
            } else {
                last = $(table).find('thead tr').append('<th orderindex="'+indx+'">'+val+'</th>').find('th:last');
            }
            if (options.orderEnabled){
                $(last).click(function(){
                    changeOrder(indx);
                });
            }
        });
        var bpwidth = 0;
        if (options.sortable){
            bpwidth = (1 + options.buttons.length)*25;
        } else {
            bpwidth = options.buttons.length*25;
        }
        $(table).find('thead tr').append('<th>&nbsp</th>').find('th:last').css('width', bpwidth+'px');
    }

    // Заполнение таблицы
    function fillTable(){
        $(table).find('tbody').empty();

        $(options.data).each(function(index, line) {
            tr = $(table).find('tbody').append('<tr></tr>').find('tr:last');
                
            var id = 0;
            $(line).each(function(indx, item) {
                if (indx == 0){
                    id = item;
                }
                $(tr).append('<td>' + item + '</td>');

                if (options.colWidth[indx] != 0){
                    $(tr).find('td:last').css('text-align', 'center');
                }
            });

            if (options.buttons.indexOf('remove') != -1){
                $(tr).prepend('<td><input type="checkbox" elementid="'+id+'" /></td>');
            }

            var buttonsBar = '';
            if (options.sortable){
                buttonsBar += '<img class="move" src="/design/cms/img/icons/move.png" alt="'+options.translation.sortTitle+'" title="'+options.translation.sortTitle+'" />';
            }
            $(options.buttons).each(function(indx, button) {
                if (button == 'view'){
                    buttonsBar += '<a href="' + options.viewLinks[index] + '"><img src="/design/cms/img/icons/go.png" alt="'+options.translation.viewTitle+'" title="'+options.translation.viewTitle+'" /></a>'
                }
                if (button == 'edit'){
                    buttonsBar += '<a href="' + options.editLinks[index] + '"><img src="/design/cms/img/icons/edit.png" alt="'+options.translation.editTitle+'" title="'+options.translation.editTitle+'" /></a>'
                }
                if (button == 'remove'){
                    buttonsBar += '<a href="#" class="remove"><img src="/design/cms/img/icons/delete.png" alt="'+options.translation.removeTitle+'" title="'+options.translation.removeTitle+'" /></a>'
                }
            });
            $(tr).append('<td>' + buttonsBar + '</td>').find('.remove').click(function(){
                removeDialog(id);
                return false;
            });
        });

        $(table).find('.checkall').click(function(){
            if ($(this).is(':checked')){
                $(table).find('tbody :checkbox').attr('checked', true);
            } else {
                $(table).find('tbody :checkbox').attr('checked', false);
            }
        });
        
        if ((options.pages) && (options.pages.pageCount > 1)){
            currentPage = options.pages.current;
            pages = $(main).find('.tablefooter .actions.right .pages').empty();
            $(pages).html('Страница: ' + options.pages.current + ' из ' + options.pages.pageCount + '&nbsp;&nbsp;');
                
            $(pages).append('<button>&lt;&lt;</button>');
            if (currentPage == 1){
                $(pages).find('button:last').button({
                    disabled : true
                });
            } else {
                $(pages).find('button:last').button().click(function(){
                    gotoPage(1);
                });
            }
                
            $(pages).append('<button>&lt;</button>');
            if (options.pages.previous){
                $(pages).find('button:last').button().click(function(){
                    gotoPage(options.pages.previous);
                });
            } else {
                $(pages).find('button:last').button({
                    disabled : true
                });
            }
                
            if (options.pages.pagesInRange[0] > 1){
                $(pages).append('<button>..</button>');
            }
                
            $(options.pages.pagesInRange).each(function(indx, value) {
                $(pages).append('<button>'+value+'</button>');
                if (value == currentPage){
                    $(pages).find('button:last').button({
                        disabled : true
                    });
                } else {
                    $(pages).find('button:last').button().click(function(){
                        gotoPage(value);
                    });
                }
            });
                
            if (options.pages.pagesInRange[options.pages.pagesInRange.length - 1] < options.pages.last){
                $(pages).append('<button>..</button>');
            }
            $(pages).append('<button>&gt;</button>');
            if (options.pages.next){
                $(pages).find('button:last').button().click(function(){
                    gotoPage(options.pages.next);
                });
            } else {
                $(pages).find('button:last').button({
                    disabled : true
                });
            }
            $(pages).append('<button>&gt;&gt;</button>');
            if (currentPage == options.pages.last){
                $(pages).find('button:last').button({
                    disabled : true
                });
            } else {
                $(pages).find('button:last').button().click(function(){
                    gotoPage(options.pages.last);
                });
            }
            $(pages).buttonset();
        }
    }
    
    function changeOrder(number){
        if (number == order){
            if (orderDirection == 0){
                orderDirection = 1;
                element = $(table).find('thead tr th[orderindex='+number+']');
                $(table).find('thead tr th span').remove();
                $(element).append('<span>&#9660;</span>');
            } else {
                orderDirection = 0;
                element = $(table).find('thead tr th[orderindex='+number+']');
                $(table).find('thead tr th span').remove();
                $(element).append('<span>&#9650;</span>');
            }
        } else {
            order = number;
            orderDirection = 0;
            element = $(table).find('thead tr th[orderindex='+number+']');
            $(table).find('thead tr th span').remove();
            $(element).append('<span>&#9650;</span>');
        }
        if (options.sortable){
            $(main).find(".savechanges").button({
                label: options.translation.backToSort,
                disabled : false
                
            }).unbind('click').click(function() {
                orderDirection = 0;
                order = -1;
                $(table).find('thead tr th span').remove();
                $(".savechanges").button({
                    label: options.translation.saveChanges,
                    disabled: true
                }).unbind('click').click(function(){
                    saveChanges();
                });
                reloadTable();
            });
        }
        reloadTable();
    }
    
    function setPage(number){
        currentPage = number;
    }
    
    function gotoPage(number){
        setPage(number);
        reloadTable();
    }

    function reloadTable(callback){
        $(main).find('.load').show();
        if (options.sortable) {
            sort = 1;
        } else {
            sort = 0;
        }
        
        $.getJSON(options.reloadUrl, {
            'page': currentPage,
            'order': order,
            'orderdirection': orderDirection,
            'sort': sort,
            'requestparams': options.requestParams
        }, function(tabledata){
            options.data = tabledata.data;
            options.viewLinks = tabledata.viewLinks;
            options.editLinks = tabledata.editLinks;
            options.pages = tabledata.pages;
            fillTable();
            if (callback) {
                callback();
                options.onReloadEnd();
            }
            $(main).find('.load').hide();
        });
    };

    function saveChanges() {
        sortData = Array();
        $(table).find('tbody').find('input[type=checkbox]').each(function(indx, el){
            sortData[indx] = $(el).attr('elementid');
        });
        $(main).find('.savechanges').button({
            disabled : true
        });
        $.getJSON(options.sortUrl, {
            'ids': sortData
        }, function(data){
            reloadTable();
        });
    }

    // Перемещение на верх документа
    function scrollTop(){
        $('html, body').animate({
            scrollTop:0
        }, 'slow');
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

    // Отображение диалога удаления и удаление
    function removeDialog(values){
        if ($(values).size() > 1){
            $('#dialog').html(options.translation.removeManyConfirm);
            $('#dialog').attr('title', options.translation.removeMany);
        } else {
            $('#dialog').html(options.translation.removeOneConfirm);
            $('#dialog').attr('title', options.translation.removeOne);
        }
        $('#dialog').dialog({
            resizable: false,
            autoOpen: false,
            hide: 'fade',
            buttons: [
            {
                text: options.translation.buttonYes,
                click: function() {
                    $.getJSON(options.removeUrl, {
                        'ids': values
                    }, function(data){
                        reloadTable(function(){
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
        $(table).find('.checkall').attr('checked', false);
    }

    // Построение нижней панели
    function buildFooter(){
        $(main).append('<div class="tablefooter"><div class="actions"><button class="button reload">'+options.translation.reload+'</button></div><img src="/design/cms/img/loading.gif" class="load" alt="loading" /></div>');
        if (options.buttons.indexOf('remove') != -1){
            $(main).find('.tablefooter .actions').prepend('<button class="button removeButton">'+options.translation.removeChecked+'</button>&nbsp;&nbsp;');
        }
        $(main).find('.tablefooter').append('<div class="actions right"><div class="pages"></div></div>');
        if (options.sortable){
            $(main).find('.tablefooter .actions.right').append('<button class="button savechanges">'+options.translation.saveChanges+'</button>');
            $(main).find('.savechanges').button({
                disabled : true
            });
        }
        $(main).find('.button').button();

        $(main).find('.removeButton').click(function(){
            if ($(table).find('tbody').find(':checked').size() > 0){
                deleteData = Array();
                $(table).find('tbody').find(':checked').each(function(indx, el){
                    deleteData[indx] = $(el).attr('elementid');
                });
                removeDialog(deleteData);
            }
            return false;
        });

        $(main).find('.load').hide();
    }

    buildTable();
    buildFooter();
    reloadTable();

    if (options.sortable){
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };			

        $(this).find('tbody').sortable({
            handle: '.move',
            helper: fixHelper,
            change: function(event, ui){
                $(main).find('.savechanges').button('enable');
            }
        }).disableSelection();

        $(".savechanges").unbind('click').click(function() {
            saveChanges();
        });
    }

    if (options.startMsg.msg.length){
        showMsg(options.startMsg.msg, options.startMsg.type);
    }

    $(".reload").click(function() {
        reloadTable();
        return false;
    });
};