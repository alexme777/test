
$(function () {
  $('.add-comment').click(function(){
    var editor = $('.editor');
    if (editor.is(":hidden")){
      editor.slideDown();
    }else{
      editor.slideUp();
    }
    return false;
  });
  
  $('.comment-ans').click(function(){
    var $editor = $('.editor');
    $editor.hide();
    var mid = $(this).attr("id");
    var clone = $editor.clone();
    $editor.remove();
    setTimeout(function(){
      $(clone).css("margin", "5px 0 5px 20px");
      $(clone).insertAfter("div#msg"+mid).slideDown();
      $("input[name=parent]").val(mid);
    }, 200);
  });
  
	$('.sort-elements').click(function(){
		var $elements = $($('.list-comments .alone-comment[data-level=0]').get().reverse());
		var $target = $('.list-comments');
		$elements.appendTo($target);
		return false;
	});
  
  
});
