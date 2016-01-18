<?php

?>
<script type="text/javascript">
    <!--
    function chooseImage(el, imgId, imgDir, XoopsUrl) {
        sel = el.options[el.selectedIndex];
        attributes = '';

        if (!sel.value) {
            alert('Please choose an option');
        } else {
            sel_split = sel.value.split('|');

            if (sel_split[1]) {
                attributes += ' width="' + sel_split[1] + '"'
            }
            if (sel_split[2]) {
                attributes += ' height="' + sel_split[2] + '"';
            }
            url = imgDir + "/" + sel_split[0];
            img = '<img src="' + url + '"' + attributes + ' alt="' + sel.innerHTML + '" />';
        }
        document.getElementById(imgId).innerHTML = img
    }

    function resizeImage(imageOrImageName, width, height) {
        var image = typeof imageOrImageName == 'string' ? document[imageOrImageName] : imageOrImageName;
        if (document.layers) {
            image.currentWidth = width;
            image.currentHeight = height;
            var layerWidth = image.width > width ? image.width : width;
            var layerHeight = image.height > height ? image.height : height;
            if (!image.overLayer) {
                var l = image.overLayer = new Layer(layerWidth);
            }
            var l = image.overLayer;
            l.bgColor = document.bgColor;
            l.clip.width = layerWidth;
            l.clip.height = layerHeight;
            l.left = image.x;
            l.top = image.y;
            var html = '';
            html += '<img src="' + image.src + '"';
            html += image.name ? ' name="overlayer' + image.name + '"' : '';
            html += ' width="' + width + '" height="' + height + '" />';
            l.document.open();
            l.document.write(html);
            l.document.close();
            l.visibility = 'show';
        } else {
            image.width = width;
            image.height = height;
        }
    }
    //-->
</script>
