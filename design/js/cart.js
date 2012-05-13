function addProduct(id){
    $.get("/addproduct",{
        'id': id
    }, function(){
        $.get("/cartinfo", null, function(data){
            $("#cart_holder").html(data);
        });
    });
}
function deleteProduct(id){
    $.get("/deleteproduct",{
        'id': id
    }, function(){
        $.get("/cartinfo", null, function(data){
            $("#cart_holder").html(data);
        });
    });
}

function reload(){
    document.location = '/cart/';
}