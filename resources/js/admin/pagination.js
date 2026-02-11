export default class Pagination {
  constructor(container, onPageChange) {
    this.container = container;
    this.onPageChange = onPageChange;
  }

  render(meta) {
    if (!meta?.links) return;
    this.container.innerHTML = meta.links.map(l => `
      <li class="page-item ${l.active ? 'active' : ''} ${!l.url ? 'disabled' : ''}">
        <a class="page-link" href="javascript:void(0)" data-url="${l.url || ''}">
          ${l.label}
        </a>
      </li>
    `).join('');

    this.container.querySelectorAll('.page-link').forEach(a => {
      const url = a.dataset.url;
      if (url) {
        a.addEventListener('click', () => this.onPageChange(url));
      }
    });
  }
}
