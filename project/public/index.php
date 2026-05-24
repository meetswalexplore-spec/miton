<?php
/**
 * public/index.php
 * Smart-card PDF generation page.
 * Fixes applied vs original:
 *   1. Removed all Cloudflare Rocket-Loader type="4df61593…-text/javascript" artifacts
 *   2. Removed if(!window.__cfRLUnblockHandlers)return false; from onclick handlers
 *   3. Removed data-cf-modified-* attributes
 *   4. Removed extra stray </div> after .plans wrapper
 *   5. Fixed .row closing tag that was missing before the submit buttons
 *   6. Removed /cdn-cgi/ Cloudflare script tag
 *   7. Form action now points to /api/generate.php (correct path)
 *   8. Loader overlay display uses flex (was conflicting with none; handled by CSS)
 *   9. Duplicate @keyframes spin renamed to spinFloat in CSS
 *  10. Duplicate .slider CSS consolidated in style.css
 *  11. display:flow → display:block fixed in CSS
 *  12. All inline onclick CF-guards cleaned
 */

require_once __DIR__ . '/../config/constants.php';

// ── Page variables ─────────────────────────────────────────────────
$pageTitle  = 'স্মার্টকার্ড পিডিএফ মেইক | ' . APP_NAME;
$activeMenu = 'nid-pdf-smart-card-make';
$userId     = 'USR236QESA';           // ← replace with session value
$userName   = 'Md Emran Hosen';       // ← replace with session value
$badgeImg   = 'images/badge-default.png';
$extraJs    = '/public/js/api.js';    // page-specific JS loaded in footer

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ── Page Header ──────────────────────────────────────────────── -->
<div class="plans">
  <div class="header-actions">
    <a href="/smart-card-make-history" class="back-btn btn-left">⟵ হিস্টোরি পেইজে যান</a>

    <h1 class="page-title">
      মাত্র<span class="price-badge"><strong id="showPagePrice"></strong></span>টাকায়
      আইডি কার্ডের স্মার্টকার্ডের পিডিএফ বানানোর অপশন এখানে!
    </h1>

    <a href="/smart-card-make-history" class="back-btn btn-right">হিস্টোরি পেইজে যান ⟶</a>

    <!-- Mobile: single centred back button -->
    <a href="/smart-card-make-history" class="back-btn mobile-only">⟵ হিস্টোরি পেইজে যান ⟶</a>
  </div>
</div><!-- /.plans -->

<!-- ── Main Content ──────────────────────────────────────────────── -->
<div class="main-content my-4">

  <!-- PDF Drop Zone -->
  <div class="nid" id="dropZone" style="cursor:pointer;">
    <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF Icon">
    <p>এখানে ফাইল টানুন এবং ড্রপ করুন<br>অথবা ফাইল নির্বাচন করতে ক্লিক করুন</p>
    <div class="preview" id="previewArea"></div>
  </div>

  <!-- Notice -->
  <div class="row g-3 my-4">
    <div class="col-md-12">
      <div style="border:2px solid #2495ff;border-radius:6px;padding:12px;
                  background-color:#f9fcff;font-size:16px;line-height:1.6;">
        ⚠️ দয়া করে লক্ষ্য করুন: সাইকপি আপলোড করার পর ছবি এবং সিগনেচার আসার পর
        সেগুলো ডাউনলোড করে নিচের লিংক থেকে ছবি এবং সিগনেচারের ব্যাকগ্রাউন্ড কেটে নিন —
        তাহলে ১০০% অরিজিনাল এবং সঠিক পিডিএফ মেইক হবে!
        <a href="https://www.remove.bg/upload" target="_blank" rel="noopener"
           style="display:block;width:100%;text-align:center;margin-top:12px;
                  padding:10px;background-color:#2495ff;color:#fff;
                  font-weight:bold;text-decoration:none;border-radius:4px;">
          এখানে ক্লিক করুন
        </a>
      </div>
    </div>
  </div>

  <!-- Upload Form — action now points to the correct backend endpoint -->
  <form method="post" action="/api/generate.php"
        enctype="multipart/form-data" id="uploadForm">

    <!-- Hidden: server-side user identity -->
    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">

    <!-- Hidden: API-supplied image URLs (set by JS when sign-copy PDF is parsed) -->
    <input type="hidden" id="hiddenProfileImg" name="hiddenProfileImg" value="">
    <input type="hidden" id="hiddenSignImg"    name="hiddenSignImg"    value="">
    <input type="hidden" id="hiddenUCode"      name="hiddenUCode"      value="">

    <!-- Hidden PDF file input (triggered by drop-zone click / drag-drop) -->
    <input type="file" id="pdfUpload" name="pdf_file" accept=".pdf" style="display:none">

    <!-- Legacy load button (hidden; kept for JS compatibility) -->
    <button class="btn btn-primary my-2 pdf_btn" type="submit" name="upload_pdf"
            style="width:50%;margin:0 auto;display:none;">Load</button>

    <div class="row g-4">

      <!-- ── Photo preview ──────────────────────────────────────── -->
      <div class="col-md-6">
        <label class="form-label">এনআইডির ছবি *</label>
        <div class="image-preview" id="nidImagePreview"
             style="min-height:200px;border:1px solid #ccc;
                    display:flex;justify-content:center;align-items:center;cursor:pointer;"
             onclick="document.querySelector('[name=profile_img]').click()">
          কোনো ছবি নেই!
        </div>
        <input type="file" name="profile_img" class="form-control d-none" accept="image/*"
               onchange="previewImageAndClearHidden(this,'nidImagePreview','hiddenProfileImg')"
               required>
        <div class="text-center mt-2">
          <button type="button" class="btn btn-success w-100 mt-2"
                  onclick="downloadPreview('nidImagePreview')">
            ছবি ডাউনলোড করুন!
          </button>
        </div>
      </div>

      <!-- ── Signature preview ──────────────────────────────────── -->
      <div class="col-md-6">
        <label class="form-label">স্বাক্ষর *</label>
        <div class="image-preview" id="signImagePreview"
             style="min-height:200px;border:1px solid #ccc;
                    display:flex;justify-content:center;align-items:center;cursor:pointer;"
             onclick="document.querySelector('[name=sign_img]').click()">
          কোনো সিগনেচার নেই!
        </div>
        <input type="file" name="sign_img" class="form-control d-none" accept="image/*"
               onchange="previewImageAndClearHidden(this,'signImagePreview','hiddenSignImg')"
               required>
        <div class="text-center mt-2 d-flex gap-2">
          <button type="button" class="btn btn-success w-100 mt-2"
                  onclick="downloadPreview('signImagePreview')">
            সিগনেচার ডাউনলোড করুন!
          </button>
          <button type="button" id="fingerLoadBtn" class="btn btn-warning w-100 mt-2">
            ফিংগার লোড করুন!
          </button>
        </div>
      </div>

      <!-- ── Personal information fields ───────────────────────── -->
      <div style="padding:20px;border:2px solid blue;border-radius:8px;margin-top:20px;">
        <div class="row g-3">

          <div class="col-md-6">
            <label>নাম (বাংলায়):</label>
            <input class="form-control" name="nameBN" required>
          </div>

          <div class="col-md-6">
            <label>নাম (ইংরেজিতে):</label>
            <input class="form-control" name="nameEn" required>
          </div>

          <div class="col-md-6">
            <label>এনআইডি নম্বর:</label>
            <input class="form-control" name="nid_num" required>
          </div>

          <div class="col-md-6">
            <label>জন্ম তারিখ:</label>
            <!-- FIX: was missing id on this field in some versions -->
            <input class="form-control" id="dob_date" name="dob_date"
                   placeholder="06 Jan 2000" required>
          </div>

          <div class="col-md-6">
            <label>পিতার নাম:</label>
            <input class="form-control" name="father_name">
          </div>

          <div class="col-md-6">
            <label>মাতার নাম:</label>
            <input class="form-control" name="mother_name">
          </div>

          <div class="col-md-6">
            <label>জন্মস্থান (ইংরেজিতে, বড় হাতে):</label>
            <input class="form-control" name="birth_place" placeholder="DHAKA">
          </div>

          <div class="col-md-6">
            <label>পিন নম্বর:</label>
            <input class="form-control" name="pincode">
          </div>

          <div class="col-md-4">
            <label>রক্তের গ্রুপ:</label>
            <input class="form-control" name="blood_groud">
          </div>

          <div class="col-md-4">
            <label>লিঙ্গ:</label>
            <select class="form-control" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="col-md-4">
            <label>প্রদানের তারিখ:</label>
            <input class="form-control" id="regs_date" name="regs_date"
                   placeholder="06 Jan 2024">
          </div>

          <!-- Address with Auto-Save toggle -->
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label for="addressField" class="form-label mb-0">ঠিকানা:</label>
              <div class="d-flex align-items-center gap-2">
                <label class="switch mb-0">
                  <input type="checkbox" id="saveToggle">
                  <span class="slider"></span>
                </label>
                <span>Auto Save</span>
              </div>
            </div>
            <textarea class="form-control" id="addressField"
                      name="address" rows="2"></textarea>
          </div>

        </div><!-- /.row g-3 (inner) -->
      </div><!-- /personal info box -->

      <!-- ── Submit / Reset ──────────────────────────────────────── -->
      <!-- FIX: was missing the closing </div> for the .row before these buttons -->
      <div class="col-12 text-center mt-3 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary"
                onclick="resetForm()" style="width:48%;">Reset</button>
        <button type="submit" class="btn btn-primary"
                name="save_btn" style="width:48%;">Save &amp; Print</button>
      </div>

    </div><!-- /.row g-4 (outer) -->
  </form><!-- /#uploadForm -->

</div><!-- /.main-content -->

<!-- ── Auto-Load Modal ───────────────────────────────────────────── -->
<div class="modal fade" id="autoLoadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header">
        <h5 class="modal-title">অটো এনআইডি মেইক</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- FIX: this inner form has no action — it triggers JS only (pop_load_btn) -->
        <form id="autoLoadForm">
          <input type="text" class="form-control mb-3" name="pop_nid"
                 placeholder="এনআইডি নাম্বার লিখুনঃ" required>
          <input type="text" class="form-control mb-3" name="pop_date"
                 placeholder="জন্মতারিখ লিখুনঃ">
          <button type="submit" class="btn btn-primary w-100"
                  name="pop_load_btn">ডাটা লোড করুন</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bottom history button -->
<div class="bottom-history-btn-wrapper">
  <a href="/smart-card-make-history" class="bottom-history-btn">
    <i class="fa-solid fa-clock-rotate-left"></i>
    ← হিস্টোরির পেইজে যান
  </a>
</div>

<!-- Page price (read by api.js) -->
<input type="hidden" id="pagePrice" value="<?= PRICE_SMART_CARD_PDF ?>">

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
