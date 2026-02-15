<?= view('layouts/header', ['title' => $mode === 'create' ? 'Visit Record' : 'Edit Visit']) ?>
<style>
.visit-shell {
    border: 0;
}

.visit-header {
    border: 1px solid #b7d0ff;
    background: #dce9ff;
    border-radius: 12px;
    padding: 1rem;
}

.visit-tools {
    border: 1px solid #b7e2c4;
    background: #d9f3e0;
    border-radius: 12px;
    padding: 1rem;
}

.visit-section {
    border: 1px solid #cfd8e6;
    border-radius: 12px;
    padding: 1rem;
    background: #f4f7fb;
}

.visit-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #22324b;
}

#icdSuggestList {
    max-height: 220px;
    overflow: auto;
}
</style>

<div class="card shadow-sm visit-shell">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h5 class="mb-0"><?= $mode === 'create' ? 'Create Visit Record' : 'Edit Visit Record' ?></h5>
            <a class="btn btn-outline-secondary btn-sm" href="/visits/timeline/<?= $patient['id'] ?>">Back</a>
        </div>

        <div class="visit-header mb-3">
            <div class="d-flex align-items-center gap-3">
                <?= view('components/patient_photo', ['photo' => $patient['photo'] ?? '', 'size' => 72, 'alt' => trim(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))]) ?>
                <div>
                    <div class="fw-semibold">Patient</div>
                    <div class="text-muted"><?= esc($patient['hn'] . ' - ' . $patient['first_name'] . ' ' . $patient['last_name']) ?></div>
                </div>
            </div>
        </div>

        <div class="visit-tools mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1" for="voiceLangSelect">Voice Language</label>
                    <select class="form-select form-select-sm" id="voiceLangSelect">
                        <option value="th-TH" selected>Thai (th-TH)</option>
                        <option value="en-US">English (en-US)</option>
                    </select>
                </div>
                <div class="col-md-9">
                    <label class="form-label mb-1 d-block">Voice Controls</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="voiceStartBtn">Start Voice</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="voiceAppendBtn">Append</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="voiceStopBtn" disabled>Stop</button>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" action="<?= $mode === 'create' ? '/visits/create/' . $patient['id'] : '/visits/update/' . $visit['id'] ?>">
            <?= csrf_field() ?>

            <div class="visit-section mb-3">
                <div class="visit-section-title">Visit Information</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Visit Date/Time</label>
                        <input type="datetime-local" class="form-control" name="visit_date" value="<?= esc(isset($visit['visit_date']) ? str_replace(' ', 'T', substr($visit['visit_date'], 0, 16)) : date('Y-m-d\\TH:i')) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label mb-1" for="chiefComplaintField">Chief Complaint</label>
                        <input class="form-control" id="chiefComplaintField" name="chief_complaint" value="<?= esc($visit['chief_complaint'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Vital Signs</label>
                        <input class="form-control" name="vital_signs" value="<?= esc($visit['vital_signs'] ?? '') ?>" placeholder="e.g. BP 120/80, BT 37.0">
                    </div>
                </div>
            </div>

            <div class="visit-section mb-3">
                <div class="visit-section-title">Diagnosis & ICD10</div>
                <div class="row g-3">
                    <div class="col-lg-8">
                        <label class="form-label mb-1" for="diagnosisField">Diagnosis</label>
                        <textarea class="form-control" rows="3" id="diagnosisField" name="diagnosis" required><?= esc($visit['diagnosis'] ?? '') ?></textarea>
                        <small class="d-none" id="voiceStatus" aria-hidden="true"></small>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Disease Code (auto)</label>
                        <input class="form-control" id="diseaseCodePreview" value="<?= esc($visit['diseasecode'] ?? '-') ?>" readonly>
                        <input type="hidden" id="diseaseCodeHidden" name="diseasecode" value="<?= esc($visit['diseasecode'] ?? '') ?>">
                        <div class="list-group mt-2 d-none" id="icdSuggestList"></div>
                        <small class="text-muted d-none mt-1" id="icdSuggestStatus">Type diagnosis to search ICD10...</small>
                    </div>
                </div>
            </div>

            <div class="visit-section">
                <div class="visit-section-title">Treatment Plan</div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label mb-1" for="treatmentField">Treatment</label>
                        <textarea class="form-control" rows="2" id="treatmentField" name="treatment"><?= esc($visit['treatment'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label mb-1" for="medicationField">Medication</label>
                        <textarea class="form-control" rows="2" id="medicationField" name="medication"><?= esc($visit['medication'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Doctor Note</label>
                        <textarea class="form-control" rows="2" name="doctor_note"><?= esc($visit['doctor_note'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary px-4">Save</button>
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

    if (!SpeechRecognition || !startBtn || !appendBtn || !stopBtn || !chiefComplaintField || !diagnosisField || !treatmentField || !medicationField || !statusText || !voiceLangSelect || !diseaseCodePreview || !diseaseCodeHidden || !icdSuggestList || !icdSuggestStatus) {
        if (statusText) {
            statusText.textContent = 'Voice status: not supported in this browser.';
        }
        if (startBtn) startBtn.disabled = true;
        if (appendBtn) appendBtn.disabled = true;
        if (stopBtn) stopBtn.disabled = true;
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = voiceLangSelect.value || 'th-TH';
    recognition.interimResults = true;
    recognition.continuous = true;

    let isListening = false;
    let finalText = '';
    let activeField = null;
    let activeName = '';
    let lastFocusedField = null;
    let pendingField = null;
    let listeningModeAppend = false;
    let suggestTimer = null;
    let selectedDiseaseLabel = '';
    const initialDiagnosis = diagnosisField.value.trim();
    const initialDiseaseCode = diseaseCodeHidden.value.trim();
    const voiceTargets = [
        { field: chiefComplaintField, name: 'chief complaint' },
        { field: diagnosisField, name: 'diagnosis' },
        { field: treatmentField, name: 'treatment' },
        { field: medicationField, name: 'medication' }
    ];

    function setStatus(text) {
        statusText.textContent = 'Voice status: ' + text;
    }

    function clearSuggestList() {
        icdSuggestList.innerHTML = '';
    }

    function setIcdStatus(text) {
        icdSuggestStatus.textContent = text;
    }

    function setIcdUiVisible(visible) {
        icdSuggestList.classList.toggle('d-none', !visible);
        icdSuggestStatus.classList.toggle('d-none', !visible);
    }

    function formatDiseasePreview(code, label) {
        const normalizedCode = (code || '').trim();
        const normalizedLabel = (label || '').trim();
        if (normalizedCode === '' && normalizedLabel === '') {
            return '-';
        }
        if (normalizedCode !== '' && normalizedLabel !== '') {
            return normalizedCode + ' - ' + normalizedLabel;
        }
        return normalizedCode || normalizedLabel;
    }

    function setDiseaseCode(code) {
        const value = (code || '').trim();
        diseaseCodeHidden.value = value;
        diseaseCodePreview.value = formatDiseasePreview(value, selectedDiseaseLabel);
    }

    function setDiseaseSelection(item) {
        const code = String(item && item.diseasecode ? item.diseasecode : '').trim();
        const th = String(item && item.diseasenamethai ? item.diseasenamethai : '').trim();
        const en = String(item && item.diseasename ? item.diseasename : '').trim();
        selectedDiseaseLabel = th !== '' ? th : en;
        setDiseaseCode(code);
    }

    function syncSuggestActive(selectedCode) {
        const normalized = (selectedCode || '').trim().toUpperCase();
        let hasMatch = false;

        Array.from(icdSuggestList.children).forEach(function (el, idx) {
            const code = String(el.getAttribute('data-code') || '').trim().toUpperCase();
            const matched = normalized !== '' && code === normalized;
            el.classList.toggle('active', matched);
            if (matched) {
                hasMatch = true;
            }
            if (!hasMatch && normalized === '' && idx === 0) {
                el.classList.add('active');
            }
        });

        if (!hasMatch && normalized !== '' && icdSuggestList.firstElementChild) {
            icdSuggestList.firstElementChild.classList.add('active');
        }
    }

    function renderSuggestList(items) {
        clearSuggestList();
        if (!items || items.length === 0) {
            return;
        }

        const selectedCode = (diseaseCodeHidden.value || '').trim().toUpperCase();
        items.forEach(function (item, index) {
            const code = (item.diseasecode || '').trim();
            const th = (item.diseasenamethai || '').trim();
            const en = (item.diseasename || '').trim();
            const title = [code, th].filter(Boolean).join(' - ');
            const sub = en !== '' ? en : '';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action';
            btn.setAttribute('data-code', code);
            if (selectedCode !== '' ? code.toUpperCase() === selectedCode : index === 0) {
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
                setDiseaseSelection(item);
                syncSuggestActive(code);
                treatmentField.focus();
                setIcdUiVisible(false);
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
                selectedDiseaseLabel = '';
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
                        selectedDiseaseLabel = '';
                        setDiseaseCode(initialDiseaseCode);
                    }
                    return;
                }

                setIcdStatus('Found ' + items.length + ' suggestion(s).');
                const currentCode = (diseaseCodeHidden.value || '').trim().toUpperCase();
                const currentMatch = items.find(function (x) {
                    return (x.diseasecode || '').toUpperCase() === currentCode;
                });
                const selectedItem = currentMatch || items[0];
                setDiseaseSelection(selectedItem);
                syncSuggestActive(selectedItem.diseasecode);
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
        stopBtn.disabled = !isListening;
    }

    function getVoiceTarget() {
        if (pendingField) {
            const pending = voiceTargets.find(function (target) {
                return target.field === pendingField;
            });
            if (pending) {
                pendingField = null;
                return pending;
            }
            pendingField = null;
        }

        const current = document.activeElement;
        const byCursor = voiceTargets.find(function (target) {
            return target.field === current;
        });
        if (byCursor) {
            return byCursor;
        }

        const byLastFocus = voiceTargets.find(function (target) {
            return target.field === lastFocusedField;
        });
        return byLastFocus || null;
    }

    function beginListening(targetField, targetName, shouldAppend) {
        recognition.lang = voiceLangSelect.value || 'th-TH';
        listeningModeAppend = shouldAppend;
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

    function switchActiveField(targetField) {
        const target = voiceTargets.find(function (item) {
            return item.field === targetField;
        });
        if (!target || target.field === activeField) {
            return;
        }

        activeField = target.field;
        activeName = target.name;
        finalText = listeningModeAppend ? activeField.value.trim() : '';
        if (!listeningModeAppend) {
            activeField.value = '';
        }
        setStatus('listening ' + activeName + ' [' + recognition.lang + '] (' + (listeningModeAppend ? 'append' : 'replace') + ')');
    }

    startBtn.addEventListener('click', function () {
        const target = getVoiceTarget();
        if (!target) {
            setStatus('select a field first');
            return;
        }
        beginListening(target.field, target.name, true);
    });

    appendBtn.addEventListener('click', function () {
        const target = getVoiceTarget();
        if (!target) {
            setStatus('select a field first');
            return;
        }
        beginListening(target.field, target.name, true);
    });

    stopBtn.addEventListener('click', function () {
        recognition.stop();
    });

    function capturePendingField() {
        const current = document.activeElement;
        const byCursor = voiceTargets.find(function (target) {
            return target.field === current;
        });
        pendingField = byCursor ? byCursor.field : lastFocusedField;
    }

    startBtn.addEventListener('mousedown', capturePendingField);
    appendBtn.addEventListener('mousedown', capturePendingField);

    voiceTargets.forEach(function (target) {
        target.field.addEventListener('focus', function () {
            lastFocusedField = target.field;
            if (isListening) {
                switchActiveField(target.field);
            }
        });
        target.field.addEventListener('click', function () {
            lastFocusedField = target.field;
            if (isListening) {
                switchActiveField(target.field);
            }
        });
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
                beginListening(diagnosisField, 'diagnosis', true);
            }
        }
    });

    diagnosisField.addEventListener('focus', function () {
        setIcdUiVisible(true);
    });

    diagnosisField.addEventListener('input', scheduleIcdSuggest);

    if (diagnosisField.value.trim() !== '') {
        scheduleIcdSuggest();
    } else if (diseaseCodeHidden.value.trim() !== '') {
        selectedDiseaseLabel = '';
        setDiseaseCode(diseaseCodeHidden.value);
        setIcdStatus('Using saved disease code.');
    } else {
        setIcdStatus('Type diagnosis to search ICD10...');
    }

    setIcdUiVisible(false);
    setButtons();
})();
</script>

<?= view('layouts/footer') ?>
