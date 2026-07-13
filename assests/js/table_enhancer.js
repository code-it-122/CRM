document.addEventListener('DOMContentLoaded', function() {
    // Find all tables inside .view classes (which contain our listings)
    const tables = document.querySelectorAll('.view table, .view-attendance table, .view-leave table, table.table-container');
    tables.forEach(table => {
        enhanceTable(table);
    });
});

function enhanceTable(table) {
    if (!table) return;

    // Check if tbody exists, if not use the table itself
    const tbody = table.querySelector('tbody') || table;
    const originalRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('th'));
    
    // Skip if no rows or only empty state row
    if (originalRows.length <= 1 && originalRows[0] && originalRows[0].cells.length <= 1) {
        return;
    }
    
    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredRows = [...originalRows];
    let currentSortHeader = null;
    let currentSortAsc = true;
    
    // Add Bootstrap table styling classes
    table.className = "table table-striped table-hover align-middle mb-0";
    
    // Create modern wrapper card
    const tableCard = document.createElement('div');
    tableCard.className = 'card border-0 shadow-sm rounded-3 mb-4 overflow-hidden';
    
    const cardHeader = document.createElement('div');
    cardHeader.className = 'card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2';
    
    const searchContainer = document.createElement('div');
    searchContainer.className = 'input-group w-auto';
    searchContainer.innerHTML = `
        <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 6px 0 0 6px;"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" class="form-control bg-light border-start-0 ps-0 search-input" placeholder="Search records..." style="max-width: 250px; border-radius: 0 6px 6px 0; font-size: 0.9rem;">
    `;
    
    cardHeader.appendChild(searchContainer);
    
    const tableResponsive = document.createElement('div');
    tableResponsive.className = 'table-responsive';
    
    const cardFooter = document.createElement('div');
    cardFooter.className = 'card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2';
    
    // Wrap table
    table.parentNode.insertBefore(tableCard, table);
    tableCard.appendChild(cardHeader);
    tableCard.appendChild(tableResponsive);
    tableResponsive.appendChild(table);
    tableCard.appendChild(cardFooter);
    
    const searchInput = searchContainer.querySelector('.search-input');
    
    function render() {
        const query = searchInput.value.toLowerCase().trim();
        filteredRows = originalRows.filter(row => {
            return row.innerText.toLowerCase().includes(query);
        });
        
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        // Hide/show rows
        originalRows.forEach(row => row.style.display = 'none');
        filteredRows.slice(start, end).forEach(row => row.style.display = '');
        
        // Render footer pagination
        const displayStart = filteredRows.length === 0 ? 0 : start + 1;
        const displayEnd = Math.min(end, filteredRows.length);
        
        cardFooter.innerHTML = `
            <div class="text-muted" style="font-size: 0.85rem;">
                Showing <span class="fw-semibold">${displayStart}</span> to <span class="fw-semibold">${displayEnd}</span> of <span class="fw-semibold">${filteredRows.length}</span> entries
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link text-success border-0 px-3 py-2 bg-light rounded-circle me-1" href="#" data-page="${currentPage - 1}"><i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i></a>
                    </li>
                    ${Array.from({length: totalPages}, (_, i) => i + 1).map(page => `
                        <li class="page-item ${currentPage === page ? 'active' : ''}">
                            <a class="page-link ${currentPage === page ? 'bg-success text-white border-0 rounded-circle px-3 py-2 me-1 fw-bold' : 'text-success border-0 px-3 py-2 bg-light rounded-circle me-1'}" href="#" data-page="${page}">${page}</a>
                        </li>
                    `).join('')}
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                        <a class="page-link text-success border-0 px-3 py-2 bg-light rounded-circle" href="#" data-page="${currentPage + 1}"><i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i></a>
                    </li>
                </ul>
            </nav>
        `;
        
        // Bind page click events
        cardFooter.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page >= 1 && page <= totalPages) {
                    currentPage = page;
                    render();
                }
            });
        });
    }
    
    // Search listener
    searchInput.addEventListener('input', function() {
        currentPage = 1;
        render();
    });
    
    // Sort logic
    const headers = table.querySelectorAll('thead th, tr:first-child th');
    headers.forEach((header, index) => {
        // Skip action headers
        if (header.innerText.toLowerCase().includes('action') || header.getAttribute('colspan')) return;
        
        header.style.cursor = 'pointer';
        header.classList.add('user-select-none', 'pe-4', 'position-relative');
        
        const sortIcon = document.createElement('span');
        sortIcon.className = 'position-absolute end-0 top-50 translate-middle-y me-2 text-muted opacity-50';
        sortIcon.innerHTML = '<i class="fa-solid fa-sort" style="font-size: 0.75rem;"></i>';
        header.appendChild(sortIcon);
        
        header.addEventListener('click', function() {
            if (currentSortHeader === header) {
                currentSortAsc = !currentSortAsc;
            } else {
                currentSortHeader = header;
                currentSortAsc = true;
            }
            
            // Reset other icons
            headers.forEach(h => {
                const iconSpan = h.querySelector('span');
                if (iconSpan) iconSpan.innerHTML = '<i class="fa-solid fa-sort" style="font-size: 0.75rem;"></i>';
            });
            
            // Update current icon
            sortIcon.innerHTML = currentSortAsc 
                ? '<i class="fa-solid fa-sort-up text-success" style="font-size: 0.85rem;"></i>' 
                : '<i class="fa-solid fa-sort-down text-success" style="font-size: 0.85rem;"></i>';
            
            originalRows.sort((a, b) => {
                let cellA = a.cells[index] ? a.cells[index].innerText.trim() : '';
                let cellB = b.cells[index] ? b.cells[index].innerText.trim() : '';
                
                // Parse numbers if applicable
                const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, ''));
                const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, ''));
                
                if (!isNaN(numA) && !isNaN(numB) && cellA.match(/^[$\d,. -]+$/)) {
                    return currentSortAsc ? numA - numB : numB - numA;
                }
                
                return currentSortAsc 
                    ? cellA.localeCompare(cellB, undefined, {numeric: true, sensitivity: 'base'}) 
                    : cellB.localeCompare(cellA, undefined, {numeric: true, sensitivity: 'base'});
            });
            
            originalRows.forEach(row => tbody.appendChild(row));
            render();
        });
    });
    
    render();
}
