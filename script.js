const upload = document.getElementById("pdfUpload");
const pdfCanvas = document.getElementById("pdfCanvas");
const pdfCtx = pdfCanvas.getContext("2d");
const drawCanvas = document.getElementById("drawCanvas");

upload.addEventListener("change", function(e) {

    const file = e.target.files[0];

    if (!file || file.type !== "application/pdf") {
        alert("Please upload a PDF");
        return;
    }

    const reader = new FileReader();

    reader.onload = function() {

        const typedarray = new Uint8Array(this.result);

        pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {

            pdf.getPage(1).then(function(page) {

                const viewport = page.getViewport({ scale: 1 });

                // Resize both canvases
                pdfCanvas.width = viewport.width;
                pdfCanvas.height = viewport.height;

                drawCanvas.width = viewport.width;
                drawCanvas.height = viewport.height;

                const renderContext = {
                    canvasContext: pdfCtx,
                    viewport: viewport
                };

                page.render(renderContext);

            });

        });

    };

    reader.readAsArrayBuffer(file);
});