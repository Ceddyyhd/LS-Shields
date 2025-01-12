$(document).ajaxSend(function(event, xhr, settings) {
    if (typeof ajaxToken !== 'undefined' && settings.url.startsWith('/admin/include/')) {
        xhr.setRequestHeader('X-AJAX-TOKEN', ajaxToken);
    } else {
        console.warn('Ajax-Token ist nicht definiert.');
    }
});
