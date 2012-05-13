/*
 * GlobalVision CMS
 * @author Budjak Orest
 * @copyright 2011 Budjak Orest
 * @license http://www.php.net/license/3_01.txt PHP License 3.01
 * @version 1.0
 */

jQuery.fn.RastorMessager = function(options){
    var options = jQuery.extend({
        msgShowTime: 3000,
        data: {
            type: '', 
            msg: ''
        }
    }, options);

    var element = this;

    // Отображение информационного сообщения
    function showMsg(msg, type){
        var id = getNewId('msg');
        $(element).prepend('<div style="margin-bottom: 6px;" id="' + id + '" class="msg">'+msg+'</div>');
        $('#' + id).addClass(type).click(function() {
            $(this).fadeTo(350, 0);
            $(this).slideUp(350);   
        }).delay(options.msgShowTime).fadeTo(350, 0).slideUp(350);
    }

    function getNewId(val){
        var id = 0;
        do{
            id++;
        } while ($('#'+val+'_'+id).size() != 0);
        
        return val+'_'+id;
    }

    if (options.data.msg.length){
        showMsg(options.data.msg, options.data.type);
    }
};