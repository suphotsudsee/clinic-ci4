<?= view('layouts/header', ['title' => 'อ่านบัตรประชาชน']) ?>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5>Smart Card Reader</h5>
        <p class="text-muted mb-2">เสียบบัตรประชาชน แล้วกดปุ่มอ่านข้อมูลจากบัตร</p>
        <button id="btnReadCard" class="btn btn-primary">อ่านข้อมูลจากบัตร</button>
        <span id="cardStatus" class="ms-2 text-muted"></span>
    </div>
</div>

<div class="card shadow-sm mb-3 d-none" id="thaiIdPreviewWrap">
    <div class="card-body">
        <div class="thai-id-frame">
            <div class="thai-id-preview">
                <div class="thai-id-overlay thai-id-cid" id="previewCid">-</div>
                <div class="thai-id-overlay thai-id-name-th" id="previewNameTh">-</div>
                <div class="thai-id-overlay thai-id-first-en" id="previewFirstNameEn">-</div>
                <div class="thai-id-overlay thai-id-last-en" id="previewLastNameEn">-</div>
                <div class="thai-id-overlay thai-id-dob-th" id="previewDobTh">-</div>
                <div class="thai-id-overlay thai-id-dob-en" id="previewDobEn">-</div>
                <div class="thai-id-overlay thai-id-address-1" id="previewAddressLine1">-</div>
                <div class="thai-id-overlay thai-id-address-2" id="previewAddressLine2"></div>
                <div class="thai-id-overlay thai-id-issue-th" id="previewIssueDateTh">-</div>
                <div class="thai-id-overlay thai-id-expiry-th" id="previewExpiryDateTh">-</div>
                <div class="thai-id-overlay thai-id-issue-en" id="previewIssueDateEn">-</div>
                <div class="thai-id-overlay thai-id-expiry-en" id="previewExpiryDateEn">-</div>
                <div class="thai-id-overlay thai-id-issuer" id="previewIssuer">-</div>
                <img id="previewPhotoLarge" class="thai-id-photo-clip" alt="Card Photo">
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5>ข้อมูลผู้ป่วย (รองรับกรอกมือ)</h5>
        <form method="post" action="/patients/card/import" id="cardForm">
            <?= csrf_field() ?>
            <input type="hidden" name="photo" value="">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">รูปจากบัตร</label>
                    <div class="border rounded p-2 text-center bg-light">
                        <img id="cardPhotoPreview" alt="Card Photo" style="width:100%; height:180px; object-fit:cover; display:none;">
                        <div id="cardPhotoPlaceholder" class="text-muted small">ยังไม่มีรูป</div>
                    </div>
                </div>
                <div class="col-md-4"><label class="form-label">เลขบัตรประชาชน</label><input class="form-control" name="cid" required></div>
                <div class="col-md-2"><label class="form-label">คำนำหน้า</label><input class="form-control" name="title_name"></div>
                <div class="col-md-3"><label class="form-label">ชื่อ</label><input class="form-control" name="first_name" required></div>
                <div class="col-md-3"><label class="form-label">นามสกุล</label><input class="form-control" name="last_name" required></div>
                <div class="col-md-2"><label class="form-label">เพศ</label><select class="form-select" name="gender"><option value="">เลือก</option><option value="M">ชาย</option><option value="F">หญิง</option><option value="O">อื่นๆ</option></select></div>
                <div class="col-md-3"><label class="form-label">วันเกิด</label><input type="date" class="form-control" name="dob"></div>
                <div class="col-md-7"><label class="form-label">ที่อยู่</label><input class="form-control" name="address"></div>
                <div class="col-md-4"><label class="form-label">โทรศัพท์</label><input class="form-control" name="phone"></div>
                <div class="col-md-8"><label class="form-label">ประวัติแพ้ยา</label><input class="form-control" name="allergy_note"></div>
            </div>
            <button class="btn btn-success mt-3">บันทึกข้อมูลผู้ป่วย</button>
        </form>
    </div>
</div>

<style>
.thai-id-frame {
    width: 8.6cm;
    height: 5.4cm;
    max-width: 100%;
    margin: 0 auto;
}

.thai-id-preview {
    position: relative;
    width: 100%;
    height: 100%;
    border-radius: 14px;
    overflow: hidden;
    background-image: url('/assets/id-card/CID.png');
    background-size: 100% 100%;
    background-repeat: no-repeat;
}

.thai-id-overlay {
    position: absolute;
    color: #0f2f5b;
    line-height: 1.02;
    font-weight: 700;
    white-space: nowrap;
    text-shadow: 0 0 1px rgba(255, 255, 255, 0.25);
    z-index: 2;
}

.thai-id-cid {
    right: 2.0%;
    top: 10.8%;
    transform: none;
    font-size: 5.5mm;
    letter-spacing: 0.1mm;
    max-width: 60%;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: clip;
    text-align: right;
}

.thai-id-name-th {
    left: 32.6%;
    top: 28.8%;
    font-size: 2.95mm;
    max-width: 35%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thai-id-first-en {
    left: 46.2%;
    top: 40.9%;
    font-family: 'Times New Roman', Georgia, serif;
    font-size: 2.8mm;
    max-width: 33%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thai-id-last-en {
    left: 46.2%;
    top: 49.7%;
    font-family: 'Times New Roman', Georgia, serif;
    font-size: 2.8mm;
    max-width: 33%;
    overflow: hidden;
    text-overflow: ellipsis;
}

.thai-id-dob-th {
    left: 47.7%;
    top: 57.1%;
    font-size: 2.95mm;
    letter-spacing: 0.06mm;
    max-width: 25%;
    overflow: hidden;
}

.thai-id-dob-en {
    left: 47.7%;
    top: 65.8%;
    font-family: 'Times New Roman', Georgia, serif;
    font-size: 2.9mm;
    letter-spacing: 0.04mm;
    max-width: 25%;
    overflow: hidden;
}

.thai-id-address-1,
.thai-id-address-2 {
    left: 12.2%;
    max-width: 58%;
    white-space: nowrap;
    overflow: visible;
    text-overflow: clip;
    font-size: 2.35mm;
    line-height: 1.02;
}

.thai-id-address-1 { top: 73.9%; }
.thai-id-address-2 { top: 78.2%; }

.thai-id-issue-th,
.thai-id-expiry-th,
.thai-id-issue-en,
.thai-id-expiry-en,
.thai-id-issuer {
    font-size: 1.85mm;
    white-space: nowrap;
}

.thai-id-issue-th { left: 14.0%; top: 88.6%; }
.thai-id-expiry-th { left: 70.0%; top: 88.6%; }
.thai-id-issue-en { left: 14.0%; top: 92.0%; font-family: 'Times New Roman', Georgia, serif; }
.thai-id-expiry-en { left: 70.0%; top: 92.0%; font-family: 'Times New Roman', Georgia, serif; }
.thai-id-issuer { left: 43.2%; top: 89.0%; transform: translateX(-50%); font-size: 1.9mm; max-width: 23%; overflow: hidden; text-overflow: ellipsis; }

.thai-id-photo-clip {
    position: absolute;
    left: 72.1%;
    top: 36.6%;
    width: 20.4%;
    height: 35.8%;
    object-fit: cover;
    display: none;
    z-index: 1;
}

@media (max-width: 767.98px) {
    .thai-id-frame {
        width: 100%;
        height: auto;
        aspect-ratio: 8.6 / 5.4;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("btnReadCard");
    const status = document.getElementById("cardStatus");
    const photoPreview = document.getElementById("cardPhotoPreview");
    const photoPlaceholder = document.getElementById("cardPhotoPlaceholder");

    const thaiIdPreviewWrap = document.getElementById("thaiIdPreviewWrap");
    const previewCid = document.getElementById("previewCid");
    const previewNameTh = document.getElementById("previewNameTh");
    const previewFirstNameEn = document.getElementById("previewFirstNameEn");
    const previewLastNameEn = document.getElementById("previewLastNameEn");
    const previewDobTh = document.getElementById("previewDobTh");
    const previewDobEn = document.getElementById("previewDobEn");
    const previewAddressLine1 = document.getElementById("previewAddressLine1");
    const previewAddressLine2 = document.getElementById("previewAddressLine2");
    const previewIssueDateTh = document.getElementById("previewIssueDateTh");
    const previewExpiryDateTh = document.getElementById("previewExpiryDateTh");
    const previewIssueDateEn = document.getElementById("previewIssueDateEn");
    const previewExpiryDateEn = document.getElementById("previewExpiryDateEn");
    const previewIssuer = document.getElementById("previewIssuer");
    const previewPhotoLarge = document.getElementById("previewPhotoLarge");

    function setField(name, value) {
        const el = document.querySelector('input[name="' + name + '"]');
        const select = document.querySelector('select[name="' + name + '"]');
        if (select) {
            select.value = value || "";
            return;
        }
        if (el) {
            el.value = value || "";
        }
    }

    function normalizeGenderValue(value) {
        const raw = String(value || "").trim();
        const upper = raw.toUpperCase();
        if (upper === "1" || upper === "M" || upper === "MALE") {
            return "M";
        }
        if (upper === "2" || upper === "F" || upper === "FEMALE") {
            return "F";
        }
        if (upper === "3" || upper === "O" || upper === "OTHER") {
            return "O";
        }
        return "";
    }

    function setPhoto(photoDataUrl) {
        if (photoDataUrl) {
            photoPreview.src = photoDataUrl;
            photoPreview.style.display = "block";
            photoPlaceholder.style.display = "none";
            return;
        }

        photoPreview.removeAttribute("src");
        photoPreview.style.display = "none";
        photoPlaceholder.style.display = "block";
    }

    function formatCid(value) {
        const digits = String(value || "").replace(/\D/g, "");
        if (digits.length !== 13) {
            return value || "-";
        }
        return digits.slice(0, 1) + " " + digits.slice(1, 5) + " " + digits.slice(5, 10) + " " + digits.slice(10, 12) + " " + digits.slice(12);
    }

    function formatDate(value) {
        const raw = String(value || "").trim();
        if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            return raw || "-";
        }
        const parts = raw.split("-");
        return parts[2] + "/" + parts[1] + "/" + parts[0];
    }

    function formatDateThaiBE(value) {
        const raw = String(value || "").trim();
        if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            return raw || "-";
        }

        const parts = raw.split("-");
        const yearBE = Number(parts[0]) + 543;
        if (!Number.isFinite(yearBE)) {
            return raw;
        }

        return parts[2] + "/" + parts[1] + "/" + String(yearBE);
    }

    function formatDateThaiMonthBE(value) {
        const raw = String(value || "").trim();
        if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            return raw || "-";
        }

        const months = ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];
        const parts = raw.split("-");
        const day = String(Number(parts[2]));
        const monthIndex = Number(parts[1]) - 1;
        const yearBE = Number(parts[0]) + 543;
        if (monthIndex < 0 || monthIndex > 11 || !Number.isFinite(yearBE)) {
            return raw;
        }

        return day + " " + months[monthIndex] + " " + String(yearBE);
    }

    function formatDateEnglish(value) {
        const raw = String(value || "").trim();
        if (!/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
            return raw || "-";
        }

        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const parts = raw.split("-");
        const monthIndex = Number(parts[1]) - 1;
        if (monthIndex < 0 || monthIndex > 11) {
            return raw;
        }

        return String(Number(parts[2])) + " " + months[monthIndex] + " " + parts[0];
    }

    function splitAddress(value) {
        const text = String(value || "").replace(/\s+/g, " ").trim();
        if (!text) {
            return ["-", ""];
        }
        if (text.length <= 36) {
            return [text, ""];
        }

        const target = 36;
        let cut = text.lastIndexOf(" ", target);
        if (cut < 22) {
            cut = text.indexOf(" ", target);
        }
        if (cut === -1) {
            return [text.slice(0, target), text.slice(target).trim()];
        }

        return [text.slice(0, cut).trim(), text.slice(cut + 1).trim()];
    }

    function fullNameTh(data) {
        const parts = [data.title_name, data.first_name, data.last_name].filter(Boolean);
        return parts.length ? parts.join(" ") : (data.name_th || "-");
    }

    function parseEnglishName(data) {
        const first = (data.first_name_en || "").trim();
        const last = (data.last_name_en || "").trim();
        if (first || last) {
            return { first: first || "-", last: last || "-" };
        }

        const raw = String(data.name_en || "").trim();
        if (!raw) {
            return { first: "-", last: "-" };
        }

        const parts = raw.split(/\s+/).filter(Boolean);
        const titles = ["MR.", "MR", "MRS.", "MRS", "MISS", "MS.", "MS"];
        if (parts.length > 0 && titles.indexOf(parts[0].toUpperCase()) !== -1) {
            parts.shift();
        }
        if (parts.length === 0) return { first: "-", last: "-" };
        if (parts.length === 1) return { first: parts[0], last: "-" };

        return { first: parts[0], last: parts.slice(1).join(" ") };
    }

    function resetCardPreview() {
        previewCid.textContent = "-";
        previewNameTh.textContent = "-";
        previewFirstNameEn.textContent = "-";
        previewLastNameEn.textContent = "-";
        previewDobTh.textContent = "-";
        previewDobEn.textContent = "-";
        previewAddressLine1.textContent = "-";
        previewAddressLine2.textContent = "";
        previewIssueDateTh.textContent = "-";
        previewExpiryDateTh.textContent = "-";
        previewIssueDateEn.textContent = "-";
        previewExpiryDateEn.textContent = "-";
        previewIssuer.textContent = "-";
        previewPhotoLarge.removeAttribute("src");
        previewPhotoLarge.style.display = "none";
        thaiIdPreviewWrap.classList.add("d-none");
    }

    function renderCardPreview(data) {
        const issueDate = data.issue_date || data.issueDate || data.issue || "";
        const expiryDate = data.expiry_date || data.expiryDate || data.expiry || "";
        const enName = parseEnglishName(data);
        const addr = splitAddress(data.address || "");

        previewCid.textContent = formatCid(data.cid);
        previewNameTh.textContent = fullNameTh(data);
        previewFirstNameEn.textContent = enName.first;
        previewLastNameEn.textContent = enName.last;

        previewDobTh.textContent = formatDateThaiMonthBE(data.dob);
        previewDobEn.textContent = formatDate(data.dob);

        previewAddressLine1.textContent = addr[0];
        previewAddressLine2.textContent = addr[1];

        previewIssueDateTh.textContent = formatDateThaiMonthBE(issueDate);
        previewExpiryDateTh.textContent = formatDateThaiMonthBE(expiryDate);
        previewIssueDateEn.textContent = formatDateEnglish(issueDate);
        previewExpiryDateEn.textContent = formatDateEnglish(expiryDate);
        previewIssuer.textContent = data.issuer || "(นายชื่อ นามสกุล)";

        if (data.photo) {
            previewPhotoLarge.src = data.photo;
            previewPhotoLarge.style.display = "block";
        } else {
            previewPhotoLarge.removeAttribute("src");
            previewPhotoLarge.style.display = "none";
        }

        thaiIdPreviewWrap.classList.remove("d-none");
    }

    btn.addEventListener("click", async function () {
        status.textContent = "กำลังอ่านข้อมูล...";

        try {
            const response = await fetch("/api/card/read", {
                method: "GET",
                headers: { "Accept": "application/json" },
                credentials: "same-origin"
            });

            const res = await response.json();
            if (!response.ok || !res.ok) {
                status.textContent = res.message || "ไม่สามารถอ่านบัตรได้ กรุณากรอกข้อมูลด้วยมือ";
                setPhoto("");
                setField("photo", "");
                resetCardPreview();
                return;
            }

            const data = res.data || {};
            setField("cid", data.cid);
            setField("title_name", data.title_name);
            setField("first_name", data.first_name);
            setField("last_name", data.last_name);
            setField("gender", normalizeGenderValue(data.gender));
            setField("dob", data.dob);
            setField("address", data.address);
            setField("photo", data.photo || "");
            setPhoto(data.photo || "");

            renderCardPreview(data);
            status.textContent = "อ่านข้อมูลสำเร็จ";
        } catch (error) {
            setField("photo", "");
            setPhoto("");
            resetCardPreview();
            status.textContent = "Card Reader Service ไม่พร้อมใช้งาน";
        }
    });

    setPhoto("");
    resetCardPreview();
});
</script>

<?= view('layouts/footer') ?>
