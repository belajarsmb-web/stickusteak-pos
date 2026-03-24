<?php
/**
 * Stickusteak POS - Premium Footer Include
 * Include file untuk footer dengan premium black & gold theme
 * 
 * Usage: <?php include __DIR__ . '/../includes/premium-footer.php'; ?>
 */
?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Page Specific JS -->
    <?php if (isset($custom_js)): ?>
        <script src="<?php echo htmlspecialchars($custom_js); ?>"></script>
    <?php endif; ?>
</body>
</html>
