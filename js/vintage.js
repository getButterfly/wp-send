document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('file')) {
        let uploadField = document.getElementById('file');

        uploadField.onchange = () => {
            if (this.files[0].size > ' . $limit_bytes .') {
                this.value = '';

                document.getElementById('wp-send-filesize-error').style.display = 'block';
            } else {
                document.getElementById('wp-send-filesize-error').style.display = 'none';
            }
        };
    }

    if (document.getElementById('selected-file')) {
        const options = {
            success: (files) => {
                let links = [];

                files.forEach((element) => {
                    links.push(element.link);

                    document.getElementById('selected-file').value = links.join(',');
                });
            },
            multiselect: true,
            //sizeLimit: 1024,
            //extensions: ["images"]
        };

        let button = Dropbox.createChooseButton(options); document.getElementById('droptarget').appendChild(button);
    }
});
