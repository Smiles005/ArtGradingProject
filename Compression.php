require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$pdf = new FPDI();
$pdf->AddPage();

// Import original PDF page
$pageId = $pdf->importPage(1);
$pdf->useTemplate($pageId, 0, 0);

// Add your JSON-rendered image
$pdf->Image('json_rendered.png', 0, 0, 210, 297); // A4 size //needs to be sized to the pdf instead as they are inconsistant

// Output compressed PDF
$pdf->SetCompression(true);
$pdf->Output('F', 'final.pdf');


      document.getElementById("save").addEventListener("click", () => {
        saveAnnotations();
        if (!currentFileName) return;

        // 1. Get PDF page size from the canvas
        const width = drawCanvas.width;
        const height = drawCanvas.height;

        // 2. Convert strokes → PNG
        const pngDataURL = renderAnnotationsToPNG(strokes, width, height);

        // 3. Send JSON + PNG to PHP
        const formData = new FormData();
        formData.append("fileName", currentFileName);
        formData.append("filePage", currentPage);
        formData.append("jsonStrokes", JSON.stringify(strokes));
        formData.append("pngData", pngDataURL);

        fetch("save.php", {
            method: "POST",
            body: formData,
        })

      });


//new

      function renderAnnotationsToPNG(strokes, width, height) {
          const canvas = document.createElement("canvas");
          canvas.width = width;
          canvas.height = height;

          const ctx = canvas.getContext("2d");
          ctx.clearRect(0, 0, width, height);

          strokes.forEach(item => {
              const tool = item.tool;
              const color = item.color || "#000000";+
              const size = item.size || 1;

              if (tool === "pen" && item.points && item.points.length > 1) {
                  ctx.strokeStyle = color;
                  ctx.lineWidth = size;
                  ctx.lineCap = "round";
                  ctx.lineJoin = "round";

                  ctx.beginPath();
                  const pts = item.points;
                  ctx.moveTo(pts[0].x, pts[0].y);
                  pts.forEach(p => ctx.lineTo(p.x, p.y));
                  ctx.stroke();
              }

              if (tool === "text") {
                  const text = item.text || "";
                  const p = item.points ? item.points[0] : { x: item.x, y: item.y };

                  if (text && p) {
                      ctx.fillStyle = color;
                      ctx.font = `${size}px Arial`;
                      ctx.fillText(text, p.x, p.y);
                  }
              }
          });

          return canvas.toDataURL("image/png");
      }