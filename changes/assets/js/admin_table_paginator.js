/**
 * TechScout Universal Admin Table Paginator & High-Speed Filter Engine
 * Automatically paginates, filters, and spaces administrative data tables
 * with live search, page size selection, and responsive page navigation pills.
 */
(function() {
  'use strict';

  window.paginateAdminTable = function(tableId, options = {}) {
    const table = typeof tableId === 'string' ? document.getElementById(tableId) : tableId;
    if (!table) return null;

    if (table._tsPaginator) {
      table._tsPaginator.refresh();
      return table._tsPaginator;
    }

    if (!table.id) {
      table.id = 'ts_tbl_' + Math.random().toString(36).substring(2, 9);
    }

    const tbody = table.querySelector('tbody');
    if (!tbody) return null;

    let pageSize = options.pageSize || parseInt(table.getAttribute('data-page-size'), 10) || 10;
    let currentPage = 1;
    let searchInputId = options.searchInputId || table.getAttribute('data-search-id');
    let searchInput = searchInputId ? document.getElementById(searchInputId) : null;
    let allRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('no-paginate') && !tr.classList.contains('paginator-empty-row'));

    // If table explicitly says data-no-paginate="true", skip
    if (table.getAttribute('data-no-paginate') === 'true' || table.classList.contains('no-paginate')) {
      const existingPager = document.getElementById(table.id + '_paginator');
      if (existingPager) existingPager.remove();
      return null;
    }
    if (allRows.length === 0) {
      const existingPager = document.getElementById(table.id + '_paginator');
      if (existingPager) existingPager.remove();
      return null;
    }

    // Clean up any duplicate paginator elements in the DOM for this table
    const existingPagers = document.querySelectorAll('#' + table.id + '_paginator');
    let pagerWrap = existingPagers[0] || null;
    if (existingPagers.length > 1) {
      for (let i = 1; i < existingPagers.length; i++) existingPagers[i].remove();
    }

    if (!pagerWrap) {
      pagerWrap = document.createElement('div');
      pagerWrap.id = table.id + '_paginator';
      pagerWrap.className = 'admin-pagination-container';
      pagerWrap.style.display = 'none'; // Initially hidden until render confirms multiple pages or active search
      
      // Insert immediately after table within its own direct parent (never outside parent wrapper)
      table.insertAdjacentElement('afterend', pagerWrap);
    } else {
      // Ensure pagerWrap is right after table in DOM
      if (table.nextElementSibling !== pagerWrap) {
        table.insertAdjacentElement('afterend', pagerWrap);
      }
    }

    let filteredRows = [...allRows];
    let debounceTimer = null;

    function applyFilter() {
      const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
      if (!query) {
        filteredRows = [...allRows];
      } else {
        filteredRows = allRows.filter(row => row.innerText.toLowerCase().includes(query));
      }
      currentPage = 1;
      render();
    }

    function render() {
      const total = filteredRows.length;
      const totalPages = Math.max(1, Math.ceil(total / pageSize));
      if (currentPage > totalPages) currentPage = totalPages;

      const startIndex = (currentPage - 1) * pageSize;
      const endIndex = Math.min(startIndex + pageSize, total);

      // Hide all rows first
      allRows.forEach(row => row.style.display = 'none');

      // Show rows for current page
      for (let i = startIndex; i < endIndex; i++) {
        if (filteredRows[i]) {
          filteredRows[i].style.display = '';
        }
      }

      // Handle empty state row
      let emptyRow = tbody.querySelector('.paginator-empty-row');
      if (total === 0) {
        if (!emptyRow) {
          emptyRow = document.createElement('tr');
          emptyRow.className = 'paginator-empty-row';
          const colCount = table.querySelectorAll('thead th').length || 7;
          emptyRow.innerHTML = `<td colspan="${colCount}" style="text-align: center; padding: 3rem; color: #64748b; font-size: 0.9rem;">No matching telemetry entries found.</td>`;
          tbody.appendChild(emptyRow);
        }
        emptyRow.style.display = '';
      } else if (emptyRow) {
        emptyRow.style.display = 'none';
      }

      // If total entries fit on a single page and no filter query is active, suppress paginator UI completely
      const activeQuery = ((searchInput ? searchInput.value : '') || (pagerWrap.querySelector('.paginator-inline-search')?.value || '')).trim();
      if (allRows.length <= pageSize && !activeQuery) {
        pagerWrap.style.display = 'none';
        return;
      }
      pagerWrap.style.display = 'flex';

      // Render pagination controls
      const showingText = total > 0 
        ? `Showing <strong>${startIndex + 1}</strong> to <strong>${endIndex}</strong> of <strong style="color: var(--admin-primary);">${total}</strong> entries`
        : `No entries to show`;

      // Page size selector
      const sizeSelectorHtml = `
        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem;">
          <span>Rows:</span>
          <select class="paginator-size-select" style="padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer; outline: none;">
            <option value="10" ${pageSize === 10 ? 'selected' : ''}>10</option>
            <option value="15" ${pageSize === 15 ? 'selected' : ''}>15</option>
            <option value="25" ${pageSize === 25 ? 'selected' : ''}>25</option>
            <option value="50" ${pageSize === 50 ? 'selected' : ''}>50</option>
            <option value="100" ${pageSize === 100 ? 'selected' : ''}>100</option>
          </select>
        </div>
      `;

      // Inline search if no external search input
      let inlineSearchHtml = '';
      if (!searchInputId && !options.noInlineSearch) {
        const currentQueryVal = pagerWrap.querySelector('.paginator-inline-search')?.value || '';
        inlineSearchHtml = `
          <div style="display: flex; align-items: center; gap: 0.35rem;">
            <input type="text" class="paginator-inline-search" value="${currentQueryVal ? currentQueryVal.replace(/"/g, '&quot;') : ''}" placeholder="🔍 Quick filter table..." style="padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.8rem; width: 140px; outline: none;">
          </div>
        `;
      }

      // Page buttons
      let btnsHtml = `
        <button type="button" class="ts-btn ts-btn-outline ts-btn-sm paginator-prev" style="padding: 0.25rem 0.65rem; font-size: 0.8rem; ${currentPage === 1 ? 'opacity: 0.4; cursor: not-allowed;' : ''}" ${currentPage === 1 ? 'disabled' : ''}>
          &laquo; Prev
        </button>
      `;

      // Calculate numeric pill range
      let startPill = Math.max(1, currentPage - 2);
      let endPill = Math.min(totalPages, currentPage + 2);
      if (endPill - startPill < 4) {
        if (startPill === 1) endPill = Math.min(totalPages, startPill + 4);
        else if (endPill === totalPages) startPill = Math.max(1, endPill - 4);
      }

      if (startPill > 1) {
        btnsHtml += `<button type="button" class="ts-btn ts-btn-outline ts-btn-sm paginator-page-btn" data-page="1" style="padding: 0.25rem 0.55rem; font-size: 0.8rem;">1</button>`;
        if (startPill > 2) btnsHtml += `<span style="opacity: 0.5; padding: 0 0.25rem;">...</span>`;
      }

      for (let p = startPill; p <= endPill; p++) {
        const isActive = p === currentPage;
        btnsHtml += `
          <button type="button" class="ts-btn ${isActive ? 'ts-btn-primary' : 'ts-btn-outline'} ts-btn-sm paginator-page-btn" data-page="${p}" style="padding: 0.25rem 0.6rem; font-size: 0.8rem; font-weight: ${isActive ? '700' : '500'};">
            ${p}
          </button>
        `;
      }

      if (endPill < totalPages) {
        if (endPill < totalPages - 1) btnsHtml += `<span style="opacity: 0.5; padding: 0 0.25rem;">...</span>`;
        btnsHtml += `<button type="button" class="ts-btn ts-btn-outline ts-btn-sm paginator-page-btn" data-page="${totalPages}" style="padding: 0.25rem 0.55rem; font-size: 0.8rem;">${totalPages}</button>`;
      }

      btnsHtml += `
        <button type="button" class="ts-btn ts-btn-outline ts-btn-sm paginator-next" style="padding: 0.25rem 0.65rem; font-size: 0.8rem; ${currentPage === totalPages ? 'opacity: 0.4; cursor: not-allowed;' : ''}" ${currentPage === totalPages ? 'disabled' : ''}>
          Next &raquo;
        </button>
      `;

      pagerWrap.innerHTML = `
        <div style="font-size: 0.84rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
          <div>${showingText}</div>
          ${sizeSelectorHtml}
          ${inlineSearchHtml}
        </div>
        <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap;">
          ${btnsHtml}
        </div>
      `;


      // Attach event listeners
      pagerWrap.querySelector('.paginator-prev')?.addEventListener('click', () => {
        if (currentPage > 1) {
          currentPage--;
          render();
          table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      });

      pagerWrap.querySelector('.paginator-next')?.addEventListener('click', () => {
        if (currentPage < totalPages) {
          currentPage++;
          render();
          table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      });

      pagerWrap.querySelectorAll('.paginator-page-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const p = parseInt(this.getAttribute('data-page'), 10);
          if (p && p !== currentPage) {
            currentPage = p;
            render();
            table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          }
        });
      });

      pagerWrap.querySelector('.paginator-size-select')?.addEventListener('change', function() {
        pageSize = parseInt(this.value, 10) || 10;
        currentPage = 1;
        render();
      });

      const inlineInput = pagerWrap.querySelector('.paginator-inline-search');
      if (inlineInput) {
        inlineInput.addEventListener('input', function() {
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(() => {
            const q = inlineInput.value.toLowerCase().trim();
            if (!q) {
              filteredRows = [...allRows];
            } else {
              filteredRows = allRows.filter(row => row.innerText.toLowerCase().includes(q));
            }
            currentPage = 1;
            render();
          }, 150);
        });
      }
    }

    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilter, 150);
      });
    }

    // Initial render
    applyFilter();

    const instance = {
      refresh: function() {
        allRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => !tr.classList.contains('no-paginate') && !tr.classList.contains('paginator-empty-row'));
        applyFilter();
      },
      setPage: function(p) {
        currentPage = p;
        render();
      }
    };

    table._tsPaginator = instance;
    return instance;
  };

  // Auto-initialize all admin tables
  function initAllAdminTables() {
    document.querySelectorAll('table.admin-table:not([data-no-paginate="true"]), table[data-paginate="true"], table.admin-table-paginated').forEach(t => {
      const searchId = t.getAttribute('data-search-id');
      const size = parseInt(t.getAttribute('data-page-size'), 10) || 10;
      window.paginateAdminTable(t, { pageSize: size, searchInputId: searchId });
    });
  }

  window.refreshAdminTablePaginators = initAllAdminTables;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllAdminTables);
  } else {
    initAllAdminTables();
  }
})();
