/**
 * Admin scripts.
 *
 * @since 5.0.2
 */

jQuery(window).load(function() {
	setTimeout(function(){
		jQuery('.hm-html-editor textarea').each(function() {
			var cm = wp.codeEditor.initialize(jQuery(this));
		});
		jQuery('.hm-css-editor textarea').each(function() {
			var cm = wp.codeEditor.initialize(jQuery(this));
			cm.codemirror.setOption('mode', 'css');
		});
	}, 100);
});
