$(function() {
    $(".group_menu_left_expand a").click(function(){
       // console.log($(this).parent().next());
        if($(this).parent().next().is(":hidden")){
            if($(".group_menu_left_expand a.active"))
            {
                $(".group_menu_left_expand a.active").parent().next().slideUp(500);
                $(".group_menu_left_expand a.active").removeClass('active');
            }
            $(this).removeClass('hidden').addClass('active').parent().next().slideDown(500);
        }
        else
        {
            $(this).removeClass('active').addClass('hidden');
            $(this).parent().next().slideUp(500);
        }
    });
});

$(function() {
    $('#column_left a#btCols_Menu_Left').hide();
    $('#column_left a#bt_Expand_Menu_Left').show();
    $('#column_left a#btCols_Menu_Left').click(function(){
        $('#column_right').animate({'margin-left':271},300,function(){
            $('body').css('background-position','0 0');
            $('#column_left').css('padding','10px 15px 10px 5px').animate({'width':250},300,function(){
                $('#column_left span.name_software').show();
                $('#column_left a#btCols_Menu_Left').hide();
                $('#column_left a#bt_Expand_Menu_Left').show();
                $('#column_left div.group_menu_left').fadeIn();
            });
        });
    });
    $('#column_left a#bt_Expand_Menu_Left').click(function(){
            $('#column_left div.group_menu_left').hide();
            $('#column_left').css('padding','0').animate({width:'10px'},300,function(){
            $('body').css('background-position','-260px 0');
            $('#column_left span.name_software').hide();
            $('#column_right').animate({'margin-left':11},300,function(){
                $('#column_left a#btCols_Menu_Left').show();
                $('#column_left a#bt_Expand_Menu_Left').hide();
            });
        });
    });
});
function Openpopup_Profile()
{
    var popurl='/ManagerDispatch/UserProfile.aspx'
    winpops=window.open(popurl,"","width=510,height=250,scrollbars,")
}