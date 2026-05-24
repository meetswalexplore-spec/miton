/* ============================================================
   api.js  —  Form Logic, PDF Upload, API Calls, Validation
   ============================================================ */

/* ── Page Price display ──────────────────────────────────── */
const pagePrice = "100";

document.addEventListener("DOMContentLoaded", function () {
  const el = document.getElementById("showPagePrice");
  if (el) el.innerText = pagePrice;
});


/* ── SweetAlert Toast ────────────────────────────────────── */
const Toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});


/* ── District Map (Bangla → English) ────────────────────── */
const districtMap = {
  "ঢাকা":"DHAKA","গাজীপুর":"GAZIPUR","নারায়ণগঞ্জ":"NARAYANGANJ",
  "কিশোরগঞ্জ":"KISHOREGONJ","মুন্সীগঞ্জ":"MUNSHIGANJ","নরসিংদী":"NARSINGDI",
  "মানিকগঞ্জ":"MANIKGANJ","ফরিদপুর":"FARIDPUR","গোপালগঞ্জ":"GOPALGANJ",
  "মাদারীপুর":"MADARIPUR","রাজবাড়ী":"RAJBARI","শরীয়তপুর":"SHARIATPUR",
  "চট্টগ্রাম":"CHATTOGRAM","কক্সবাজার":"COX'S BAZAR","ফেনী":"FENI",
  "নোয়াখালী":"NOAKHALI","লক্ষ্মীপুর":"LAKSHMIPUR","চাঁদপুর":"CHANDPUR",
  "কুমিল্লা":"CUMILLA","ব্রাহ্মণবাড়িয়া":"BRAHMANBARIA","রাঙ্গামাটি":"RANGAMATI",
  "খাগড়াছড়ি":"KHAGRACHHARI","বান্দরবান":"BANDARBAN",
  "খুলনা":"KHULNA","বাগেরহাট":"BAGERHAT","সাতক্ষীরা":"SATKHIRA",
  "যশোর":"JASHORE","ঝিনাইদহ":"JHENAIDAH","মাগুরা":"MAGURA",
  "নড়াইল":"NARAIL","কুষ্টিয়া":"KUSHTIA","চুয়াডাঙ্গা":"CHUADANGA","মেহেরপুর":"MEHERPUR",
  "রাজশাহী":"RAJSHAHI","নাটোর":"NATORE","চাঁপাইনবাবগঞ্জ":"CHAPAINAWABGANJ",
  "পাবনা":"PABNA","বগুড়া":"BOGURA","জয়পুরহাট":"JOYPURHAT","সিরাজগঞ্জ":"SIRAJGANJ",
  "রংপুর":"RANGPUR","দিনাজপুর":"DINAJPUR","নীলফামারী":"NILPHAMARI",
  "কুড়িগ্রাম":"KURIGRAM","গাইবান্ধা":"GAIBANDHA","পঞ্চগড়":"PANCHAGARH",
  "ঠাকুরগাঁও":"THAKURGAON","লালমনিরহাট":"LALMONIRHAT",
  "বরিশাল":"BARISAL","ভোলা":"BHOLA","পটুয়াখালী":"PATUAKHALI",
  "পিরোজপুর":"PIROJPUR","ঝালকাঠি":"JHALOKATI","বরগুনা":"BARGUNA",
  "সিলেট":"SYLHET","মৌলভীবাজার":"MOULVIBAZAR","হবিগঞ্জ":"HABIGANJ","সুনামগঞ্জ":"SUNAMGANJ",
  "ময়মনসিংহ":"MYMENSINGH","নেত্রকোণা":"NETRAKONA","শেরপুর":"SHERPUR","জামালপুর":"JAMALPUR"
};

function extractDistrict(text) {
  if (!text) return "";
  const parts = text.split(/[\s,]+/);
  for (const word of parts) {
    if (districtMap[word.trim()]) return districtMap[word.trim()];
  }
  return text;
}


/* ── Date Helpers ────────────────────────────────────────── */
function getOneYearAgoDate() {
  const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const today = new Date();
  today.setFullYear(today.getFullYear() - 1);
  const day   = String(today.getDate()).padStart(2, "0");
  const month = months[today.getMonth()];
  const year  = today.getFullYear();
  return `${day} ${month} ${year}`;
}

function formatDateTo12Apr2000Format(dateString) {
  const shortMonths = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const monthNames  = ["January","February","March","April","May","June",
                       "July","August","September","October","November","December"];
  let parts = dateString.trim().split(/[\/\-\s]/);
  let day, month, year;
  if (isNaN(parts[1])) {
    month = monthNames.findIndex(m => m.toLowerCase() === parts[1].toLowerCase()) + 1;
  } else {
    month = parseInt(parts[1]);
  }
  if (parts[0].length === 4) {
    year = parseInt(parts[0]); day = parseInt(parts[2]);
  } else if (parts[2] && parts[2].length === 4) {
    day = parseInt(parts[0]); year = parseInt(parts[2]);
  } else {
    return dateString;
  }
  const dateObj = new Date(year, month - 1, day);
  if (isNaN(dateObj.getTime())) return dateString;
  return `${String(day).padStart(2,"0")} ${shortMonths[month-1]} ${year}`;
}


/* ── Image Helpers ───────────────────────────────────────── */
function previewImageAndClearHidden(input, previewId, hiddenInputId) {
  const preview     = document.getElementById(previewId);
  const hiddenInput = document.getElementById(hiddenInputId);
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width:100%;max-height:200px;">`;
      hiddenInput.value = "";
      input.removeAttribute("required");
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    preview.innerHTML = "NO IMAGE";
    hiddenInput.value = "";
    input.setAttribute("required", "required");
  }
}

function setApiImages(photoUrl, signatureUrl) {
  if (photoUrl) {
    document.getElementById("hiddenProfileImg").value = photoUrl;
    document.getElementById("nidImagePreview").innerHTML =
      `<img src="${photoUrl}" alt="Profile Photo" style="max-width:100%;max-height:200px;">`;
    document.querySelector('[name="profile_img"]').removeAttribute("required");
  }
  if (signatureUrl) {
    document.getElementById("hiddenSignImg").value = signatureUrl;
    document.getElementById("signImagePreview").innerHTML =
      `<img src="${signatureUrl}" alt="Signature" style="max-width:100%;max-height:200px;">`;
    document.querySelector('[name="sign_img"]').removeAttribute("required");
  } else {
    document.querySelector('[name="sign_img"]').setAttribute("required","required");
    document.getElementById("signImagePreview").innerHTML = "NO IMAGE";
    document.getElementById("hiddenSignImg").value = "";
  }
}

function downloadPreview(previewId) {
  const preview = document.getElementById(previewId);
  const img     = preview.querySelector("img");
  if (!img) { Swal.fire("Error","No image available to download!","error"); return; }
  const nidNumber = document.querySelector('[name="nid_num"]').value || "unknown";
  let type = previewId.toLowerCase().includes("sign") ? "signature" : "image";
  let ext  = "jpg";
  const match = img.src.match(/\.([a-zA-Z0-9]+)(\?|$)/);
  if (match) ext = match[1];
  const link = document.createElement("a");
  link.href     = img.src;
  link.download = `${nidNumber}_${type}.${ext}`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}


/* ── Finger Load Button ──────────────────────────────────── */
document.addEventListener("DOMContentLoaded", () => {
  const btn        = document.getElementById("fingerLoadBtn");
  if (!btn) return;
  const signInput  = document.querySelector('input[type="file"][name="sign_img"]');
  const signPreview = document.getElementById("signImagePreview");
  const hiddenSign  = document.getElementById("hiddenSignImg");
  if (!signInput) { console.error("sign_img input not found!"); return; }

  async function urlToBlob(url) {
    const res = await fetch(url + (url.includes("?") ? "&" : "?") + "v=" + Date.now(), { cache: "no-store" });
    if (!res.ok) throw new Error("Image fetch failed: " + res.status);
    return await res.blob();
  }
  function blobToFile(blob, filename) {
    return new File([blob], filename, { type: blob.type || "image/png" });
  }

  btn.addEventListener("click", async () => {
    btn.disabled = true;
    try {
      const r    = await fetch("nid-smart-card/finger_random.php?ts=" + Date.now(), { cache: "no-store" });
      const data = await r.json();
      if (!data || data.status !== "success" || !data.url) {
        Swal.fire("ত্রুটি", data?.message || "Finger image পাওয়া যায়নি!", "error");
        return;
      }
      if (hiddenSign)  hiddenSign.value  = "";
      signInput.value = "";
      const blob = await urlToBlob(data.url);
      const file = blobToFile(blob, "finger_signature_" + Date.now() + ".png");
      const dt   = new DataTransfer();
      dt.items.add(file);
      signInput.files = dt.files;
      if (typeof previewImageAndClearHidden === "function") {
        previewImageAndClearHidden(signInput, "signImagePreview", "hiddenSignImg");
      } else {
        const reader = new FileReader();
        reader.onload = (e) => {
          if (signPreview) signPreview.innerHTML =
            `<img src="${e.target.result}" alt="Finger Signature" style="max-width:100%;max-height:200px;">`;
        };
        reader.readAsDataURL(file);
      }
      signInput.removeAttribute("required");
      Toast.fire({ icon: "success", title: "ফিঙ্গার সিগনেচার লোড হয়েছে ✅" });
    } catch (err) {
      console.error(err);
      Swal.fire("ত্রুটি", "Finger load করতে সমস্যা হয়েছে (Network/Server)!", "error");
    } finally {
      btn.disabled = false;
    }
  });
});


/* ── Address Auto-Save ───────────────────────────────────── */
window.addEventListener("DOMContentLoaded", function () {
  fetch("nid-smart-card/get_address.php")
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        document.getElementById("addressField").value   = data.saved_address || "";
        document.getElementById("saveToggle").checked   = data.auto_save === "1";
      }
    })
    .catch(err => console.error("Fetch address error:", err));
});

document.getElementById("saveToggle")?.addEventListener("change", function () {
  const toggle  = this;
  const autoSave = toggle.checked ? 1 : 0;
  const addr    = document.getElementById("addressField").value.trim();
  fetch("nid-smart-card/save_address.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "address=" + encodeURIComponent(addr) + "&auto_save=" + autoSave
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === "success") {
      Swal.fire(
        autoSave ? "Success" : "Info",
        autoSave ? "Auto Save চালু হয়েছে এবং Address সেভ হয়েছে।" : "Auto Save বন্ধ করা হয়েছে।",
        autoSave ? "success" : "info"
      );
    } else {
      toggle.checked = !autoSave;
      Swal.fire("Error", data.message || "Database error!", "error");
    }
  })
  .catch(() => {
    toggle.checked = !autoSave;
    Swal.fire("Error", "Network বা Server Error!", "error");
  });
});

document.getElementById("addressField")?.addEventListener("input", function () {
  if (document.getElementById("saveToggle").checked) {
    const addr = this.value.trim();
    fetch("nid-smart-card/save_address.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "address=" + encodeURIComponent(addr) + "&auto_save=1"
    }).catch(err => console.error("Auto save error:", err));
  }
});


/* ── Drop Zone / PDF Upload ──────────────────────────────── */
document.getElementById("pdfUpload")?.addEventListener("click", function () {
  this.value = "";
});
document.getElementById("dropZone")?.addEventListener("click", function () {
  document.getElementById("pdfUpload").click();
});

const dropZone  = document.getElementById("dropZone");
const pdfInput  = document.getElementById("pdfUpload");
const previewArea = document.getElementById("previewArea");
let dragCounter = 0;

if (dropZone) {
  dropZone.addEventListener("dragenter",  (e) => { e.preventDefault(); dragCounter++; dropZone.classList.add("dragging"); });
  dropZone.addEventListener("dragover",   (e) => { e.preventDefault(); dropZone.classList.add("dragging"); });
  dropZone.addEventListener("dragleave",  (e) => {
    e.preventDefault(); dragCounter--;
    if (dragCounter <= 0) { dropZone.classList.remove("dragging"); dragCounter = 0; }
  });
  dropZone.addEventListener("drop", (e) => {
    e.preventDefault(); dragCounter = 0; dropZone.classList.remove("dragging");
    const file = e.dataTransfer.files?.[0];
    if (!file) return;
    if (file.type !== "application/pdf") { Swal.fire("ত্রুটি","শুধু PDF ফাইল দিন।","error"); return; }
    const dt = new DataTransfer(); dt.items.add(file); pdfInput.files = dt.files;
    previewArea.innerHTML = `<b>${file.name}</b>`;
    pdfInput.dispatchEvent(new Event("change"));
  });
}
["dragover","drop"].forEach(evt => window.addEventListener(evt, e => e.preventDefault()));


/* ── Date Input Live Formatter ───────────────────────────── */
document.getElementById("dob_date")?.addEventListener("input", function () {
  this.value = formatDateTo12Apr2000Format(this.value);
});


/* ── PDF → API Data Load ─────────────────────────────────── */
document.getElementById("pdfUpload")?.addEventListener("change", async function () {
  const file = this.files[0];
  if (!file) return;

  const confirmUpload = await Swal.fire({
    title: "এখানে কোনো টাকা টাকা হবেনা!",
    text:  "আপনি কি ডাটা আনতে চান?",
    icon:  "warning",
    showCancelButton: true,
    confirmButtonText:  "ঠিক আছে! চালিয়ে যান",
    cancelButtonText:   "না! বাতিল করুন",
    confirmButtonColor: "#28a745",
    cancelButtonColor:  "#dc3545",
    reverseButtons: true
  });
  if (!confirmUpload.isConfirmed) {
    this.value = "";
    document.getElementById("previewArea").innerHTML = "";
    return;
  }

  const formData = new FormData();
  formData.append("pdf_file", file);
  document.querySelector("#loaderOverlay").style.display = "flex";

  fetch("smart-nid-make-api.php", { method: "POST", body: formData })
    .then(res => res.json())
    .then(data => {
      document.querySelector("#loaderOverlay").style.display = "none";
      if (data.status === "success" && data.data) {
        const f = data.data;
        const mapping = {
          nameBN:      f.name_bn      || f.nameBn     || "",
          nameEn:      f.name_en      || f.nameEn     || "",
          nid_num:     f.nid_number   || f.nid        || "",
          dob_date:    f.birth_date   || f.dob        || "",
          father_name: f.father       || f.fatherName || "",
          mother_name: f.mother       || f.motherName || "",
          birth_place: extractDistrict(f.address || f.birth_place || ""),
          pincode:     f.pin          || f.pincode    || "",
          blood_groud: f.blood_group  || f.bloodGroup || "",
          gender:      f.gender       || "",
          regs_date:   f.issue_date   || f.regs_date  || getOneYearAgoDate(),
          address:     f.address      || ""
        };

        for (const [key, val] of Object.entries(mapping)) {
          const el = document.querySelector(`[name="${key}"]`);
          if (!el) continue;
          if (el.tagName === "SELECT") {
            if (key === "gender") {
              const v = val.toLowerCase();
              el.value = v === "male" ? "Male" : v === "female" ? "Female" : v === "other" ? "Other" : "";
            } else { el.value = val; }
          } else if (key === "dob_date" || key === "regs_date") {
            el.value = formatDateTo12Apr2000Format(val);
          } else {
            el.value = val;
          }
        }

        setApiImages(f.photo || "", f.signature || "");
        document.getElementById("hiddenUCode").value = f.u_code || "";
        Toast.fire({ icon: "success", title: "আলহামদুলিল্লাহ্‌ ডাটা লোড হয়েছে।" });
      } else {
        Swal.fire("ত্রুটি", data.message || "ডেটা পাওয়া যায়নি।", "error");
      }
    })
    .catch(() => {
      document.querySelector("#loaderOverlay").style.display = "none";
      Swal.fire("ত্রুটি", "API কল করতে সমস্যা হয়েছে।", "error");
      this.value = "";
      document.getElementById("previewArea").innerHTML = "";
    });
});


/* ── Form Reset ──────────────────────────────────────────── */
function resetForm() {
  document.getElementById("uploadForm").reset();
  document.getElementById("nidImagePreview").innerHTML  = "NO IMAGE";
  document.getElementById("signImagePreview").innerHTML = "NO IMAGE";
  document.getElementById("previewArea").innerHTML      = "";
  document.getElementById("hiddenProfileImg").value     = "";
  document.getElementById("hiddenSignImg").value        = "";
  document.getElementById("hiddenUCode").value          = "";
  document.getElementById("pdfUpload").value            = "";
  const issueDate = document.getElementById("regs_date");
  if (issueDate) issueDate.value = getOneYearAgoDate();
}
function showModal() {
  new bootstrap.Modal(document.getElementById("autoLoadModal")).show();
}

window.addEventListener("pageshow", e => { if (e.persisted) resetForm(); });


/* ── Validation & Form Submit ────────────────────────────── */
let submittedOnce = false;

function setInvalid(el)   { if (!el) return; el.classList.add("is-invalid");    el.scrollIntoView({ behavior:"smooth", block:"center" }); }
function clearInvalid(el) { if (!el) return; el.classList.remove("is-invalid"); }

function isValidCustomDateFormat(v) {
  if (!v) return false;
  v = v.trim();
  const re = /^(0[1-9]|[12][0-9]|3[01])\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(19|20)\d{2}$/;
  if (!re.test(v)) return false;
  const parts = v.split(" ");
  const day   = parseInt(parts[0], 10);
  const months = {Jan:0,Feb:1,Mar:2,Apr:3,May:4,Jun:5,Jul:6,Aug:7,Sep:8,Oct:9,Nov:10,Dec:11};
  const d = new Date(parseInt(parts[2]), months[parts[1]], day);
  return d.getFullYear() === parseInt(parts[2]) && d.getMonth() === months[parts[1]] && d.getDate() === day;
}

function isAllCapsEnglishPlace(v) {
  if (!v) return false;
  return /^[A-Z][A-Z .'-]*$/.test(v.trim());
}

document.getElementById("uploadForm")?.addEventListener("submit", async function (e) {
  e.preventDefault();
  const form      = e.target;
  const dobEl     = document.getElementById("dob_date");
  const regsEl    = document.getElementById("regs_date");
  const birthEl   = document.querySelector('[name="birth_place"]');
  const genderEl  = document.querySelector('[name="gender"]');

  [dobEl, regsEl, birthEl, genderEl].forEach(clearInvalid);

  if (!isValidCustomDateFormat(dobEl?.value))   { setInvalid(dobEl);    Swal.fire("ত্রুটি","জন্ম তারিখ সঠিক নয়! উদাহরণ: 06 Jan 2025","error"); return; }
  if (!isValidCustomDateFormat(regsEl?.value))  { setInvalid(regsEl);   Swal.fire("ত্রুটি","প্রদানের তারিখ সঠিক নয়! এইভাবে দিতে হবে অবশ্যই! (06 Jan 2025)","error"); return; }
  if (!isAllCapsEnglishPlace(birthEl?.value))   { setInvalid(birthEl);  Swal.fire("ত্রুটি","জন্মস্থান অবশ্যই ইংরেজিতে বড় হাতের অক্ষরে লিখুন (উদাহরণ: DHAKA)","error"); return; }
  if (!genderEl?.value)                          { setInvalid(genderEl); Swal.fire("ত্রুটি","লিঙ্গ সিলেক্ট করুন!","error"); return; }

  const profileVal = document.getElementById("hiddenProfileImg")?.value || "";
  const signVal    = document.getElementById("hiddenSignImg")?.value    || "";
  const formData   = new FormData(form);
  formData.set("hiddenProfileImg", profileVal);
  formData.set("hiddenSignImg",    signVal);

  const price = Number(document.getElementById("pagePrice")?.value || 0);
  const confirmSubmit = await Swal.fire({
    title: "টাকা কাটা হবে",
    text:  `এই সার্ভিস ব্যবহার করতে আপনার একাউন্ট থেকে ${price} টাকা কেটে নেওয়া হবে।`,
    icon:  "warning",
    showCancelButton: true,
    confirmButtonText:  "হ্যা! ডাউনলোড করবো",
    cancelButtonText:   "না! বাতিল করুন",
    confirmButtonColor: "#28a745",
    cancelButtonColor:  "#dc3545",
    reverseButtons: true
  });
  if (!confirmSubmit.isConfirmed) return;

  document.querySelector("#loaderOverlay").style.display = "flex";
  try {
    const res  = await fetch("nid-smart-card/nid_pdf.php", { method: "POST", body: formData });
    const data = await res.json();
    document.querySelector("#loaderOverlay").style.display = "none";

    if (data.status === "success") {
      submittedOnce = true;
      Swal.fire({ icon:"success", title:"সফল", text:data.message, confirmButtonText:"ডাউনলোড করুন" })
        .then(() => {
          const link = document.createElement("a");
          link.href = data.redirect; link.download = "";
          document.body.appendChild(link); link.click(); document.body.removeChild(link);
          resetForm();
        });
    } else {
      Swal.fire("ত্রুটি", data.message || "কিছু সমস্যা হয়েছে!", "error");
    }
  } catch (err) {
    document.querySelector("#loaderOverlay").style.display = "none";
    Swal.fire("ত্রুটি", "Server error বা Network error", "error");
    console.error(err);
  }
});

document.querySelectorAll("#uploadForm input, #uploadForm textarea").forEach(el => {
  el.addEventListener("input", function () {
    if (submittedOnce) {
      const u = document.getElementById("hiddenUCode");
      if (u) u.value = "";
    }
  });
});


/* ── DOMContentLoaded init ───────────────────────────────── */
document.addEventListener("DOMContentLoaded", function () {
  const issueDate = document.getElementById("regs_date");
  if (issueDate && !issueDate.value) issueDate.value = getOneYearAgoDate();

  document.querySelector('[name="birth_place"]')?.addEventListener("input", function () {
    this.value = extractDistrict(this.value);
  });
});
