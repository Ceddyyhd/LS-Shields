$(document).ajaxSend(function(event, xhr, settings) {
    // Überprüfe, ob die Anfrage zu den sicheren Endpunkten gehört
    if (settings.url.startsWith('/admin/include/')) {
        // Füge das Token als zusätzlichen Parameter hinzu
        xhr.setRequestHeader('X-AJAX-TOKEN', ajaxToken);
    }
});
