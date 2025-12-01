<div class="content">
    <!-- Header -->
    <div class="topbar mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h5 fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>New Franchise Application</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise') ?>">Franchise</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('franchise/applications') ?>">Applications</a></li>
                    <li class="breadcrumb-item active">New Application</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('franchise/applications') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-person me-2"></i>Applicant Information</h6>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('franchise/store') ?>" method="post">
                        <div class="row g-3">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <label class="form-label">Applicant Name <span class="text-danger">*</span></label>
                                <input type="text" name="applicant_name" class="form-control" 
                                       value="<?= old('applicant_name') ?>" 
                                       placeholder="Full name of applicant" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="contact_info" class="form-control" 
                                       value="<?= old('contact_info') ?>" 
                                       placeholder="e.g., 09171234567" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= old('email') ?>" 
                                       placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Investment Capacity (₱)</label>
                                <input type="number" step="0.01" name="investment_capacity" class="form-control" 
                                       value="<?= old('investment_capacity') ?>" 
                                       placeholder="Available capital for investment">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Current Address</label>
                                <textarea name="address" class="form-control" rows="2" 
                                          placeholder="Complete address of applicant"><?= old('address') ?></textarea>
                            </div>

                            <div class="col-12">
                                <hr class="my-2">
                                <h6 class="fw-semibold"><i class="bi bi-briefcase me-2"></i>Business Details</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Proposed Franchise Location <span class="text-danger">*</span></label>
                                <input type="text" name="proposed_location" class="form-control" 
                                       value="<?= old('proposed_location') ?>" 
                                       placeholder="e.g., SM City Davao, Poblacion Gensan" required>
                                <small class="text-muted">Where do you plan to open the franchise?</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Business Experience</label>
                                <textarea name="business_experience" class="form-control" rows="4" 
                                          placeholder="Describe your relevant business experience, previous businesses, or qualifications..."><?= old('business_experience') ?></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Note:</strong> After submitting this application, it will be reviewed by the Franchise Management team. 
                            You will be notified once a decision has been made.
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="<?= site_url('franchise/applications') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

