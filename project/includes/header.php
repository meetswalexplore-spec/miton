<?php
/**
 * includes/header.php
 * Outputs the full <head>, sidebar, topbar, and notice bar.
 * Usage: require_once __DIR__ . '/../includes/header.php';
 *
 * Expected PHP variables (set by the calling page before requiring this file):
 *   $pageTitle   string  – <title> tag value
 *   $userId      string  – logged-in user code  (e.g. "USR236QESA")
 *   $userName    string  – display name
 *   $badgeImg    string  – badge image src (or 'images/badge-default.png')
 *   $activeMenu  string  – slug of the current page to highlight active link
 */
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0d6efd">
<title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>

<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- Owl Carousel -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<!-- Bengali Fonts -->
<link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- Shared stylesheet -->
<link rel="stylesheet" href="/public/css/style.css">
<!-- Page-specific stylesheet (optional — set $extraCss before require) -->
<?php if (!empty($extraCss)): ?>
<link rel="stylesheet" href="<?= htmlspecialchars($extraCss) ?>">
<?php endif; ?>
<link rel="manifest" href="/manifest.json">
</head>
<body>

<!-- ── Alert Section ────────────────────────────────────────────── -->
<div class="alert-section" id="alertSection"></div>

<!-- ── Payment Confirm Popup ────────────────────────────────────── -->
<div class="payment-overlay">
  <div class="payment-popup">
    <div class="icon">⚠</div>
    <h2>Are you sure?</h2>
    <p>এই সেবাটি ব্যবহার করার জন্য আপনার অ্যাকাউন্ট থেকে
       <span class="service_price"></span>টাকা কেটে নেওয়া হবে</p>
    <input type="hidden" class="hidden-price">
    <input type="hidden" class="hidden-id">
    <div class="payment-btn">
      <button class="payment-btn-cancel">Cancel</button>
      <button class="payment-btn-ok">OK</button>
    </div>
  </div>
</div>

<!-- ── Sidebar ───────────────────────────────────────────────────── -->
<div class="sidebar">
  <button class="close-btn">&times;</button>

  <div class="logo-container">
    <a href="/public/index.php">
      <img src="/public/assets/images/best_buy.png" alt="Best Buy Logo">
    </a>
  </div>

  <!-- Master open/close toggle for all dropdowns -->
  <div class="checkbox-wrapper-64">
    <label class="switch">
      <input type="checkbox" id="dropdownToggle">
      <span class="slider"></span>
    </label>
  </div>

  <ul class="sidebar_nav">
    <?php
    // Helper: output active class when slug matches
    function sidebarActive(string $slug, string $active): string {
        return $slug === $active ? ' class="active"' : '';
    }
    $a = $activeMenu ?? '';
    ?>

    <li class="menuheader">--- সকল অটো সার্ভিস ---</li>

    <li><a href="/public/index.php"<?= sidebarActive('index',$a) ?>>
      <i class="fa-solid fa-house"></i> ড্যাশবোর্ড</a></li>

    <li><a href="/work_rate"<?= sidebarActive('work_rate',$a) ?>>
      <i class="fa-solid fa-hand-holding-heart"></i> কাজের লিস্ট ও মূল্য</a></li>

    <li><a href="/api-list"<?= sidebarActive('api-list',$a) ?>>
      <i class="fa-solid fa-list"></i> সকল ধরনের API কিনুন</a></li>

    <!-- Registration services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa fa-birthday-cake"></i></span>সকল নিবন্ধনের সার্ভিস অটো
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/Ministry-Data-A"<?= sidebarActive('Ministry-Data-A',$a) ?>>⚪সুপার ফাস্ট মিনিস্ট্রি ডাটা</a></li>
        <li><a href="/birth-pdf-make-2"<?= sidebarActive('birth-pdf-make-2',$a) ?>>⚪জন্মনিবন্ধনের Pdf মেইক</a></li>
        <li><a href="/br_application_index"<?= sidebarActive('br_application_index',$a) ?>>⚪নতুন নিবন্ধনের আবেদন</a></li>
        <li><a href="/validate_correction_index"<?= sidebarActive('validate_correction_index',$a) ?>>⚪নাম্বার পরিবর্তন করে সংশোধন আবেদন</a></li>
        <li><a href="/death-pdf-make"<?= sidebarActive('death-pdf-make',$a) ?>>⚪মৃত্যু নিবন্ধনের PDF মেইক</a></li>
        <li><a href="/17digit-to-info"<?= sidebarActive('17digit-to-info',$a) ?>>⚪নিবন্ধনের বিস্তারিত তথ্য</a></li>
        <li><a href="/parent-info"<?= sidebarActive('parent-info',$a) ?>>⚪সন্তানের নিবন্ধন দিয়ে পিতা মাতার নিবন্ধন</a></li>
        <li><a href="/17digit-to-dob"<?= sidebarActive('17digit-to-dob',$a) ?>>⚪১৭সংখ্যা নিবন্ধন নং দিয়ে জন্মতারিখ</a></li>
      </ul>
    </li>

    <!-- NID services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-address-card"></i></span>সকল এনআইডি সার্ভিস অটো
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/auto_sincopy_index"<?= sidebarActive('auto_sincopy_index',$a) ?>>⚪অটো সাইনকপি</a></li>
        <li><a href="/server-copy"<?= sidebarActive('server-copy',$a) ?>>⚪অটো সার্ভার কপি</a></li>
        <li><a href="/userpass_to-servercopy"<?= sidebarActive('userpass_to-servercopy',$a) ?>>⚪ইউজার পাসওয়ার্ড দিয়ে সার্ভারকপি</a></li>
        <li><a href="/nid-pdf-make"<?= sidebarActive('nid-pdf-make',$a) ?>>⚪আইডি কার্ডের PDF মেইক</a></li>
        <li><a href="/nid-pdf-smart-card-make"<?= sidebarActive('nid-pdf-smart-card-make',$a) ?>>⚪স্মার্টকার্ডের পিডিএফ মেইক</a></li>
        <li><a href="/sin_to_server_form"<?= sidebarActive('sin_to_server_form',$a) ?>>⚪সাইনকপি থেকে সার্ভার কপি</a></li>
        <li><a href="/nid_correction_pdf"<?= sidebarActive('nid_correction_pdf',$a) ?>>⚪ইউজার পাসওয়ার্ড দিয়ে আইডির PDF ও সংশোধন ফর্ম</a></li>
      </ul>
    </li>

    <!-- Land services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-map-location-dot"></i></span>জমির খাজনার কাজ অটো
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/index_approval_auto"<?= sidebarActive('index_approval_auto',$a) ?>>⚪জমির খাজনার পেমেন্ট AUTO</a></li>
        <li><a href="/land_password_reset_index"<?= sidebarActive('land_password_reset_index',$a) ?>>⚪খাজনার পাসওয়ার্ড রিসেট</a></li>
        <li><a href="/land_dakhila_finder_index"<?= sidebarActive('land_dakhila_finder_index',$a) ?>>⚪হারানো দাখিলা বের করা</a></li>
      </ul>
    </li>

    <!-- Education -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-graduation-cap"></i></span>Edu সার্টিফিকেট মেইক অটো
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/certificate_hardcopy_make_index"<?= sidebarActive('certificate_hardcopy_make_index',$a) ?>>⚪সার্টিফিকেটের হার্ডকপি PDF মেইক</a></li>
        <li><a href="/online_certificate_index"<?= sidebarActive('online_certificate_index',$a) ?>>⚪সার্টিফিকেটের অনলাইনকপি মেইক</a></li>
      </ul>
    </li>

    <!-- Other auto services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-circle-info"></i></span>সকল অন্যান্য সার্ভিস অটো
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/tin_auto_download"<?= sidebarActive('tin_auto_download',$a) ?>>⚪অটো টিন সার্টিফিকেট ডাউনলোড</a></li>
        <li><a href="/passport_pdf_index"<?= sidebarActive('passport_pdf_index',$a) ?>>⚪অরিজিনাল Mrp ও ই পাসপোর্ট মেইক</a></li>
      </ul>
    </li>

    <li class="menuheader">--- সকল অর্ডার সার্ভিস ---</li>

    <li><a href="/educational_certificate_order"<?= sidebarActive('educational_certificate_order',$a) ?>>
      <span class="menu-icon">📑</span>১০০% অরিজিনাল সার্টিফিকেট</a></li>

    <li><a href="/mamla_ck"<?= sidebarActive('mamla_ck',$a) ?>>
      <span class="menu-icon">📑</span>মামলা চেক অর্ডার</a></li>

    <!-- Birth order services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-cake-candles"></i></span>নিবন্ধনের অর্ডার সার্ভিস
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/new-birth-certificate"<?= sidebarActive('new-birth-certificate',$a) ?>>⚪জন্ম ও মৃত্যু নিবন্ধন</a></li>
        <li><a href="/lost_birth_order"<?= sidebarActive('lost_birth_order',$a) ?>>⚪হারানো নিবন্ধন</a></li>
        <li><a href="/birth-correction"<?= sidebarActive('birth-correction',$a) ?>>⚪নিবন্ধনের সংশোধন</a></li>
        <li><a href="/birth-delete"<?= sidebarActive('birth-delete',$a) ?>>⚪আবেদন আইডি দিয়ে নিবন্ধন নং</a></li>
        <li><a href="/ministry-data"<?= sidebarActive('ministry-data',$a) ?>>⚪আনকমন মিনিস্ট্রি ডাটা</a></li>
      </ul>
    </li>

    <!-- NID order services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-address-card"></i></span>এনআইডি অর্ডার সার্ভিস
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/sign-copy"<?= sidebarActive('sign-copy',$a) ?>>⚪সাইনকপি</a></li>
        <li><a href="/server-copy-order"<?= sidebarActive('server-copy-order',$a) ?>>⚪সার্ভার কপি অর্ডার</a></li>
        <li><a href="/nid-pdf-order"<?= sidebarActive('nid-pdf-order',$a) ?>>⚪আইডি কার্ড PDF</a></li>
        <li><a href="/nid-user-set"<?= sidebarActive('nid-user-set',$a) ?>>⚪ইউজার পাসওয়ার্ড সেট</a></li>
        <li><a href="/smart_card_order"<?= sidebarActive('smart_card_order',$a) ?>>⚪অরিজিনাল স্মার্টকার্ড</a></li>
        <li><a href="/name-address-order"<?= sidebarActive('name-address-order',$a) ?>>⚪হারানো এনআইডি</a></li>
        <li><a href="/voter-upload"<?= sidebarActive('voter-upload',$a) ?>>⚪নাম্বার ফালানো</a></li>
        <li><a href="/correction-application-cancel"<?= sidebarActive('correction-application-cancel',$a) ?>>⚪সংশোধন আবেদন বাতিল</a></li>
        <li><a href="/nid-correction-approved"<?= sidebarActive('nid-correction-approved',$a) ?>>⚪সংশোধন এপ্রুভ</a></li>
      </ul>
    </li>

    <!-- SIM order services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-sim-card"></i></span>সিমের অর্ডার সার্ভিস
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/sim-biometric"<?= sidebarActive('sim-biometric',$a) ?>>⚪সিমের বায়োমেট্রিক</a></li>
        <li><a href="/sim-location"<?= sidebarActive('sim-location',$a) ?>>⚪সিমের লোকেশন</a></li>
        <li><a href="/sim-call-list"<?= sidebarActive('sim-call-list',$a) ?>>⚪সিমের কল লিস্ট</a></li>
        <li><a href="/all-sim-msg-list"<?= sidebarActive('all-sim-msg-list',$a) ?>>⚪সিমের মেসেজ লিস্ট</a></li>
        <li><a href="/nid-to-sim"<?= sidebarActive('nid-to-sim',$a) ?>>⚪Nid নং থেকে সব সিম</a></li>
        <li><a href="/imei-service"<?= sidebarActive('imei-service',$a) ?>>⚪imei দিয়ে মোবাইলের তথ্য</a></li>
        <li><a href="/account-biometric"<?= sidebarActive('account-biometric',$a) ?>>⚪একাউন্টের বায়োমেট্রিক</a></li>
        <li><a href="/account-statement"<?= sidebarActive('account-statement',$a) ?>>⚪একাউন্টের লেনদেন তথ্য</a></li>
      </ul>
    </li>

    <!-- Other order services -->
    <li class="dropdown">
      <a href="javascript:void(0);" class="dropdown-toggle"
         style="display:flex;align-items:center;justify-content:space-between;">
        <span style="display:flex;align-items:center;">
          <span class="menu-icon"><i class="fa-solid fa-bullhorn"></i></span>অন্যান্য অর্ডার সার্ভিস
        </span>
        <svg class="dropdown-arrow" style="width:10px;height:10px;margin-left:6px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </a>
      <ul class="dropdown-menu custom-dropdown-menu">
        <li><a href="/computer-certificate"<?= sidebarActive('computer-certificate',$a) ?>>⚪কম্পিউটার সার্টিফিকেট</a></li>
        <li><a href="/lost_tin"<?= sidebarActive('lost_tin',$a) ?>>⚪হারানো টিন সার্টিফিকেট</a></li>
        <li><a href="/mrp"<?= sidebarActive('mrp',$a) ?>>⚪Mrp থেকে Mrp রি-ইস্যু</a></li>
        <li><a href="/passport-sb-copy"<?= sidebarActive('passport-sb-copy',$a) ?>>⚪পাসপোর্টের PDF এবং SB কপি</a></li>
        <li><a href="/bmet-approve"<?= sidebarActive('bmet-approve',$a) ?>>⚪BMET এর সকল অর্ডার</a></li>
        <li><a href="/bmet-correction"<?= sidebarActive('bmet-correction',$a) ?>>⚪bmet-correction</a></li>
      </ul>
    </li>

    <li class="menuheader">--- অন্যান্য অপশন ---</li>

    <li><a href="/order-history"<?= sidebarActive('order-history',$a) ?>>
      <span class="menu-icon"><i class="fa-solid fa-list"></i></span>অর্ডার হিস্টোরি</a></li>

    <li><a href="/recharge"<?= sidebarActive('recharge',$a) ?>>
      <span class="menu-icon"><i class="fa-solid fa-sack-dollar"></i></span>অটো রিচার্জ</a></li>

    <li><a href="/my_profile"<?= sidebarActive('my_profile',$a) ?>>
      <span class="menu-icon"><i class="fa-solid fa-user"></i></span>আমার প্রোফাইল</a></li>

    <li><a href="/password-change"<?= sidebarActive('password-change',$a) ?>>
      <span class="menu-icon"><i class="fa-solid fa-key"></i></span>পাসওয়ার্ড পরিবর্তন</a></li>

    <li class="logout">
      <a href="/logout">
        <span class="menu-icon"><i class="fa-solid fa-power-off"></i></span>Logout
      </a>
    </li>
  </ul>
</div><!-- /.sidebar -->

<!-- ── Topbar ────────────────────────────────────────────────────── -->
<div class="topbar">
  <button class="toggle-btn"><i class="fa fa-bars"></i></button>

  <div class="topbar-user">
    <h5 class="user-name" style="text-transform:uppercase;margin:0;">
      <?= htmlspecialchars($userName ?? '') ?>
    </h5>
    <div class="user-badge">
      <img src="<?= htmlspecialchars($badgeImg ?? 'images/badge-default.png') ?>"
           alt="Badge" title="Badge">
    </div>
  </div>

  <div class="icons">
    <div class="btn-group mr-2" role="group" id="balanceGroup"
         style="border:1px solid #fff;cursor:pointer;">
      <button type="button" class="btn btn-secondary bg-white p-1 d-flex align-items-center gap-2">
        <img src="/public/assets/images/all-icon/money-bag.png" width="30" class="moneybag-icon">
        <span style="font-weight:700;color:#000;">বেলেন্স</span>
      </button>
      <button type="button" class="btn btn-transparent text-white fetchCount"
              id="balanceBtn">🔍</button>
    </div>
  </div>
</div><!-- /.topbar -->

<!-- ── Floating Quick-Access Menu ───────────────────────────────── -->
<div class="floating-menu" id="floatingMenu">
  <button class="floating-btn" id="floatBtn">
    <i class="fa-solid fa-screwdriver-wrench custom-icon"></i>
  </button>
  <div class="menu-options" id="menuOptions">
    <a href="/sign-copy"         class="menu-item"><i class="fa-solid fa-pen-nib black"></i> সাইনকপি অর্ডার!</a>
    <a href="/server-copy-order" class="menu-item"><i class="fa-solid fa-address-card black"></i> সার্ভারকপি অর্ডার!</a>
    <a href="/nid-pdf-order"     class="menu-item"><i class="fa-solid fa-address-card black"></i> NID কার্ড অর্ডার!</a>
    <a href="/order-history"     class="menu-item"><i class="fa-solid fa-clock-rotate-left black"></i> অর্ডার হিস্টোরি পেইজ!</a>
    <a href="/recharge"          class="menu-item"><i class="fa-solid fa-sack-dollar black"></i> অটো রিচার্জ করুন!</a>
    <a href="https://chat.whatsapp.com/LVPfw50DneNIjMH2vmfE0U" target="_blank" rel="noopener" class="menu-item">
      <i class="fa-solid fa-users-line black"></i> হোয়াটসঅ্যাপ গ্রুপ!</a>
    <a href="https://wa.me/8801842509992" target="_blank" rel="noopener" class="menu-item">
      <i class="fa-brands fa-square-whatsapp black"></i> এডমিনের হোয়াটসঅ্যাপ!</a>
    <a href="https://t.me/Tahsan888" target="_blank" rel="noopener" class="menu-item">
      <i class="fa-brands fa-telegram black"></i> এডমিনের টেলিগ্রাম</a>
  </div>
</div>

<!-- ── Loader Overlay ───────────────────────────────────────────── -->
<div id="loaderOverlay">
  <div class="spinner"></div>
  <div class="loader-text">
    <span>লো</span><span>ড</span>&nbsp;<span>হ</span><span>চ্ছে</span><span>...</span>
  </div>
  <div class="loader-name">❤️ <?= APP_NAME ?> ❤️</div>
</div>

<!-- ── Notice Bar ───────────────────────────────────────────────── -->
<div class="welcome">
  <div class="notice-track">
    <span>📢আমাদের সাইটের সকল অটো অপশনের হিস্টোরি ওই কাজের পেইজেই থাকে এবং যেসব কাজ অর্ডার করেন সেগুলোর হিস্টোরি একবারে নিচে অন্যান্য অপশনের এখানের অর্ডার হিস্টোরি অপশনে থাকে📢...✅সাইটে এনআইডি সার্ভিস অটো অপশনে অটো সাইনকপি বের করার কাজ চলমান থাকবে সকাল ১০টা থেকে রাত ৯টা পর্যন্ত✅</span>
  </div>
</div>
