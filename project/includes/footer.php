<?php
/**
 * includes/footer.php
 * Outputs closing scripts and </body></html>.
 *
 * Expected PHP variables (optional):
 *   $userId  string – current user code, forwarded to AndroidApp
 */
?>

<!-- ── Scripts ──────────────────────────────────────────────────── -->
<!-- jQuery (must come before Bootstrap, DataTables, Owl) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Firebase (push notifications) -->
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
<!-- Owl Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Shared UI logic (sidebar, floating menu, balance, pagination) -->
<script src="/public/js/script.js"></script>

<!-- Firebase init -->
<script>
firebase.initializeApp({
  apiKey:            "<?= defined('FIREBASE_API_KEY')          ? FIREBASE_API_KEY          : '' ?>",
  authDomain:        "<?= defined('FIREBASE_AUTH_DOMAIN')      ? FIREBASE_AUTH_DOMAIN      : '' ?>",
  projectId:         "<?= defined('FIREBASE_PROJECT_ID')       ? FIREBASE_PROJECT_ID       : '' ?>",
  messagingSenderId: "<?= defined('FIREBASE_MESSAGING_SENDER') ? FIREBASE_MESSAGING_SENDER : '' ?>",
  appId:             "<?= defined('FIREBASE_APP_ID')           ? FIREBASE_APP_ID           : '' ?>"
});
const messaging = firebase.messaging();
</script>

<!-- Android app user ID bridge -->
<?php if (!empty($userId)): ?>
<script>window.AndroidApp?.saveUserId("<?= htmlspecialchars($userId) ?>");</script>
<?php endif; ?>

<!-- Page-specific extra scripts (set $extraJs before requiring footer) -->
<?php if (!empty($extraJs)): ?>
<script src="<?= htmlspecialchars($extraJs) ?>"></script>
<?php endif; ?>

</body>
</html>
