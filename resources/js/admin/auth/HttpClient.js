/**
 * HTTP Client Helper
 * Tự động thêm authentication token vào mọi request
 */

class HttpClient {
    constructor() {
        this.baseURL = window.location.origin;
    }

    /**
     * Lấy token từ localStorage
     */
    getAuthToken() {
        return localStorage.getItem('auth_token');
    }

    /**
     * Lấy CSRF token
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    /**
     * Tạo headers mặc định
     */
    getHeaders(customHeaders = {}) {
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.getCsrfToken(),
            ...customHeaders
        };

        // Thêm Authorization header nếu có token
        const token = this.getAuthToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        return headers;
    }

    /**
     * Xử lý response
     */
    async handleResponse(response) {
        const data = await response.json();

        if (!response.ok) {
            // Nếu token hết hạn hoặc không hợp lệ
            if (response.status === 401) {
                this.handleUnauthorized();
            }
            throw new Error(data.message || 'Request failed');
        }

        return data;
    }

    /**
     * Xử lý khi unauthorized
     */
    handleUnauthorized() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_info');

        // Redirect to login nếu không phải trang login
        if (!window.location.pathname.includes('/login')) {
            window.location.href = '/admin/login';
        }
    }

    /**
     * GET request
     */
    async get(url, options = {}) {
        try {
            const response = await fetch(`${this.baseURL}${url}`, {
                method: 'GET',
                headers: this.getHeaders(options.headers),
                ...options
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('GET request error:', error);
            throw error;
        }
    }

    /**
     * POST request
     */
    async post(url, data = {}, options = {}) {
        try {
            const isFormData = data instanceof FormData;
            const headers = this.getHeaders(options.headers);

            // Không set Content-Type nếu là FormData (browser tự động set)
            if (!isFormData) {
                headers['Content-Type'] = 'application/json';
            }

            const response = await fetch(`${this.baseURL}${url}`, {
                method: 'POST',
                headers: headers,
                body: isFormData ? data : JSON.stringify(data),
                ...options
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('POST request error:', error);
            throw error;
        }
    }

    /**
     * PUT request
     */
    async put(url, data = {}, options = {}) {
        try {
            const response = await fetch(`${this.baseURL}${url}`, {
                method: 'PUT',
                headers: this.getHeaders({
                    'Content-Type': 'application/json',
                    ...options.headers
                }),
                body: JSON.stringify(data),
                ...options
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('PUT request error:', error);
            throw error;
        }
    }

    /**
     * PATCH request
     */
    async patch(url, data = {}, options = {}) {
        try {
            const response = await fetch(`${this.baseURL}${url}`, {
                method: 'PATCH',
                headers: this.getHeaders({
                    'Content-Type': 'application/json',
                    ...options.headers
                }),
                body: JSON.stringify(data),
                ...options
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('PATCH request error:', error);
            throw error;
        }
    }

    /**
     * DELETE request
     */
    async delete(url, options = {}) {
        try {
            const response = await fetch(`${this.baseURL}${url}`, {
                method: 'DELETE',
                headers: this.getHeaders(options.headers),
                ...options
            });

            return await this.handleResponse(response);
        } catch (error) {
            console.error('DELETE request error:', error);
            throw error;
        }
    }
}

// Tạo instance global
const httpClient = new HttpClient();

// Export để sử dụng ở các file khác
window.httpClient = httpClient;
