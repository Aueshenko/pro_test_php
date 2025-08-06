document.addEventListener('DOMContentLoaded', function() {
    const catalogFilterForm = document.querySelector('.js-track-submit-filter');
    catalogFilterForm?.addEventListener('submit', function() {
        dataLayer.push({event: 'filter_applied'});
    });

    const exportCsv = document.querySelector('.js-track-export-csv');
    exportCsv?.addEventListener('click', function() {
        dataLayer.push({event: 'csv_export_clicked'});
    });

    const productEl = document.querySelector('.product-detail-container');
    if (productEl) {
        const productData = {
            id: productEl.dataset.productId,
            name: productEl.dataset.productName,
            price: parseFloat(productEl.dataset.productPrice),
            category: productEl.dataset.productCategory
        };
        dataLayer.push({
            event: 'view_product',
            product: productData
        });
    }
});