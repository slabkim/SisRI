@php
    use Illuminate\Support\Str;

    $selectedFacilities = old('facilities', $kost->facilities ?? []);
    $existingPhotos = $kost?->photos ?? collect();
@endphp

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pb-0">
        <nav class="nav nav-pills nav-justified kost-wizard-nav" role="tablist">
            <button class="nav-link active" type="button" data-step="0">1. Info Utama</button>
            <button class="nav-link" type="button" data-step="1">2. Fasilitas</button>
            <button class="nav-link" type="button" data-step="2">3. Media</button>
            <button class="nav-link" type="button" data-step="3">4. Harga &amp; Ketersediaan</button>
        </nav>
    </div>
    <div class="card-body">
        <div class="kost-wizard-steps">
            <section class="kost-wizard-step active" data-step="0">
                <h5 class="fw-bold mb-3">Info Utama</h5>
                <p class="text-muted small mb-4">Lengkapi informasi dasar kost agar mudah dikenali calon penghuni.</p>
                <div class="mb-3">
                    <label class="form-label">Nama Kost <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $kost->name ?? '') }}" required>
                    <div class="form-text">Gunakan nama yang familiar atau landmark terdekat.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $kost->address ?? '') }}" required>
                    <div class="form-text">Sertakan detail alamat dan patokan jika ada.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Opsional">{{ old('description', $kost->description ?? '') }}</textarea>
                    <div class="form-text">Ceritakan keunggulan kost, peraturan, atau info tambahan lain.</div>
                </div>
            </section>

            <section class="kost-wizard-step" data-step="1">
                <h5 class="fw-bold mb-3">Fasilitas</h5>
                <p class="text-muted small mb-4">Centang fasilitas yang tersedia agar penghuni dapat memfilter kost Anda.</p>
                <div class="row">
                    @foreach($facilityOptions as $facility)
                        <div class="col-md-4 col-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="facilities[]" value="{{ $facility }}"
                                    id="facility-{{ Str::slug($facility) }}-form"
                                    @checked(in_array($facility, $selectedFacilities ?? []))>
                                <label class="form-check-label" for="facility-{{ Str::slug($facility) }}-form">{{ $facility }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-text">Tidak menemukan fasilitas tertentu? Tambahkan catatan di deskripsi.</div>
            </section>

            <section class="kost-wizard-step" data-step="2">
                <h5 class="fw-bold mb-3">Media &amp; Peta</h5>
                <p class="text-muted small mb-4">Foto dan peta membantu calon penghuni membayangkan suasana kost.</p>

                @if($existingPhotos->isNotEmpty())
                    <div class="mb-4">
                        <span class="form-label d-block mb-2">Foto Saat Ini</span>
                        <div class="row g-3">
                            @foreach($existingPhotos as $photo)
                                <div class="col-md-4 col-6">
                                    <div class="border rounded p-2 h-100">
                                        <img src="{{ $photo->url }}" class="img-fluid rounded mb-2" alt="Foto kost">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_photos[]" value="{{ $photo->id }}" id="remove-photo-{{ $photo->id }}">
                                            <label class="form-check-label small text-danger" for="remove-photo-{{ $photo->id }}">Hapus foto ini</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">Centang foto yang ingin dihapus lalu simpan perubahan.</small>
                    </div>
                @elseif(!empty($kost?->photo_path))
                    <div class="mb-4">
                        <span class="form-label d-block mb-2">Foto Lama</span>
                        <img src="{{ $kost->photo_url }}" class="img-fluid rounded" alt="Foto kost">
                        <small class="text-muted d-block mt-2">Foto tunggal lama masih tersedia.</small>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Unggah Foto Kost</label>
                    <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                    <small class="text-muted d-block mt-1">Pilih hingga 5 foto (maks. 2MB per foto). Gunakan Ctrl/Shift untuk memilih lebih dari satu.</small>
                    <div id="newPhotoPreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Embed Map</label>
                    <textarea name="map_embed" rows="3" class="form-control" placeholder="Tempel kode embed peta (opsional)">{{ old('map_embed', $kost->map_embed ?? '') }}</textarea>
                    <div class="form-text">Gunakan tautan Google Maps &lt;iframe&gt; agar mudah ditemukan. Pastikan kodenya benar.</div>
                </div>
            </section>

            <section class="kost-wizard-step" data-step="3">
                <h5 class="fw-bold mb-3">Harga &amp; Ketersediaan</h5>
                <p class="text-muted small mb-4">Pastikan informasi harga dan status kamar selalu terkini.</p>

                <div class="mb-3">
                    <label class="form-label">Harga per bulan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" name="price_per_month" class="form-control" value="{{ old('price_per_month', $kost->price_per_month ?? '') }}" data-currency-input required inputmode="numeric" placeholder="Contoh: 1.500.000">
                    </div>
                    <div class="form-text">Gunakan harga bersih per bulan. Nominal akan otomatis diformat.</div>
                </div>

                <div class="alert alert-info d-flex align-items-start gap-2">
                    <span class="fw-bold">Tips:</span>
                    <span class="small">Tambahkan detail ketersediaan kamar di deskripsi atau gunakan catatan booking untuk info tambahan.</span>
                </div>
            </section>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 kost-wizard-controls">
            <button type="button" class="btn btn-outline-secondary" id="kostWizardPrev" disabled>Sebelumnya</button>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small" id="kostWizardProgress">Langkah 1 dari 4</span>
                <button type="button" class="btn btn-primary" id="kostWizardNext">Selanjutnya</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wizardNav = document.querySelectorAll('.kost-wizard-nav .nav-link');
            const steps = Array.from(document.querySelectorAll('.kost-wizard-step'));
            const prevBtn = document.getElementById('kostWizardPrev');
            const nextBtn = document.getElementById('kostWizardNext');
            const progress = document.getElementById('kostWizardProgress');
            const photoInput = document.querySelector('input[name="photos[]"]');
            const previewContainer = document.getElementById('newPhotoPreview');
            let currentStep = 0;

            function setStep(index) {
                if (index < 0 || index >= steps.length) {
                    return;
                }

                currentStep = index;
                steps.forEach((step, idx) => {
                    step.classList.toggle('active', idx === currentStep);
                });
                wizardNav.forEach((nav, idx) => {
                    nav.classList.toggle('active', idx === currentStep);
                });

                prevBtn.disabled = currentStep === 0;
                nextBtn.textContent = currentStep === steps.length - 1 ? 'Selesai' : 'Selanjutnya';
                progress.textContent = `Langkah ${currentStep + 1} dari ${steps.length}`;
            }

            wizardNav.forEach(nav => {
                nav.addEventListener('click', () => {
                    const stepIndex = parseInt(nav.dataset.step, 10);
                    setStep(stepIndex);
                });
            });

            prevBtn.addEventListener('click', () => setStep(currentStep - 1));
            nextBtn.addEventListener('click', () => {
                if (currentStep === steps.length - 1) {
                    const submitButton = document.querySelector('button[type="submit"].kost-submit-button');
                    if (submitButton) {
                        submitButton.click();
                        return;
                    }
                    const form = document.querySelector('form');
                    if (form) form.submit();
                    return;
                }
                setStep(currentStep + 1);
            });

            if (photoInput && previewContainer) {
                photoInput.addEventListener('change', () => {
                    previewContainer.innerHTML = '';
                    const files = Array.from(photoInput.files || []);
                    if (!files.length) {
                        return;
                    }
                    files.slice(0, 5).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = file.name;
                            img.className = 'rounded border';
                            img.style.width = '90px';
                            img.style.height = '90px';
                            img.style.objectFit = 'cover';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }

            setStep(0);
        });
    </script>
@endpush
