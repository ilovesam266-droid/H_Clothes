const ProductCreate = {
    async handleSubmit(event){
        event.preventDefault();
        const form = document.getElementById('productForm');
        const formData = new FormData(form);

        try {
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            // const res = await fetch
        } catch(e) {

        }
    }
}
