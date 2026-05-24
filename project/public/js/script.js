/* ============================================================
   script.js  —  General UI, Floating Menu, Sidebar, Pagination
   ============================================================ */

/* ── Floating Draggable Menu ─────────────────────────────── */
(function () {
  const floatBtn      = document.getElementById("floatBtn");
  const floatingMenu  = document.getElementById("floatingMenu");
  const menuOptions   = document.getElementById("menuOptions");
  if (!floatBtn) return;

  let isDragging = false, offsetX = 0, offsetY = 0, dragMoved = false;

  function startDrag(e) {
    e.preventDefault();
    isDragging = true;
    dragMoved  = false;
    const rect = floatingMenu.getBoundingClientRect();
    if (e.type === "mousedown") {
      offsetX = e.clientX - rect.left;
      offsetY = e.clientY - rect.top;
      document.addEventListener("mousemove", drag);
      document.addEventListener("mouseup",   stopDrag);
    } else {
      offsetX = e.touches[0].clientX - rect.left;
      offsetY = e.touches[0].clientY - rect.top;
      document.addEventListener("touchmove", drag,     { passive: false });
      document.addEventListener("touchend",  stopDrag);
    }
  }

  function drag(e) {
    if (!isDragging) return;
    dragMoved = true;
    e.preventDefault();
    const x = e.type === "mousemove" ? e.clientX : e.touches[0].clientX;
    const y = e.type === "mousemove" ? e.clientY : e.touches[0].clientY;
    const btnWidth  = floatingMenu.offsetWidth;
    const btnHeight = floatingMenu.offsetHeight;
    const winW = window.innerWidth;
    const winH = window.innerHeight;
    let left = Math.max(1, Math.min(x - offsetX, winW - btnWidth  - 1));
    let top  = Math.max(1, Math.min(y - offsetY, winH - btnHeight - 1));
    floatingMenu.style.transition = "none";
    floatingMenu.style.left     = left + "px";
    floatingMenu.style.top      = top  + "px";
    floatingMenu.style.right    = "auto";
    floatingMenu.style.bottom   = "auto";
    floatingMenu.style.position = "fixed";
  }

  function stopDrag() {
    isDragging = false;
    floatingMenu.style.transition = "all 0.2s ease";
    document.removeEventListener("mousemove", drag);
    document.removeEventListener("mouseup",   stopDrag);
    document.removeEventListener("touchmove", drag);
    document.removeEventListener("touchend",  stopDrag);
    if (!dragMoved) toggleMenu();
    dragMoved = false;

    const rect = floatingMenu.getBoundingClientRect();
    const winW = window.innerWidth;
    const winH = window.innerHeight;
    menuOptions.style.flexDirection = "column";
    if (rect.top < winH * 0.5) {
      menuOptions.style.top    = "70px";
      menuOptions.style.bottom = "auto";
    } else {
      menuOptions.style.bottom = "70px";
      menuOptions.style.top    = "auto";
    }
    if (rect.left < winW * 0.5) {
      menuOptions.style.left        = "0";
      menuOptions.style.right       = "auto";
      menuOptions.style.alignItems  = "flex-start";
    } else {
      menuOptions.style.right       = "0";
      menuOptions.style.left        = "auto";
      menuOptions.style.alignItems  = "flex-end";
    }
  }

  floatBtn.addEventListener("mousedown",  startDrag);
  floatBtn.addEventListener("touchstart", startDrag, { passive: false });

  window.toggleMenu = function () {
    if (menuOptions.classList.contains("show")) {
      menuOptions.classList.remove("show");
      menuOptions.classList.add("hide");
      setTimeout(() => (menuOptions.style.display = "none"), 400);
    } else {
      menuOptions.style.display = "flex";
      setTimeout(() => {
        menuOptions.classList.remove("hide");
        menuOptions.classList.add("show");
      }, 50);
    }
  };
})();


/* ── Balance Loader ──────────────────────────────────────── */
document.addEventListener("DOMContentLoaded", function () {
  const group = document.getElementById("balanceGroup");
  const btn   = document.getElementById("balanceBtn");
  if (!group || !btn) return;

  let loading = false;

  async function loadBalance() {
    if (loading) return;
    loading = true;
    btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i>`;
    try {
      const res  = await fetch("?action=get_order_counts");
      const data = await res.json();
      btn.innerHTML = data.error ? "⚠️" : data.balance + "৳";
    } catch (e) {
      btn.innerHTML = "❌";
    } finally {
      loading = false;
    }
  }

  group.addEventListener("click", loadBalance);
  group.addEventListener("touchstart", function (e) {
    e.preventDefault();
    loadBalance();
  }, { passive: false });
});


/* ── Sidebar: active dropdown scroll ────────────────────── */
window.addEventListener("load", function () {
  const activeLink = document.querySelector(
    ".sidebar .dropdown-menu a.active, .sidebar > ul > li > a.active"
  );
  if (!activeLink) return;
  const parentDropdown = activeLink.closest(".dropdown");
  if (parentDropdown) parentDropdown.classList.add("active");
  setTimeout(() => activeLink.scrollIntoView({ behavior: "smooth", block: "center" }), 250);
});


/* ── All-Dropdown Toggle ─────────────────────────────────── */
document.addEventListener("DOMContentLoaded", function () {
  const dropdownToggle = document.getElementById("dropdownToggle");
  const allDropdowns   = document.querySelectorAll(".sidebar .dropdown");
  if (!dropdownToggle) return;

  function setAllDropdowns(open) {
    allDropdowns.forEach(d => d.classList[open ? "add" : "remove"]("active"));
  }

  dropdownToggle.addEventListener("change", function () {
    setAllDropdowns(this.checked);
    localStorage.setItem("allDropdownToggle", this.checked ? "on" : "off");
  });

  const saved = localStorage.getItem("allDropdownToggle");
  if (saved === "on") { dropdownToggle.checked = true; setAllDropdowns(true); }
  else                { dropdownToggle.checked = false; setAllDropdowns(false); }
});


/* ── Alert Auto-Hide ─────────────────────────────────────── */
setTimeout(() => {
  const a = document.getElementById("alertSection");
  if (a) {
    a.style.opacity = "0";
    setTimeout(() => (a.style.display = "none"), 1000);
  }
}, 5000);


/* ── Popup helpers ───────────────────────────────────────── */
window.showPopup  = id => (document.getElementById(id).style.display = "flex");
window.closePopup = id => (document.getElementById(id).style.display = "none");


/* ── Table Pagination ────────────────────────────────────── */
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".table-container").forEach(function ($container) {
    let page = 1;
    let rowsPerPage = parseInt($container.querySelector(".entries-select")?.value || 10);

    function getFilteredRows() {
      return Array.from($container.querySelectorAll("table tbody tr")).filter(
        r => r.style.display !== "none"
      );
    }

    function paginateRows() {
      const rows      = Array.from($container.querySelectorAll("table tbody tr"));
      const searchVal = ($container.querySelector(".search-input")?.value || "").toLowerCase().trim();

      rows.forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(searchVal) ? "" : "none";
      });

      const visible    = getFilteredRows();
      const totalRows  = visible.length;
      const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;
      if (page > totalPages) page = totalPages;

      const start = (page - 1) * rowsPerPage;
      const end   = start + rowsPerPage;

      visible.forEach((r, i) => (r.style.display = i >= start && i < end ? "" : "none"));

      const noData     = $container.querySelector(".no-data");
      const showResult = $container.querySelector(".show-result");
      if (noData)     noData.style.display     = totalRows === 0 ? "block" : "none";
      if (showResult) showResult.textContent    = totalRows === 0
        ? "Showing 0 to 0 of 0 entries"
        : `Showing ${start + 1} to ${Math.min(end, totalRows)} of ${totalRows} entries`;

      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      const pag = $container.querySelector(".pagination-buttons");
      if (!pag) return;
      pag.innerHTML = "";
      if (totalPages <= 1) return;
      const prev = document.createElement("span"); prev.className = "prev"; prev.textContent = "Previous"; pag.appendChild(prev);
      for (let i = 1; i <= totalPages; i++) {
        const s = document.createElement("span");
        s.className   = "page-number" + (i === page ? " active" : "");
        s.textContent = i;
        pag.appendChild(s);
      }
      const next = document.createElement("span"); next.className = "next"; next.textContent = "Next"; pag.appendChild(next);
    }

    $container.querySelector(".entries-select")?.addEventListener("change", function () {
      rowsPerPage = parseInt(this.value);
      page = 1;
      paginateRows();
    });
    $container.addEventListener("click", function (e) {
      if (e.target.classList.contains("page-number")) {
        page = parseInt(e.target.textContent);
        paginateRows();
      }
      if (e.target.classList.contains("prev") && page > 1) { page--; paginateRows(); }
      if (e.target.classList.contains("next")) {
        const tp = Math.ceil(getFilteredRows().length / rowsPerPage);
        if (page < tp) { page++; paginateRows(); }
      }
    });
    $container.querySelector(".search-input")?.addEventListener("input", function () {
      page = 1;
      paginateRows();
    });
    paginateRows();
  });
});


/* ── Sidebar Dropdown jQuery-style ──────────────────────── */
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".dropdown-toggle").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const parent = this.closest(".dropdown");
      if (parent.classList.contains("active")) {
        parent.classList.remove("active");
      } else {
        document.querySelectorAll(".dropdown").forEach(d => d.classList.remove("active"));
        parent.classList.add("active");
      }
    });
  });

  document.querySelectorAll(".dropdown-menu a.active").forEach(a => {
    a.closest(".dropdown")?.classList.add("active");
  });
});


/* ── Service Worker ──────────────────────────────────────── */
(async function () {
  const isStandalone = window.matchMedia("(display-mode: standalone)").matches
    || window.navigator.standalone === true;
  const isAppFlag = new URLSearchParams(location.search).get("app") === "1";
  if (!("serviceWorker" in navigator)) return;
  if (isStandalone || isAppFlag) {
    try { await navigator.serviceWorker.register("/sw.js"); } catch (e) {}
    return;
  }
  const regs = await navigator.serviceWorker.getRegistrations();
  for (const r of regs) await r.unregister();
  if (window.caches) {
    const keys = await caches.keys();
    for (const k of keys) await caches.delete(k);
  }
})();


/* ── Android App save user id ───────────────────────────── */
window.AndroidApp?.saveUserId("USR236QESA");
