<?= view('layouts/header', ['title' => $mode === 'create' ? 'เพิ่มผู้ป่วย' : 'แก้ไขผู้ป่วย']) ?>
<?php $currentPhoto = (string) ($patient['photo'] ?? ''); ?>
<style>
.patient-section {
    border: 1px solid #e6e9ef;
    border-radius: 12px;
    padding: 1rem;
}

.patient-section-main {
    background: #dfeeff;
}

.patient-section-profile {
    background: #e3f6e3;
}

.patient-section-medical {
    background: #ffe9d6;
}

.dob-inline-info {
    font-size: 0.9rem;
    font-weight: 600;
    color: #243447;
    white-space: nowrap;
}

.dob-inline-wrap {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.dob-inline-wrap .form-control {
    max-width: 210px;
}

.patient-photo-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #fff;
    display: none;
}

.patient-photo-empty {
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 1px dashed #94a3b8;
    color: #64748b;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}
</style>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="mb-1"><?= $mode === 'create' ? 'ลงทะเบียนผู้ป่วย' : 'แก้ไขข้อมูลผู้ป่วย' ?></h5>
                <small class="text-muted">กรอกข้อมูลให้ครบถ้วนก่อนบันทึก</small>
            </div>
            <a class="btn btn-outline-secondary" href="/patients">ย้อนกลับ</a>
        </div>

        <form method="post" action="<?= $mode === 'create' ? '/patients/create' : '/patients/update/' . $patient['id'] ?>">
            <?= csrf_field() ?>

            <div class="patient-section patient-section-main mb-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label mb-2">รูปผู้ป่วย</label>
                        <div class="mb-2">
                            <img id="patientPhotoPreview" class="patient-photo-preview" alt="Patient Photo">
                            <div id="patientPhotoEmpty" class="patient-photo-empty">ยังไม่มีรูป</div>
                        </div>
                        <input type="hidden" id="photoField" name="photo" value="<?= esc($currentPhoto, 'attr') ?>">
                        <input type="file" id="photoUploadField" class="form-control form-control-sm" accept="image/*">
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="photoClearBtn">ล้างรูป</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">HN <span class="text-danger">*</span></label>
                        <input class="form-control" name="hn" value="<?= esc($patient['hn'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">เลขบัตรประชาชน <span class="text-danger">*</span></label>
                        <input class="form-control" name="cid" value="<?= esc($patient['cid'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">โทรศัพท์</label>
                        <input class="form-control" name="phone" value="<?= esc($patient['phone'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="patient-section patient-section-profile mb-3">
                <h6 class="mb-3">ข้อมูลส่วนตัว</h6>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">คำนำหน้า</label>
                        <input class="form-control" name="title_name" value="<?= esc($patient['title_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                        <input class="form-control" name="first_name" value="<?= esc($patient['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                        <input class="form-control" name="last_name" value="<?= esc($patient['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เพศ</label>
                        <select class="form-select" name="gender">
                            <option value="">เลือก</option>
                            <option value="M" <?= (($patient['gender'] ?? '') === 'M') ? 'selected' : '' ?>>ชาย</option>
                            <option value="F" <?= (($patient['gender'] ?? '') === 'F') ? 'selected' : '' ?>>หญิง</option>
                            <option value="O" <?= (($patient['gender'] ?? '') === 'O') ? 'selected' : '' ?>>อื่นๆ</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">วันเกิด</label>
                        <div class="dob-inline-wrap">
                            <input type="date" class="form-control" id="dobField" name="dob" value="<?= esc($patient['dob'] ?? '') ?>">
                            <div class="dob-inline-info" id="dobAgeInline">วันเกิด (พ.ศ.): - | อายุ ณ ปัจจุบัน: -</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="patient-section patient-section-medical">
                <h6 class="mb-3">การติดต่อและข้อมูลทางแพทย์</h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">ที่อยู่</label>
                        <input class="form-control" name="address" value="<?= esc($patient['address'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">ประวัติแพ้ยา</label>
                        <textarea class="form-control" name="allergy_note" rows="3"><?= esc($patient['allergy_note'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary px-4">บันทึก</button>
                <a class="btn btn-secondary" href="/patients">ย้อนกลับ</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const dobField = document.getElementById('dobField');
    const dobAgeInline = document.getElementById('dobAgeInline');
    const photoField = document.getElementById('photoField');
    const photoUploadField = document.getElementById('photoUploadField');
    const photoClearBtn = document.getElementById('photoClearBtn');
    const photoPreview = document.getElementById('patientPhotoPreview');
    const photoEmpty = document.getElementById('patientPhotoEmpty');

    function renderPhoto(photoValue) {
        const value = String(photoValue || '').trim();
        if (value === '') {
            photoPreview.removeAttribute('src');
            photoPreview.style.display = 'none';
            photoEmpty.style.display = 'flex';
            return;
        }

        photoPreview.src = value;
        photoPreview.style.display = 'block';
        photoEmpty.style.display = 'none';
    }

    if (photoField && photoUploadField && photoClearBtn && photoPreview && photoEmpty) {
        renderPhoto(photoField.value);

        photoUploadField.addEventListener('change', function () {
            const file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) {
                return;
            }

            if (!file.type || file.type.indexOf('image/') !== 0) {
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                const result = String(event.target && event.target.result ? event.target.result : '');
                photoField.value = result;
                renderPhoto(result);
            };
            reader.readAsDataURL(file);
        });

        photoClearBtn.addEventListener('click', function () {
            photoField.value = '';
            photoUploadField.value = '';
            renderPhoto('');
        });
    }

    if (!dobField || !dobAgeInline) {
        return;
    }

    function daysInMonth(year, monthIndex) {
        return new Date(year, monthIndex + 1, 0).getDate();
    }

    function parseDateOnly(value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value || '')) {
            return null;
        }

        const parts = value.split('-');
        const y = Number(parts[0]);
        const m = Number(parts[1]) - 1;
        const d = Number(parts[2]);
        const date = new Date(y, m, d);

        if (date.getFullYear() !== y || date.getMonth() !== m || date.getDate() !== d) {
            return null;
        }

        return date;
    }

    function formatThaiDateBE(date) {
        const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        const day = date.getDate();
        const month = months[date.getMonth()];
        const yearBE = date.getFullYear() + 543;
        return day + ' ' + month + ' ' + yearBE;
    }

    function calculateAgeParts(dob, now) {
        let years = now.getFullYear() - dob.getFullYear();
        let months = now.getMonth() - dob.getMonth();
        let days = now.getDate() - dob.getDate();

        if (days < 0) {
            let prevMonth = now.getMonth() - 1;
            let prevYear = now.getFullYear();
            if (prevMonth < 0) {
                prevMonth = 11;
                prevYear -= 1;
            }
            days += daysInMonth(prevYear, prevMonth);
            months -= 1;
        }

        if (months < 0) {
            months += 12;
            years -= 1;
        }

        return { years: years, months: months, days: days };
    }

    function updateDobPreview() {
        const dob = parseDateOnly(dobField.value);
        if (!dob) {
            dobAgeInline.textContent = 'วันเกิด (พ.ศ.): - | อายุ ณ ปัจจุบัน: -';
            return;
        }

        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        const dobText = 'วันเกิด (พ.ศ.): ' + formatThaiDateBE(dob);

        if (dob > today) {
            dobAgeInline.textContent = dobText + ' | อายุ ณ ปัจจุบัน: -';
            return;
        }

        const age = calculateAgeParts(dob, today);
        dobAgeInline.textContent = dobText + ' | อายุ ณ ปัจจุบัน: ' + age.years + ' ปี ' + age.months + ' เดือน ' + age.days + ' วัน';
    }

    dobField.addEventListener('input', updateDobPreview);
    updateDobPreview();
})();
</script>

<?= view('layouts/footer') ?>
