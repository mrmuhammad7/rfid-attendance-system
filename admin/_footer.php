</div><!-- /page-content -->
</main>
</div><!-- /layout -->

<footer>
  <div class="footer-content">
    <p>&copy; 2026 <span style="color: var(--accent);">Devora</span> Attendance. All rights reserved.</p>
  </div>
</footer>

<script src="/attendance/assets/js/theme.js"></script>
<script src="/attendance/assets/js/app.js"></script>
<script>
  // Live clock
  function tick() {
    const el = document.getElementById('liveClock');
    if (el) el.textContent = new Date().toLocaleString('en-US', {
      weekday: 'short', month: 'short', day: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
  }
  tick(); setInterval(tick, 1000);
</script>
<?= $extraScript ?? '' ?>
</body>

</html>