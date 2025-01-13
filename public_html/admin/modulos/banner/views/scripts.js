// $(document).ready(function(){
//     $('.banner_item a').mousemove(function(e){

//         var x = ((10/100) * Math.round((100/this.offsetWidth)*(e.pageX - this.offsetLeft))) * -1;
//         var y = ((10/100) * Math.round((100/this.offsetHeight)*(e.pageY - this.offsetTop))) * -1;

//         if( $(this).attr("livre")==1 )
//         {
//             $(this).find("img").css({'margin-left':x+'px','margin-top':y+'px'});// + y + 'px'
//         }
//     });
//     $('.banner_item a').mouseenter(function(e){
//         var x = ((10/100) * Math.round((100/this.offsetWidth)*(e.pageX - this.offsetLeft))) * -1;
//         var y = ((10/100) * Math.round((100/this.offsetHeight)*(e.pageY - this.offsetTop))) * -1;
//         $(this).attr("livre",0);
//         $(this).find("img").stop().animate({width:483,height:224,marginLeft:x,marginTop:y}, 'fast',function(){
//             $(this).parent().parent().attr("livre",1);
//         });

//     });

//     $('.banner_item a').mouseleave(function(e){
//         $(this).attr("livre",0);
//         $(this).find("img").animate({width:463,height:215,marginTop:0,marginLeft:0}, 'fast');
//     });
// });