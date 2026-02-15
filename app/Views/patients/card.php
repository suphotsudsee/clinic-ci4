<?= view('layouts/header', ['title' => 'อ่านบัตรประชาชน']) ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h5>Smart Card Reader</h5>
        <p class="text-muted mb-2">เสียบบัตรประชาชนและกดปุ่มอ่านข้อมูลจาก Local Service</p>
        <button id="btnReadCard" class="btn btn-primary">อ่านข้อมูลจากบัตร</button>
        <span id="cardStatus" class="ms-2 text-muted"></span>
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
                <div class="col-md-2"><label class="form-label">เพศ</label><input class="form-control" name="gender"></div>
                <div class="col-md-3"><label class="form-label">วันเกิด</label><input type="date" class="form-control" name="dob"></div>
                <div class="col-md-7"><label class="form-label">ที่อยู่</label><input class="form-control" name="address"></div>
                <div class="col-md-4"><label class="form-label">โทรศัพท์</label><input class="form-control" name="phone"></div>
                <div class="col-md-8"><label class="form-label">ประวัติแพ้ยา</label><input class="form-control" name="allergy_note"></div>
            </div>
            <button class="btn btn-success mt-3">บันทึกข้อมูลผู้ป่วย</button>
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("btnReadCard");
    const status = document.getElementById("cardStatus");
    const photoPreview = document.getElementById("cardPhotoPreview");
    const photoPlaceholder = document.getElementById("cardPhotoPlaceholder");

    function setField(name, value) {
        const el = document.querySelector('input[name="' + name + '"]');
        if (el) {
            el.value = value || "";
        }
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
                return;
            }

            const data = res.data || {};
            setField("cid", data.cid);
            setField("title_name", data.title_name);
            setField("first_name", data.first_name);
            setField("last_name", data.last_name);
            setField("gender", data.gender);
            setField("dob", data.dob);
            setField("address", data.address);
            setField("photo", data.photo || "");
            setPhoto(data.photo || "");
            status.textContent = "อ่านข้อมูลสำเร็จ";
        } catch (error) {
            setField("photo", "");
            setPhoto("");
            status.textContent = "Card Reader Service ไม่พร้อมใช้งาน";
        }
    });

    setPhoto("");
});
</script>
<?= view('layouts/footer') ?>
