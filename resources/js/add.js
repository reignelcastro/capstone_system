function reset(){
    $('#regCode').val('');
    $('.prov-container').html('<input id="provCode" type="text" class="provCode" autocomplete="off" readonly>');
    $('.cm-container').html('<input id="citymunCode" type="text" class="citymunCode"autocomplete="off" readonly>');
    $('.brgy-container').html('<input id="brgyCode" type="text" class="brgyCode" autocomplete="off" readonly>');
}
function region(_token,regCode,provCode,loader,route){
    $.ajax({
        url:route,
        method:'POST',
        data:{
            _token:_token,
            regCode:regCode
        },/*
        contentType:false,
        cache:false,
        processData:false,*/
        dataType:'json',
        beforeSend:function(){
            $('.prov-container').html(loader);
        },
        success:function(response){
            if(response.error)
            {
                alerts('error', response.error);
                reset();
            }
            else if(response.success)
            {
                $('.prov-container').html(response.html);
                $('#provCode').val(provCode);
            }
        }
    });
}
function province(_token,regCode,provCode,citymunCode,loader,route){
    $.ajax({
        url:route,
        method:'POST',
        data:{
            _token:_token,
            regCode:regCode,
            provCode:provCode
        },/*
        contentType:false,
        cache:false,
        processData:false,*/
        dataType:'json',
        beforeSend:function(){
            $('.cm-container').html(loader);
        },
        success:function(response){
            if(response.error)
            {
                alerts('error', response.error);
                reset();
            }
            else if(response.success)
            {
                $('.cm-container').html(response.html);
                $('#citymunCode').val(citymunCode);
            }
        }
    });
}

function cm(_token,regCode,provCode,citymunCode,brgyCode,loader,route){
    $.ajax({
        url:route,
        method:'POST',
        data:{
            _token:_token,
            regCode:regCode,
            provCode:provCode,
            citymunCode:citymunCode
        },/*
        contentType:false,
        cache:false,
        processData:false,*/
        dataType:'json',
        beforeSend:function(){
            $('.brgy-container').html(loader);
        },
        success:function(response){
            if(response.error)
            {
                alerts('error', response.error);
                reset();
            }
            else if(response.success)
            {
                $('.brgy-container').html(response.html);
                $('#brgyCode').val(brgyCode);
            }
        }
    });
}

function alerts(type, message){
    if(type === 'error')
    {
        $('#errors').html('<div class="alert alert-danger">'+message+'</div>');
    }
}
$(function(){
    var _token = $('meta[name="csrf-token"]').attr('content');
    var loader = $('#img_loader').val();

    var r1     = $('#r-1').val();
    var r2     = $('#r-2').val();
    var r3     = $('#r-3').val();
    var x_status    = $('#x-status').val();
    
    if(x_status === 'exists')
    {
        var x_regCode       = $('#x-regCode').val();
        var x_provCode      = $('#x-provCode').val();
        var x_citymunCode   = $('#x-citymunCode').val();
        var x_brgyCode      = $('#x-brgyCode').val();
        region(_token,x_regCode,x_provCode,loader,r1);
        province(_token,x_regCode,x_provCode,x_citymunCode,loader,r2);
        cm(_token,x_regCode,x_provCode,x_citymunCode,x_brgyCode,loader,r3);
        $('#regCode').val(x_regCode);
    }

        $(document).on('focus','#regCode',function(){
            $(this).val('');
            $('.prov-container').html('<input id="provCode" type="text" class="provCode" autocomplete="off" readonly>');
            $('.cm-container').html('<input id="citymunCode" type="text" class="citymunCode"autocomplete="off" readonly>');
            $('.brgy-container').html('<input id="brgyCode" type="text" class="brgyCode" autocomplete="off" readonly>');
        });
        $(document).on('change','#regCode',function(){
            $(this).trigger('blur');
            var regCode     = $(this).val();
            region(_token,regCode,'',loader,r1);
        });
        $(document).on('focus','#provCode',function(){
            $(this).val('');
            if ($('#regCode').val() === '' || $('#regCode').val() === null) 
            {
                alerts('error','SELECT REGION FIRST.');
            }

            $('.cm-container').html('<input id="citymunCode" type="text" class="citymunCode" autocomplete="off" readonly>');
            $('.brgy-container').html('<input id="brgyCode" type="text" class="brgyCode" name="brgyCode" autocomplete="off" readonly>');
        });
        $(document).on('change','#provCode',function(){
            $(this).trigger('blur');
            var regCode     = $('#regCode').val();
            var provCode     = $(this).val();
            province(_token,regCode,provCode,'',loader,r2);
        });


        $(document).on('focus','#citymunCode',function(){
            $(this).val('');
            if ($('#provCode').val() === '' || $('#provCode').val() === null) 
            {
                alerts('error','SELECT PROVINCE AND REGION FIRST.');
            }

            $('.brgy-container').html('<input id="brgyCode" type="text" class="brgyCode" name="brgyCode" autocomplete="off" readonly>');
        });
        $(document).on('change','#citymunCode',function(){
            $(this).trigger('blur');
            var regCode     = $('#regCode').val();
            var provCode     = $('#provCode').val();
            var citymunCode     = $(this).val();
            cm(_token,regCode,provCode,citymunCode,'',loader,r3);
        });

        $(document).on('focus','#brgyCode',function(){
            $(this).val('');
            if ($('#citymunCode').val() === '' || $('#citymunCode').val() === null) 
            {
                alerts('error','FOLLOW THE SEQUENCE OF SELECTION.');
                $('.brgy-container').html('<input id="brgyCode" type="text" class="brgyCode" name="brgyCode" autocomplete="off" readonly>');
            }
        });
});