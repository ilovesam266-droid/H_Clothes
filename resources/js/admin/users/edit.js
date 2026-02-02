const editUserApp = {
    state: {
        userId: null,
        user: null,
    },
    init() {
        const el = document.getElementById('user-edit-page');
        if (!el) return;

        this.userId = el.dataset.userId;
        this.cacheDom();
        this.fetchUser();
    },

    cacheDom() {
        this.form = document.getElementById('editUserForm');
        this.avatarImg = document.getElementById('currentAvatar');
        this.avatarPlaceholder = document.getElementById('avatarPlaceholder');
        this.avatarActions = document.getElementById('avatarActions');
    },

    async fetchUser() {
        const res = await fetch(`/api/admin/users/${this.userId}`);
        this.state.user = await res.json();

        this.fillForm(this.state.user.data);
    },

    fillForm(user) {
        console.log('API response user ', user);
        document.getElementById('first_name').value = user.first_name ?? '';
        document.getElementById('last_name').value = user.last_name ?? '';
        document.getElementById('username').value = user.user_name ?? '';
        document.getElementById('email').value = user.email ?? '';
        document.getElementById('birthday').value = user.birthday.date ?? '';

        document.querySelector(`input[name="sex"][value="${user.sex.value}"]`)?.click();
        document.getElementById('role').value = user.role.value;
        document.getElementById('status').value = user.status.value;

        document.getElementById('emailVerified').checked = !!user.email_verified_at;

        if (user.avatar) {
            this.avatarImg.src = "/" + user.avatar;
            this.avatarImg.classList.remove('hidden');
            this.avatarPlaceholder.classList.add('hidden');
            this.avatarActions.classList.remove('hidden');
        }

        document.getElementById('createdAt').innerText = user.created_at.date;
        document.getElementById('updatedAt').innerText = user.updated_at.date;
    },

    previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                event.target.value = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please upload an image file');
                event.target.value = '';
                return;
            }

            this.avatarFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('avatarPreview');
                preview.innerHTML = `
                            <img src="${e.target.result}" alt="Avatar Preview">
                            <div class="overlay">
                                <div class="overlay-text">
                                    <i class="bi bi-pencil" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                    Click to change
                                </div>
                            </div>
                        `;

                // Show action buttons
                document.getElementById('avatarActions').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    },

    triggerAvatarUpload() {
        document.getElementById('avatarInput').click();
    },

    changeAvatar() {
        this.triggerAvatarUpload();
    },

    removeAvatar() {
        if (confirm('Remove avatar?')) {
            this.avatarFile = null;
            document.getElementById('avatarInput').value = '';
            document.getElementById('avatarPreview').innerHTML = `
                        <i class="bi bi-person-circle placeholder"></i>
                        <div class="overlay">
                            <div class="overlay-text">
                                <i class="bi bi-cloud-upload" style="font-size: 24px; display: block; margin-bottom: 5px;"></i>
                                Click to upload
                            </div>
                        </div>
                    `;
            document.getElementById('avatarActions').classList.add('hidden');
        }
    },

    async handleSubmit(e) {
        e.preventDefault();

        this.form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        this.form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

        const formData = new FormData(this.form);

        try {
            const res = await fetch(`/api/admin/users/${this.userId}/edit`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await res.json();

            // Validation error
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = this.form.querySelector(`[name="${field}"]`);
                    if (!input) return;

                    input.classList.add('is-invalid');

                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.innerText = messages[0];

                    input.parentNode.appendChild(feedback);
                });
                return;
            }

            if (!res.ok) {
                throw new Error(data.message || 'Update failed');
            }

            console.log('SUCCESS', data);
        } catch (err) {
            console.error('ERROR', err);
        }

    }
};

window.editUserApp = editUserApp;
document.addEventListener('DOMContentLoaded', () => editUserApp.init());
