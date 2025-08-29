import api from './api';

export const authService = {
    async register(userData) {
        try {
            const response = await api.post('/register', userData);
            return response.data;
        } catch (error) {
            if (error.response?.data?.errors) {
                throw new Error(Object.values(error.response.data.errors).flat().join(', '));
            }
            throw new Error(error.response?.data?.message || 'Registration failed');
        }
    },

    async login(credentials) {
        try {
            const response = await api.post('/login', credentials);
            return response.data;
        } catch (error) {
            if (error.response?.status === 401) {
                throw new Error('Invalid email or password');
            }
            if (error.response?.data?.errors) {
                throw new Error(Object.values(error.response.data.errors).flat().join(', '));
            }
            throw new Error(error.response?.data?.message || 'Login failed');
        }
    },

    async logout() {
        const response = await api.post('/logout');
        return response.data;
    },

    async getUser() {
        const response = await api.get('/user');
        return response.data;
    },

    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    },

    getToken() {
        return localStorage.getItem('auth_token');
    },

    getUserRole() {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        return user.role;
    },

    isAdmin() {
        return this.getUserRole() === 'admin';
    },

    isEditor() {
        return this.getUserRole() === 'editor';
    }
};