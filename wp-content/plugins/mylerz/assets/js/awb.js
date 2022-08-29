var viewAWB = (awb, canvasId) => {
    var pdfData = atob(awb);

    // Loaded via <script> tag, create shortcut to access PDF.js exports.
    // var pdfjsLib = window['pdfjs-dist/build/pdf'];

    // The workerSrc property shall be specified.
    // pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

    // Using DocumentInitParameters object to load binary data.
    var loadingTask = pdfjsLib.getDocument({ data: pdfData });
    loadingTask.promise.then(pdf => {
        console.log('PDF loaded');

        // Fetch the first page
        var pageNumber = 1;
        pdf.getPage(pageNumber).then(page => {
            console.log('Page loaded');

            var scale = 1;
            var viewport = page.getViewport({ scale: scale });

            // Prepare canvas using PDF page dimensions
            var canvas = document.getElementById(canvasId);
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Render PDF page into canvas context
            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);
            renderTask.promise.then(function() {
                console.log('Page rendered');
            });
        });
    }, function(reason) {
        // PDF loading error
        console.error(reason);
    });
}



var printAWB = () => {
    var divContents = document.getElementById("canvasDiv").childNodes;

    var a = window.open('', '', 'height=500, width=500');
    a.document.write('<html>');
    a.document.write('<body >');

    let imageList = Array.from(divContents).slice(1).map(canvas => {
        let img = new Image();
        img.src = canvas.toDataURL();
        img.style.display = 'inline';
        // canvas.style.display = 'none';
        return img;
    });

    imageList.forEach(img => {
        a.document.body.appendChild(img);
    })
    a.document.write('</body></html>');
    a.document.close();
    a.print();
    a.close();
}


var test = ()=>{
    console.log("koko");
}