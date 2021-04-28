window.onload = () => {
    let frame;
    const btnUploadImg = document.getElementById('mm-csi-btn-upload');
    const imgContainer = document.getElementById('mm-csi-image-container');
    const imgIdInput = document.querySelector('input[type="text"]#BG_IMG');

    console.log(imgIdInput);

    if(btnUploadImg) {
        btnUploadImg.onclick = (event) => {
            event.preventDefault();
    
            // If the media frame already exists, reopen it.
            if ( frame ) {
                frame.open();
                return;
            }
    
            // Create a new media frame
            frame = wp.media({
                title: 'Válassz egy képet',
                button: {
                    text: 'Use this media'
                },
                multiple: false
            });
    
            frame.on("select", () => {
                // Get media attachment details from the frame state
                var attachment = frame.state().get('selection').first().toJSON();
    
                // Send the attachment URL to our custom image input field.
                imgContainer.src = attachment.url;
                // Send the attachment id to our hidden input
                imgIdInput.value = attachment.url;
            });
    
            frame.open();
        }
    }
    
}