<?php
/**
 * HTML Footer Template
 * Included at the bottom of every admin page
 */
?>
    </div>
    <!-- End Page Wrapper -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p>&copy; 2024 Laberion Workforce Management System. All rights reserved.</p>
            <p class="footer-links">
                <a href="#">Privacy Policy</a> | 
                <a href="#">Terms of Service</a> | 
                <a href="#">Support</a>
            </p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    
    <!-- Page-specific scripts (if needed) -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <style>
        .footer {
            background: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #374151;
        }
        
        .footer-content p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .footer-links a {
            color: #3b82f6;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #60a5fa;
            text-decoration: underline;
        }
    </style>
</body>
</html>