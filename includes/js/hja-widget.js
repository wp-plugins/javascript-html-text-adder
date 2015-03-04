jQuery(document).ready(function() {
	
	jQuery('.hjaAccord').live('click', function(){
		jQuery(this).parent().children('.hjaAccordWrap').hide();
		jQuery(this).next('.hjaAccordWrap').show();
	});
	
	jQuery('.hjaTb').live('click', function(){
		var cntId = jQuery(this).parent().attr('editorId');
		var openTag = jQuery(this).attr('openTag');
		var closeTag = jQuery(this).attr('closeTag');
		var action = jQuery(this).attr('action');
		return awQuickTags(cntId, openTag, closeTag, action);
	});
	
	jQuery('.hjaTb-preview').live('click', function(){
		var editId = jQuery(this).attr('editorId');
		hjaOpenPopup(jQuery('#' + editId).val());
	});
	
	jQuery('.hjaOverlayClose').click(function(){
		jQuery(this).parent().fadeOut('fast');
	});
	
});

function hjaOpenPopup(content){
	
	hjaIframe.document.open();
	hjaIframe.document.write(content);
	hjaIframe.document.close();
	
	jQuery('.hjaWindow').fadeIn('fast');
}