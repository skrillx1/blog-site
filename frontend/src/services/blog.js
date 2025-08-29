import api from './api';

export const blogService = {
    async getAll() {
        const response = await api.get('/blogs');
        return response.data;
    },

    async getById(id) {
        const response = await api.get(`/blogs/${id}`);
        return response.data;
    },

    async create(blogData) {
        const response = await api.post('/blogs', blogData);
        return response.data;
    },

    async update(id, blogData) {
        const response = await api.put(`/blogs/${id}`, blogData);
        return response.data;
    },

    async delete(id) {
        const response = await api.delete(`/blogs/${id}`);
        return response.data;
    }
};