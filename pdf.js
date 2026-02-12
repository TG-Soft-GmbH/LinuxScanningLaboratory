function loadPDF(container, url, doneCallback) {
  pdfjsLib.GlobalWorkerOptions.workerSrc = "pdfjs/build/pdf.worker.mjs";
  pdfjsLib.getDocument(url).promise.then(async pdf => {
    let eid = $(container).closest('.exhibit').find('.eid').text();
    const containerWidth = container.clientWidth;

    if (pdf.numPages == 0) {
      $(container).text('PDF contains no pages.');
      triggerURL(`?path=${eid}&log=` + encodeURIComponent('PDF contains no pages.'));
    }
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
        container.style.height = `${scaledViewport.height - 1}px`;
        $(container).text('');
      }
      container.appendChild(canvas);

      await page.render({
        canvasContext: ctx,
        viewport: scaledViewport
      }).promise;

      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
      let numRelevantPixels = 0;
      let isEmpty = true;
      const tolerancePercent = 15;
      const amountPPM = 55;
      const maxPixelVal = 255;
      const tolerance = maxPixelVal * tolerancePercent / 100;
      for (let i = 0; i < imgData.length; i += 4) {
        if (imgData[i] < maxPixelVal - tolerance || imgData[i + 1] < maxPixelVal - tolerance || imgData[i + 2] < maxPixelVal - tolerance) {
          numRelevantPixels++;
          if (numRelevantPixels > canvas.width * canvas.height * amountPPM / 1000000) {
            isEmpty = false;
            break;
          }
        }
      }
      //console.log('Page', pageNum, 'IsEmpty', isEmpty, numRelevantPixels, canvas.width * canvas.height);

      const pdfwidth = (viewport.width * 25.4 / 72).toFixed(0);
      const pdfheight = (viewport.height * 25.4 / 72).toFixed(0);
      const format = detectISOAFormat(pdfwidth, pdfheight);
      let formatStr = format.match ? format.name : `(${format.name ? format.name : '>A0'})`;
      formatStr += ' ';
      if (format.orientation == 'P') {
        formatStr += 'Portrait';
      } else if (format.orientation == 'L') {
        formatStr += 'Landscape';
      } else {
        formatStr += 'Square';
      }

      function detectISOAFormat(widthMm, heightMm) {

      }
      let annotation = `Page ${pageNum}/${pdf.numPages} - ${pdfwidth}mm x ${pdfheight}mm - ${formatStr}`;
      if (isEmpty) annotation += ' - BLANK';
      ctx.font = "bold 18px sans-serif";
      ctx.strokeStyle = "white";
      ctx.fillStyle = "darkred";
      ctx.lineWidth = 4;
      ctx.strokeText(annotation, 5, 20);
      ctx.fillText(annotation, 5, 20);
      triggerURL(`?path=${eid}&log=` + encodeURIComponent(annotation));
    }
    triggerURL(`?path=${eid}&log=` + encodeURIComponent('END of analysis.'));
    doneCallback();
  });
}

function detectISOAFormat(widthMm, heightMm) {
  widthMm = +widthMm;
  heightMm = +heightMm;
  const isoASizes = [
    { name: "A10", s: 26, l: 37 },
    { name: "A9", s: 37, l: 52 },
    { name: "A8", s: 52, l: 74 },
    { name: "A7", s: 74, l: 105 },
    { name: "A6", s: 105, l: 148 },
    { name: "A5", s: 148, l: 210 },
    { name: "A4", s: 210, l: 297 },
    { name: "A3", s: 297, l: 420 },
    { name: "A2", s: 420, l: 594 },
    { name: "A1", s: 594, l: 841 },
    { name: "A0", s: 841, l: 1189 }
  ];
  function getTolerance(dim) {
    if (dim <= 150) return 1.5;
    if (dim <= 600) return 2.0;
    return 3.0;
  }
  let orientation;
  if (Math.abs(widthMm - heightMm) < 0.01) {
    orientation = 'LP';
  } else if (heightMm > widthMm) {
    orientation = 'P';
  } else {
    orientation = 'L';
  }
  const s = Math.min(widthMm, heightMm);
  const l = Math.max(widthMm, heightMm);
  for (const size of isoASizes) {
    const tolS = getTolerance(size.s);
    const tolL = getTolerance(size.l);
    const sMatches = Math.abs(s - size.s) <= tolS;
    const lMatches = Math.abs(l - size.l) <= tolL;
    if (sMatches && lMatches) {
      return {
        name: size.name,
        match: true,
        orientation
      };
    }
  }
  for (const size of isoASizes) {
    if (size.s >= s && size.l >= l) {
      return {
        name: size.name,
        match: false,
        orientation
      };
    }
  }
  return {
    name: '',
    match: false,
    orientation
  };
}
