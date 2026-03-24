// Print receipt - Fixed version
async function printReceipt(order, change) {
    // Get receipt settings
    let receiptSettings = {
        tax_percent: 10,
        service_percent: 5,
        outlet: {
            name: 'RESTAURANT',
            address: '',
            phone: ''
        },
        template: {
            footer_text: 'Thank you for your visit!'
        }
    };

    try {
        const response = await fetch('/php-native/api/settings/receipt-settings.php');
        const data = await response.json();
        if (data.success && data.template) {
            receiptSettings = {
                template: data.template,
                tax_percent: data.tax_percent || 10,
                service_percent: data.service_percent || 5,
                outlet: data.outlet || {}
            };
        }
    } catch (error) {
        console.error('Error loading receipt settings:', error);
    }

    // Build items HTML
    let itemsHtml = '';
    let subtotal = 0;

    cart.forEach(item => {
        const itemTotal = (item.price || 0) * (item.quantity || 1);
        subtotal += itemTotal;

        // Get notes and modifiers as array
        let allNotes = [];
        if (item.notes && Array.isArray(item.notes)) {
            allNotes = allNotes.concat(item.notes);
        }
        if (item.modifiers && Array.isArray(item.modifiers)) {
            item.modifiers.forEach(m => {
                if (typeof m === 'string') allNotes.push(m);
                else if (m && m.name) allNotes.push(m.name);
            });
        }

        const notesText = allNotes.length > 0 ? '[' + allNotes.join(', ') + ']' : '';

        itemsHtml += '<tr>';
        itemsHtml += '<td colspan="2" style="text-align: left; padding: 5px 0; font-weight: bold;">';
        itemsHtml += (item.name || 'Item');
        if (item.is_voided) itemsHtml += ' <span style="color: red;">[VOIDED]</span>';
        itemsHtml += '</td>';
        itemsHtml += '</tr>';

        itemsHtml += '<tr>';
        itemsHtml += '<td style="text-align: left; padding: 3px 0; padding-left: 15px; font-size: 11px;">';
        itemsHtml += (item.quantity || 1) + 'x @ Rp ' + (item.price || 0).toLocaleString('id-ID');
        itemsHtml += '</td>';
        itemsHtml += '<td style="text-align: right; padding: 3px 0;">Rp ' + itemTotal.toLocaleString('id-ID') + '</td>';
        itemsHtml += '</tr>';

        if (notesText) {
            itemsHtml += '<tr>';
            itemsHtml += '<td colspan="2" style="text-align: left; padding: 3px 0; padding-left: 15px; font-size: 9px; color: #666; font-style: italic;">';
            itemsHtml += notesText;
            itemsHtml += '</td>';
            itemsHtml += '</tr>';
        }
    });

    const tax = subtotal * (receiptSettings.tax_percent / 100);
    const service = subtotal * (receiptSettings.service_percent / 100);
    const total = subtotal + tax + service;

    const printWindow = window.open('', '_blank');
    const receiptHtml = '<!DOCTYPE html>' +
        '<html><head><title>Receipt #' + (order.id || '') + '</title>' +
        '<style>' +
        '@media print { body { margin: 0; padding: 0; } .no-print { display: none; } }' +
        'body { font-family: "Courier New", monospace; font-size: 11px; }' +
        '.receipt { width: 58mm; padding: 10px; margin: 0 auto; }' +
        '.text-center { text-align: center; } .text-right { text-align: right; }' +
        'table { width: 100%; border-collapse: collapse; } td { padding: 2px 0; }' +
        '.total-row { font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; }' +
        '.grand-total { font-size: 13px; }' +
        '</style></head><body>' +
        '<div class="receipt">' +
        '<div class="text-center">' +
        '<h3 style="margin: 5px 0;">' + (receiptSettings.outlet.name || 'RESTAURANT') + '</h3>' +
        '<p style="margin: 3px 0; font-size: 10px;">' + (receiptSettings.outlet.address || '') + '</p>' +
        '<p style="margin: 3px 0; font-size: 10px;">Telp: ' + (receiptSettings.outlet.phone || '') + '</p>' +
        '</div>' +
        '<hr style="border-top: 1px dashed #000;">' +
        '<table>' +
        '<tr><td>Order #: ' + (order.id || '-') + '</td><td class="text-right">Table: ' + (order.table_name || '-') + '</td></tr>' +
        '<tr><td>Date: ' + new Date().toLocaleString('id-ID') + '</td><td class="text-right">Cashier: Staff</td></tr>' +
        '</table>' +
        '<hr style="border-top: 1px dashed #000;">' +
        '<table>' + itemsHtml + '</table>' +
        '<hr style="border-top: 1px dashed #000;">' +
        '<table>' +
        '<tr><td>Subtotal:</td><td class="text-right">Rp ' + subtotal.toLocaleString('id-ID') + '</td></tr>' +
        (receiptSettings.service_percent > 0 ? '<tr><td>Service (' + receiptSettings.service_percent + '%):</td><td class="text-right">Rp ' + service.toLocaleString('id-ID') + '</td></tr>' : '') +
        (receiptSettings.tax_percent > 0 ? '<tr><td>Tax (' + receiptSettings.tax_percent + '%):</td><td class="text-right">Rp ' + tax.toLocaleString('id-ID') + '</td></tr>' : '') +
        '<tr class="total-row grand-total"><td>TOTAL:</td><td class="text-right">Rp ' + total.toLocaleString('id-ID') + '</td></tr>' +
        '<tr><td>Paid:</td><td class="text-right">Rp ' + (order.paid_amount || total).toLocaleString('id-ID') + '</td></tr>' +
        '<tr><td>Change:</td><td class="text-right">Rp ' + (change || 0).toLocaleString('id-ID') + '</td></tr>' +
        '</table>' +
        '<hr style="border-top: 1px dashed #000;">' +
        '<p class="text-center" style="margin: 10px 0; font-size: 10px;">' + (receiptSettings.template.footer_text || 'Thank you for your visit!') + '</p>' +
        '</div>' +
        '<script>window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 500); };<\/script>' +
        '</body></html>';

    printWindow.document.write(receiptHtml);
    printWindow.document.close();
}
