/* ҳ����ʼ�� */
jQuery(document).ready(function() {
	/* ��ק���� */
	jQuery( ".pmtool-card-list" ).sortable({
		connectWith: ".pmtool-card-list",
		handle: ".card-header",
		cancel: ".card-toggle",
		placeholder: "card-placeholder ui-corner-all"
    });
	jQuery( ".card-container" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" );

	/* ��Ƭ�༭ */
	//card-content

	jQuery(".card-content").click(function() {
		debugger;
		jQuery.fancybox.open({
			href : '/popView.html',
			type : 'iframe',
			padding : 5
		});
	});
});
