pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

// Bloqueios
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    return false;
});
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey && (e.key === 's' || e.key === 'S')) || 
        (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i'))) {
        e.preventDefault();
        return false;
    }
});

const PDFViewer = {
    init: function(pdfUrl) {
        const container = document.getElementById('viewer');
        container.innerHTML = '';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.alignItems = 'center';
        container.style.padding = '20px';
        container.style.backgroundColor = '#e0e0e0';

        // Faz uma requisição fetch para obter o PDF como ArrayBuffer
        fetch(pdfUrl)
            .then(response => {
                if (!response.ok) throw new Error('Erro na requisição: ' + response.status);
                return response.arrayBuffer();
            })
            .then(data => {
                // Carrega o PDF a partir dos dados binários
                const loadingTask = pdfjsLib.getDocument({ data: data });
                return loadingTask.promise;
            })
            .then(pdf => {
                const totalPages = pdf.numPages;
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    pdf.getPage(pageNum).then(page => {
					const canvas = document.createElement('canvas');
					const context = canvas.getContext('2d');
					
					// Largura desejada em CSS (menos o padding do container)
					const containerWidth = container.clientWidth - 40;
					
					// Escala base para caber na largura
					const originalViewport = page.getViewport({ scale: 1 });
					const baseScale = containerWidth / originalViewport.width;
					
					// Obtém a densidade de pixels da tela (1 = normal, 2 = retina, 3 = 4K)
					const pixelRatio = window.devicePixelRatio || 1;
					
					// Escala final: baseScale * pixelRatio para renderizar em alta resolução
					const renderScale = baseScale * pixelRatio;
					const viewport = page.getViewport({ scale: renderScale });
					
					// Define o tamanho real do canvas em pixels (alta definição)
					canvas.width = viewport.width;
					canvas.height = viewport.height;
					
					// Aplica CSS para exibir o canvas no tamanho desejado (escala suavizada)
					canvas.style.width = `${containerWidth}px`;
					canvas.style.height = 'auto';
					
					canvas.style.marginBottom = '16px';
					canvas.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
					canvas.style.backgroundColor = 'white';
					container.appendChild(canvas);
					
					// Renderiza a página com o viewport de alta resolução
					page.render({ canvasContext: context, viewport: viewport });
				}).catch(error => console.error('Erro ao renderizar página:', error));
                }
            })
            .catch(error => {
                console.error('Erro ao carregar PDF:', error);
                container.innerHTML = '<p>Erro ao carregar o PDF.</p>';
            });
    }
};