@extends('admin.layouts.layout-page')

@vite(['resources/js/admin/users/create.js', 'resources/css/admin/users.css'])

@section('content')
    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h2><i class="bi bi-person-plus-fill"></i> Create New User</h2>
            <p>Fill in the information below to create a new user account</p>
        </div>

        <form id="createUserForm" method="POST" onsubmit="createUserApp.handleSubmit(event)">
            <!-- Avatar Upload -->
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview" onclick="createUserApp.triggerAvatarUpload()">
                    <img src="\storage\avatars\uNF1lAILgTSp4zFB3zfQvEq7v769240pbHcwVwXR.jpg" alt="">
                    <div class="overlay">
                        <div class="overlay-text">
                            <i class="bi bi-cloud-upload" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                            Click to upload
                        </div>
                    </div>
                </div>
                <input type="file" class="avatar-upload-input  @error('avatarInput') is-invalid @enderror"
                    id="avatarInput" accept="image/*" onchange="createUserApp.previewAvatar(event)">
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="avatar-actions hidden" id="avatarActions">
                    <button type="button" class="btn btn-warning btn-sm" onclick="createUserApp.changeAvatar()">
                        <i class="bi bi-arrow-repeat"></i> Change
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="createUserApp.removeAvatar()">
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
                            <input class="form-check-input" type="radio" name="sex" id="male" value="1"
                                checked>
                            <label class="form-check-label" for="male">
                                <i class="bi bi-gender-male text-primary"></i> Male
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" id="female" value="0">
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
                <div class="col-md-6">
                    <label class="form-label">
                        Password <span class="required">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password" name="password">
                        <i class="bi bi-eye password-toggle" onclick="createUserApp.togglePassword('password')"></i>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                    <div class="hint-text">Min 8 characters, include uppercase, lowercase, number & symbol</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        Confirm Password <span class="required">*</span>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="confirmPassword" name="password_confirmation">
                        <i class="bi bi-eye password-toggle"
                            onclick="createUserApp.togglePassword('confirmPassword')"></i>
                    </div>
                    <div class="invalid-feedback">Passwords do not match</div>
                    <div class="valid-feedback">Passwords match!</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">
                        Role <span class="required">*</span>
                    </label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Select Role</option>
                        <option value="0">Admin</option>
                        <option value="1" selected>User</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        Status <span class="required">*</span>
                    </label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="createUserApp.cancel()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="submit" class="btn btn-submit">
                    <i class="bi bi-check-circle"></i> Create User
                </button>
            </div>
        </form>
    @endsection
