<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">CloudHost</h5>
                <p>Penyedia layanan cloud & hosting terpercaya dengan uptime 99.9% untuk bisnis Anda.</p>
            </div>
            <div class="col-md-3">
                <h6>Kontak</h6>
                <ul class="list-unstyled">
                    <li>Email: support@cloudhost.id</li>
                    <li>Telp: +62 812-3456-7890</li>
                    <li>Alamat: Jakarta, Indonesia</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6>Ikuti Kami</h6>
                <div class="d-flex gap-3">
                    <a class="text-white" href="#"><i class="fab fa-facebook"></i></a>
                    <a class="text-white" href="#"><i class="fab fa-instagram"></i></a>
                    <a class="text-white" href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 small">&copy; <?php echo date('Y'); ?> CloudHost. All rights reserved.</div>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const countdownElement = document.getElementById('promo-countdown');
        if (countdownElement) {
            const deadlineAttribute = countdownElement.getAttribute('data-deadline');
            const deadline = deadlineAttribute ? new Date(deadlineAttribute) : null;
            if (deadline instanceof Date && !isNaN(deadline)) {
                const updateCountdown = () => {
                    const now = new Date();
                    const diff = deadline.getTime() - now.getTime();
                    if (diff <= 0) {
                        countdownElement.textContent = '00h : 00m : 00s';
                        return;
                    }
                    const totalSeconds = Math.floor(diff / 1000);
                    const days = Math.floor(totalSeconds / 86400);
                    const hours = Math.floor((totalSeconds % 86400) / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;
                    const totalHours = days * 24 + hours;
                    countdownElement.textContent = `${String(totalHours).padStart(2, '0')}h : ${String(minutes).padStart(2, '0')}m : ${String(seconds).padStart(2, '0')}s`;
                };
                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
        }
    });
</script>
</body>
</html>
