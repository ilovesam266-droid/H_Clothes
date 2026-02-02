@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/users/edit.js', 'resources/css/admin/users.css'])

@section('content')
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h2><i class="bi bi-pencil-square"></i> Edit User</h2>
            <p>Update user information below</p>
        </div>

        <form id="editUserForm" method="POST"onsubmit="editUserApp.handleSubmit(event)" enctype="multipart/form-data">
            @csrf
            <div id="user-edit-page" data-user-id="{{ $id }}"></div>

            <!-- Avatar Upload -->
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview" onclick="editUserApp.triggerAvatarUpload()">
                    <img id="currentAvatar" src="" alt="User Avatar">
                    <!-- Placeholder -->
                    <i class="bi bi-person-circle placeholder" id="avatarPlaceholder"></i>
                    <div class="overlay">
                        <div class="overlay-text">
                            <i class="bi bi-cloud-upload" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            Click to upload
                        </div>
                    </div>
                </div>
                <input type="file" class="avatar-upload-input @error('avatarInput') is-invalid @enderror"
                        id="avatarInput" name="avatar" accept="image/*" onchange="editUserApp.previewAvatar(event)">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                <div class="avatar-actions" id="avatarActions">
                        <button type="button" class="btn btn-warning btn-sm" onclick="editUserApp.changeAvatar()">
                            <i class="bi bi-arrow-repeat"></i> Change
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="editUserApp.removeAvatar()">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                    <div class="hint-text">Recommended: Square image, max 2MB</div>
            </div>

                <!-- Personal Information -->
                <div class="section-title">
                    <i class="bi bi-person-vcard"></i>
                    Personal Information
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            First Name <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                            name="first_name">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback">Please enter first name</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Last Name <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                            name="last_name">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback">Please enter last name</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Username <span class="required">*</span>
                        </label>
                        <input type="text" class="form-control" id="username" name="user_name">
                        <div class="hint-text">Only letters, numbers, and underscores</div>
                        <div class="invalid-feedback">Username already exists or invalid</div>
                        <div class="valid-feedback">Username available!</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            Email <span class="required">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback">Email already exists or invalid</div>
                        <div class="valid-feedback">Email available!</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Birthday</label>
                        <input type="date" class="form-control" id="birthday" name="birthday">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sex" id="male" value="1">
                                <label class="form-check-label" for="male">
                                    <i class="bi bi-gender-male text-primary"></i> Male
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="sex" id="female"
                                    value="0">
                                <label class="form-check-label" for="female">
                                    <i class="bi bi-gender-female text-danger"></i> Female
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="section-title mt-4">
                    <i class="bi bi-shield-lock"></i>
                    Account Information
                </div>

                <div class="row mb-3">


                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                Role <span class="required">*</span>
                            </label>
                            <select class="form-select" id="role" name="role">
                                <option value="">Select Role</option>
                                <option value="0">Admin</option>
                                <option value="1">User</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                Status <span class="required">*</span>
                            </label>
                            <select class="form-select" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailVerified" name="email_verified" disabled>
                            <label class="form-check-label" for="emailVerified">
                                <i class="bi bi-patch-check text-success"></i>
                                Mark email as verified
                            </label>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="section-title mt-4">
                        <i class="bi bi-clock-history"></i>
                        Account History
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted">Created At:</label>
                                <div class="fw-bold" id="createdAt"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label class="text-muted">Last Updated:</label>
                                <div class="fw-bold" id="updatedAt"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-cancel" onclick="editUserApp.cancel()">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-check-circle"></i> Update User
                        </button>
                    </div>
        </form>
    </div>
@endsection
