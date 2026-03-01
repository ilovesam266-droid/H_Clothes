const UserCreate = {
    init() {
        this.triggerAvatarUpload();
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


    async handleSubmit(event) {
        event.preventDefault();
        // event.stopPropagation();

        const form = document.getElementById('createUserForm');
        const formData = new FormData(form);

        try {
            // Clear old errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            const res = await fetch('/api/admin/users/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                }
            });

            const data = await res.json();

            // Validation error (Laravel 422)
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    const input = document.querySelector(`[name="${field}"]`);
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
                throw new Error(data.message || 'Request failed');
            }

            // Success
            console.log('SUCCESS', data);
            // toast / redirect / reset form

        } catch (err) {
            console.error('ERROR', err);
        }


        return false;
    }
};

window.createUserApp = UserCreate;

document.addEventListener('DOMContentLoaded', () => UserCreate.init());
