<?= view('layouts/header', ['title' => $mode === 'create' ? 'Visit Record' : 'Edit Visit']) ?>
<div class="card shadow-sm">
    <div class="card-body">
        <h5><?= $mode === 'create' ? 'Create Visit Record' : 'Edit Visit Record' ?></h5>
        <div class="d-flex align-items-center gap-3 mb-3">
            <?= view('components/patient_photo', ['photo' => $patient['photo'] ?? '', 'size' => 72, 'alt' => trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))]) ?>
            <p class="text-muted mb-0">Patient: <?= esc($patient['hn'] . ' - ' . $patient['first_name'] . ' ' . $patient['last_name']) ?></p>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label mb-1" for="voiceLangSelect">Voice Language</label>
                <select class="form-select form-select-sm" id="voiceLangSelect">
                    <option value="th-TH" selected>Thai (th-TH)</option>
                    <option value="en-US">English (en-US)</option>
                </select>
            </div>
        </div>

        <form method="post" action="<?= $mode === 'create' ? '/visits/create/' . $patient['id'] : '/visits/update/' . $visit['id'] ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Visit Date/Time</label>
                    <input type="datetime-local" class="form-control" name="visit_date" value="<?= esc(isset($visit['visit_date']) ? str_replace(' ', 'T', substr($visit['visit_date'], 0, 16)) : date('Y-m-d\\TH:i')) ?>">
                </div>

                <div class="col-md-8">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                        <label class="form-label mb-0" for="chiefComplaintField">Chief Complaint</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="voiceChiefStartBtn">Start Voice</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="voiceChiefAppendBtn">Append</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="voiceChiefStopBtn" disabled>Stop</button>
                        </div>
                    </div>
                    <input class="form-control" id="chiefComplaintField" name="chief_complaint" value="<?= esc($visit['chief_complaint'] ?? '') ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Vital Signs</label>
                    <input class="form-control" name="vital_signs" value="<?= esc($visit['vital_signs'] ?? '') ?>" placeholder="e.g. BP 120/80, BT 37.0">
                </div>

                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                        <label class="form-label mb-0" for="diagnosisField">Diagnosis</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="voiceStartBtn">Start Voice</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="voiceAppendBtn">Append</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="voiceStopBtn" disabled>Stop</button>
                        </div>
                    </div>
                    <textarea class="form-control" rows="2" id="diagnosisField" name="diagnosis" required><?= esc($visit['diagnosis'] ?? '') ?></textarea>
                    <small class="text-muted d-block" id="voiceStatus">Voice status: idle</small>
                    <small class="text-muted">Hotkey: <code>Alt + M</code> starts voice in Diagnosis.</small>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Disease Code (auto)</label>
                    <input class="form-control" id="diseaseCodePreview" value="<?= esc($visit['diseasecode'] ?? '-') ?>" readonly>
                    <input type="hidden" id="diseaseCodeHidden" name="diseasecode" value="<?= esc($visit['diseasecode'] ?? '') ?>">
                    <div class="list-group mt-1" id="icdSuggestList" style="max-height: 220px; overflow: auto;"></div>
                    <small class="text-muted d-block mt-1" id="icdSuggestStatus">Type diagnosis to search ICD10...</small>
                </div>

                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                        <label class="form-label mb-0" for="treatmentField">Treatment</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="voiceTreatmentStartBtn">Start Voice</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="voiceTreatmentAppendBtn">Append</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="voiceTreatmentStopBtn" disabled>Stop</button>
                        </div>
                    </div>
                    <textarea class="form-control" rows="2" id="treatmentField" name="treatment"><?= esc($visit['treatment'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                        <label class="form-label mb-0" for="medicationField">Medication</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="voiceMedicationStartBtn">Start Voice</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="voiceMedicationAppendBtn">Append</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="voiceMedicationStopBtn" disabled>Stop</button>
                        </div>
                    </div>
                    <textarea class="form-control" rows="2" id="medicationField" name="medication"><?= esc($visit['medication'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Doctor Note</label>
                    <textarea class="form-control" rows="2" name="doctor_note"><?= esc($visit['doctor_note'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a class="btn btn-secondary" href="/visits/timeline/<?= $patient['id'] ?>">Back</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const startBtn = document.getElementById('voiceStartBtn');
    const appendBtn = document.getElementById('voiceAppendBtn');
    const chiefStartBtn = document.getElementById('voiceChiefStartBtn');
    const chiefAppendBtn = document.getElementById('voiceChiefAppendBtn');
    const chiefStopBtn = document.getElementById('voiceChiefStopBtn');
    const treatmentStartBtn = document.getElementById('voiceTreatmentStartBtn');
    const treatmentAppendBtn = document.getElementById('voiceTreatmentAppendBtn');
    const treatmentStopBtn = document.getElementById('voiceTreatmentStopBtn');
    const medicationStartBtn = document.getElementById('voiceMedicationStartBtn');
    const medicationAppendBtn = document.getElementById('voiceMedicationAppendBtn');
    const medicationStopBtn = document.getElementById('voiceMedicationStopBtn');
    const stopBtn = document.getElementById('voiceStopBtn');
    const chiefComplaintField = document.getElementById('chiefComplaintField');
    const diagnosisField = document.getElementById('diagnosisField');
    const treatmentField = document.getElementById('treatmentField');
    const medicationField = document.getElementById('medicationField');
    const statusText = document.getElementById('voiceStatus');
    const voiceLangSelect = document.getElementById('voiceLangSelect');
    const diseaseCodePreview = document.getElementById('diseaseCodePreview');
    const diseaseCodeHidden = document.getElementById('diseaseCodeHidden');
    const icdSuggestList = document.getElementById('icdSuggestList');
    const icdSuggestStatus = document.getElementById('icdSuggestStatus');

    if (!SpeechRecognition || !startBtn || !appendBtn || !chiefStartBtn || !chiefAppendBtn || !chiefStopBtn || !treatmentStartBtn || !treatmentAppendBtn || !treatmentStopBtn || !medicationStartBtn || !medicationAppendBtn || !medicationStopBtn || !stopBtn || !chiefComplaintField || !diagnosisField || !treatmentField || !medicationField || !statusText || !voiceLangSelect || !diseaseCodePreview || !diseaseCodeHidden || !icdSuggestList || !icdSuggestStatus) {
        if (statusText) {
            statusText.textContent = 'Voice status: not supported in this browser.';
        }
        if (startBtn) startBtn.disabled = true;
        if (appendBtn) appendBtn.disabled = true;
        if (chiefStartBtn) chiefStartBtn.disabled = true;
        if (chiefAppendBtn) chiefAppendBtn.disabled = true;
        if (chiefStopBtn) chiefStopBtn.disabled = true;
        if (treatmentStartBtn) treatmentStartBtn.disabled = true;
        if (treatmentAppendBtn) treatmentAppendBtn.disabled = true;
        if (treatmentStopBtn) treatmentStopBtn.disabled = true;
        if (medicationStartBtn) medicationStartBtn.disabled = true;
        if (medicationAppendBtn) medicationAppendBtn.disabled = true;
        if (medicationStopBtn) medicationStopBtn.disabled = true;
        if (stopBtn) stopBtn.disabled = true;
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = voiceLangSelect.value || 'th-TH';
    recognition.interimResults = true;
    recognition.continuous = true;

    let isListening = false;
    let finalText = '';
    let activeField = diagnosisField;
    let activeName = 'diagnosis';
    let suggestTimer = null;
    const initialDiagnosis = diagnosisField.value.trim();
    const initialDiseaseCode = diseaseCodeHidden.value.trim();

    function setStatus(text) {
        statusText.textContent = 'Voice status: ' + text;
    }

    function clearSuggestList() {
        icdSuggestList.innerHTML = '';
    }

    function setIcdStatus(text) {
        icdSuggestStatus.textContent = text;
    }

    function setDiseaseCode(code) {
        const value = (code || '').trim();
        diseaseCodeHidden.value = value;
        diseaseCodePreview.value = value !== '' ? value : '-';
    }

    function renderSuggestList(items) {
        clearSuggestList();
        if (!items || items.length === 0) {
            return;
        }

        items.forEach(function (item, index) {
            const code = (item.diseasecode || '').trim();
            const th = (item.diseasenamethai || '').trim();
            const en = (item.diseasename || '').trim();
            const title = [code, th].filter(Boolean).join(' - ');
            const sub = en !== '' ? en : '';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action';
            if (index === 0) {
                btn.classList.add('active');
            }
            const titleEl = document.createElement('div');
            titleEl.className = 'fw-semibold';
            titleEl.textContent = title;
            btn.appendChild(titleEl);
            if (sub) {
                const subEl = document.createElement('small');
                subEl.textContent = sub;
                btn.appendChild(subEl);
            }
            btn.addEventListener('click', function () {
                setDiseaseCode(code);
                Array.from(icdSuggestList.children).forEach(function (el) { el.classList.remove('active'); });
                btn.classList.add('active');
                treatmentField.focus();
            });

            icdSuggestList.appendChild(btn);
        });
    }

    function fetchIcdSuggestions() {
        const q = diagnosisField.value.trim();
        if (q.length < 2) {
            clearSuggestList();
            setIcdStatus('Type at least 2 characters...');
            if (q === initialDiagnosis && initialDiseaseCode !== '') {
                setDiseaseCode(initialDiseaseCode);
            }
            return;
        }

        setIcdStatus('Searching ICD10...');
        fetch('/api/icd10/suggest?q=' + encodeURIComponent(q) + '&limit=8', {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                const contentType = (res.headers.get('content-type') || '').toLowerCase();
                if (!res.ok || contentType.indexOf('application/json') === -1) {
                    throw new Error('non_json_response');
                }
                return res.json();
            })
            .then(function (data) {
                const items = Array.isArray(data.items) ? data.items : [];
                renderSuggestList(items);
                if (items.length === 0) {
                    setIcdStatus('No ICD10 match found.');
                    if (q === initialDiagnosis && initialDiseaseCode !== '') {
                        setDiseaseCode(initialDiseaseCode);
                    }
                    return;
                }

                setIcdStatus('Found ' + items.length + ' suggestion(s).');
                const currentCode = (diseaseCodeHidden.value || '').trim().toUpperCase();
                const currentMatch = items.find(function (x) {
                    return (x.diseasecode || '').toUpperCase() === currentCode;
                });
                setDiseaseCode(currentMatch ? currentMatch.diseasecode : items[0].diseasecode);
            })
            .catch(function () {
                clearSuggestList();
                setIcdStatus('ICD10 API error. Please refresh page and try again.');
            });
    }

    function scheduleIcdSuggest() {
        if (suggestTimer) {
            clearTimeout(suggestTimer);
        }
        suggestTimer = setTimeout(fetchIcdSuggestions, 250);
    }

    function setButtons() {
        startBtn.disabled = isListening;
        appendBtn.disabled = isListening;
        chiefStartBtn.disabled = isListening;
        chiefAppendBtn.disabled = isListening;
        chiefStopBtn.disabled = !isListening;
        treatmentStartBtn.disabled = isListening;
        treatmentAppendBtn.disabled = isListening;
        treatmentStopBtn.disabled = !isListening;
        medicationStartBtn.disabled = isListening;
        medicationAppendBtn.disabled = isListening;
        medicationStopBtn.disabled = !isListening;
        stopBtn.disabled = !isListening;
    }

    function beginListening(targetField, targetName, shouldAppend) {
        recognition.lang = voiceLangSelect.value || 'th-TH';
        activeField = targetField;
        activeName = targetName;
        finalText = shouldAppend ? activeField.value.trim() : '';

        if (!shouldAppend) {
            activeField.value = '';
        }

        try {
            recognition.start();
            isListening = true;
            setButtons();
            setStatus('listening ' + activeName + ' [' + recognition.lang + '] (' + (shouldAppend ? 'append' : 'replace') + ')');
        } catch (e) {
            setStatus('cannot start microphone');
        }
    }

    startBtn.addEventListener('click', function () {
        beginListening(diagnosisField, 'diagnosis', false);
    });

    appendBtn.addEventListener('click', function () {
        beginListening(diagnosisField, 'diagnosis', true);
    });

    chiefStartBtn.addEventListener('click', function () {
        beginListening(chiefComplaintField, 'chief complaint', false);
    });

    chiefAppendBtn.addEventListener('click', function () {
        beginListening(chiefComplaintField, 'chief complaint', true);
    });

    treatmentStartBtn.addEventListener('click', function () {
        beginListening(treatmentField, 'treatment', false);
    });

    treatmentAppendBtn.addEventListener('click', function () {
        beginListening(treatmentField, 'treatment', true);
    });

    medicationStartBtn.addEventListener('click', function () {
        beginListening(medicationField, 'medication', false);
    });

    medicationAppendBtn.addEventListener('click', function () {
        beginListening(medicationField, 'medication', true);
    });

    stopBtn.addEventListener('click', function () {
        recognition.stop();
    });
    chiefStopBtn.addEventListener('click', function () {
        recognition.stop();
    });
    treatmentStopBtn.addEventListener('click', function () {
        recognition.stop();
    });
    medicationStopBtn.addEventListener('click', function () {
        recognition.stop();
    });

    recognition.onresult = function (event) {
        let interim = '';

        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalText = (finalText + ' ' + transcript).trim();
            } else {
                interim += transcript;
            }
        }

        activeField.value = (finalText + ' ' + interim).trim();
        if (activeField === diagnosisField) {
            scheduleIcdSuggest();
        }
    };

    recognition.onend = function () {
        isListening = false;
        setButtons();
        setStatus('stopped');
    };

    recognition.onerror = function (event) {
        isListening = false;
        setButtons();
        setStatus('error: ' + event.error);
    };

    document.addEventListener('keydown', function (event) {
        if (event.altKey && (event.key === 'm' || event.key === 'M')) {
            event.preventDefault();
            if (!isListening) {
                beginListening(diagnosisField, 'diagnosis', false);
            }
        }
    });

    diagnosisField.addEventListener('input', scheduleIcdSuggest);

    if (diagnosisField.value.trim() !== '') {
        scheduleIcdSuggest();
    } else if (diseaseCodeHidden.value.trim() !== '') {
        setDiseaseCode(diseaseCodeHidden.value);
        setIcdStatus('Using saved disease code.');
    } else {
        setIcdStatus('Type diagnosis to search ICD10...');
    }

    setButtons();
})();
</script>

<?= view('layouts/footer') ?>
