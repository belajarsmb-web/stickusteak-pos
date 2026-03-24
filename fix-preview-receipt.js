// Fix untuk function previewReceipt di settings-receipt-template.php
// Replace function previewReceipt yang lama dengan ini

        // Preview receipt
        function previewReceipt() {
            // Get values from new fields
            const restaurantName = document.getElementById('restaurantName').value || 'Your Restaurant';
            const restaurantAddress = document.getElementById('restaurantAddress').value || '';
            const restaurantPhone = document.getElementById('restaurantPhone').value || '';
            const footerText = document.getElementById('footerText').value || 'Thank you!';
            const website = document.getElementById('website').value || '';
            const socialMedia = document.getElementById('socialMedia').value || '';
            const fontSize = document.getElementById('fontSize').value;
            const showLogo = document.getElementById('showLogo').checked;
            const logoPath = currentLogoPath;

            const previewContainer = document.getElementById('receiptPreviewContainer');
            previewContainer.className = `receipt-preview ${fontSize}`;

            let html = '';

            // Logo
            if (showLogo && logoPath) {
                html += `<img src="${logoPath}" class="receipt-logo" style="max-width: 80px; max-height: 80px; margin: 0 auto 10px; display: block;">`;
            }

            // Header - Restaurant info
            html += `<div class="text-center" style="margin-bottom: 10px;">`;
            html += `<strong style="font-size: 13px;">${restaurantName}</strong><br>`;
            if (restaurantAddress) { html += `<span style="font-size: 10px;">${restaurantAddress}</span><br>`; }
            if (restaurantPhone) { html += `<span style="font-size: 10px;">Telp: ${restaurantPhone}</span><br>`; }
            html += `</div>`;
            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Sample items
            html += `<table style="width: 100%; font-size: 11px;">`;
            html += `<tr><td style="text-align: left;">1x Item Name</td><td style="text-align: right;">Rp 50,000</td></tr>`;
            html += `<tr><td style="text-align: left;">2x Another Item</td><td style="text-align: right;">Rp 100,000</td></tr>`;
            html += `</table>`;

            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Totals
            html += `<table style="width: 100%; font-size: 11px;">`;
            html += `<tr><td style="text-align: left;">Subtotal:</td><td style="text-align: right;">Rp 150,000</td></tr>`;

            if (document.getElementById('showService').checked) {
                html += `<tr><td style="text-align: left;">Service (5%):</td><td style="text-align: right;">Rp 7,500</td></tr>`;
            }

            if (document.getElementById('showTax').checked) {
                html += `<tr><td style="text-align: left;">Tax (10%):</td><td style="text-align: right;">Rp 15,000</td></tr>`;
            }

            html += `<tr style="font-weight: bold; font-size: 13px;"><td style="text-align: left;">TOTAL:</td><td style="text-align: right;">Rp 172,500</td></tr>`;
            html += `</table>`;

            html += `<hr style="border-top: 1px dashed #000; margin: 8px 0;">`;

            // Footer
            html += `<div class="text-center" style="margin-top: 10px; font-size: 10px;">`;
            if (footerText) { html += `<div>${footerText}</div>`; }
            if (website) { html += `<div>${website}</div>`; }
            if (socialMedia) { html += `<div>${socialMedia}</div>`; }
            html += `</div>`;

            previewContainer.innerHTML = html;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }
