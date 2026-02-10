function loadPDF(container, url) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = "pdfjs/build/pdf.worker.mjs";
    pdfjsLib.getDocument(url).promise.then(async pdf => {
        const containerWidth = container.clientWidth - 24;

        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            const page = await pdf.getPage(pageNum);

            // scale page to container width
            const viewport = page.getViewport({ scale: 1 });
            const scale = containerWidth / viewport.width;
            const scaledViewport = page.getViewport({ scale });

            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            canvas.width = scaledViewport.width;
            canvas.height = scaledViewport.height;

            // first page defines container height
            if (pageNum === 1) {
                container.style.height = `${scaledViewport.height + 24}px`;
            }
            container.appendChild(canvas);

            await page.render({
                canvasContext: ctx,
                viewport: scaledViewport
            }).promise;
        }
    });
}