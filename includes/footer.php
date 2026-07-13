<?php if (isset($_SESSION['user_id'])): ?>
    <footer class="footer mt-auto py-3 bg-white border-top text-center text-muted" style="margin-left: 260px; font-size: 0.85rem; transition: all 0.3s ease;">
        <div class="container-fluid">
            <span>&copy; <?php echo date('Y'); ?> CRM System. All rights reserved. &bull; Designed for Corporate Evaluation.</span>
        </div>
    </footer>
    
    <!-- Adjust footer positioning dynamically based on viewport -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateFooter() {
                const footer = document.querySelector('footer.footer');
                if (!footer) return;
                if (window.innerWidth < 992) {
                    footer.style.marginLeft = '0';
                    footer.style.width = '100%';
                } else {
                    footer.style.marginLeft = '260px';
                    footer.style.width = 'calc(100% - 260px)';
                }
            }
            window.addEventListener('resize', updateFooter);
            updateFooter();
        });
    </script>
<?php endif; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Client-side Table Enhancer JS -->
<script src="../assests/js/table_enhancer.js"></script>

<!-- Mobile Sidebar Toggler -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.querySelector('.admin_sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show-mobile-sidebar');
            });
            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show-mobile-sidebar');
                }
            });
        }
    });
</script>
</body>
</html>