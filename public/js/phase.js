      document.querySelectorAll('.progress-item').forEach(item => {
            const fullText = item.getAttribute('data-fulltext');
            const shortText = fullText.length > 6 ? fullText.slice(0, 6) + '...' : fullText;
            item.textContent = shortText;
            
            item.addEventListener('mouseover', () => {
                item.textContent = fullText;
                item.style.width = 'auto';
            });
            item.addEventListener('mouseout', () => {
                item.textContent = shortText;
                item.style.width = '';
            });
        });