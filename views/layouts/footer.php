<?php if (Session::isLoggedIn()): ?>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="row text-center align-items-center">
                    <div class="col-12">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                &copy; <?php echo date('Y'); ?> <a href="dashboard.php" class="link-secondary">BudgetPlanner</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
<?php else: ?>
    </div>
</div>
<?php endif; ?>

<!-- Tabler Core -->
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>

<!-- ApexCharts for Reports -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
</body>
</html>
