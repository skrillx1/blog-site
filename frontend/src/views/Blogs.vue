<template>
    <div class="blogs-container">
        <header>
            <h1>Blog Management</h1>
            <div class="user-info">
                <span>Welcome, {{ user.name }} ({{ user.role }})</span>
                <button @click="handleLogout" class="logout-btn">Logout</button>
            </div>
        </header>

        <div class="blog-form" v-if="user.role === 'editor' || user.role === 'admin'">
            <h3>{{ editingBlog ? 'Edit Blog' : 'Create New Blog' }}</h3>
            <form @submit.prevent="handleSubmit">
                <input v-model="blogForm.title" placeholder="Title" required>
                <textarea v-model="blogForm.content" placeholder="Content" required></textarea>
                <button type="submit" :disabled="loading">
                    {{ loading ? 'Saving...' : editingBlog ? 'Update' : 'Create' }}
                </button>
                <button v-if="editingBlog" @click="cancelEdit" type="button">Cancel</button>
            </form>
        </div>

        <div class="blogs-list">
            <h3>Blog Posts</h3>
            <div v-for="blog in blogs" :key="blog.id" class="blog-item">
                <h4>{{ blog.title }}</h4>
                <p>{{ blog.content }}</p>
                <p class="blog-author">Author: {{ blog.user?.name || 'Unknown' }}</p>
                
                <div class="blog-actions">
                    <!-- Edit button - Only show for admin OR editor who owns the blog -->
                    <button 
                        v-if="user.role === 'admin' || (user.role === 'editor' && blog.user_id === user.id)"
                        @click="editBlog(blog)"
                        class="edit-btn">
                        Edit
                    </button>
                    
                    <!-- Delete button - Only show for admin -->
                    <button 
                        v-if="user.role === 'admin'"
                        @click="deleteBlog(blog.id)"
                        class="delete-btn">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { blogService } from '../services/blog';
import { authService } from '../services/auth';

export default {
    data() {
        return {
            blogs: [],
            blogForm: {
                title: '',
                content: ''
            },
            editingBlog: null,
            loading: false,
            user: JSON.parse(localStorage.getItem('user') || '{}')
        };
    },
    mounted() {
        this.loadBlogs();
    },
    methods: {
        async loadBlogs() {
            try {
                this.blogs = await blogService.getAll();
            } catch (error) {
                console.error('Error loading blogs:', error);
            }
        },

        async handleSubmit() {
            this.loading = true;
            try {
                if (this.editingBlog) {
                    // Check if user has permission to edit
                    if (this.user.role !== 'admin' && this.editingBlog.user_id !== this.user.id) {
                        alert('You do not have permission to edit this blog');
                        return;
                    }
                    await blogService.update(this.editingBlog.id, this.blogForm);
                } else {
                    await blogService.create(this.blogForm);
                }
                
                this.resetForm();
                await this.loadBlogs();
            } catch (error) {
                console.error('Error saving blog:', error);
                if (error.response?.status === 403) {
                    alert('You do not have permission to perform this action');
                }
            } finally {
                this.loading = false;
            }
        },

        editBlog(blog) {
            // Check if user has permission to edit
            if (this.user.role !== 'admin' && blog.user_id !== this.user.id) {
                alert('You do not have permission to edit this blog');
                return;
            }
            
            this.editingBlog = blog;
            this.blogForm = { ...blog };
        },

        cancelEdit() {
            this.editingBlog = null;
            this.resetForm();
        },

        resetForm() {
            this.blogForm = {
                title: '',
                content: ''
            };
            this.editingBlog = null;
        },

        async deleteBlog(id) {
            if (!confirm('Are you sure you want to delete this blog?')) return;

            try {
                await blogService.delete(id);
                await this.loadBlogs();
            } catch (error) {
                console.error('Error deleting blog:', error);
                if (error.response?.status === 403) {
                    alert('You do not have permission to delete this blog');
                }
            }
        },

        async handleLogout() {
            try {
                await authService.logout();
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                this.$router.push('/login');
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
    }
};
</script>

<style scoped>
.blogs-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.logout-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.blog-form {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

input, textarea {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

button {
    padding: 0.5rem 1rem;
    margin-right: 0.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button[type="submit"] {
    background: #007bff;
    color: white;
}

.blog-item {
    background: white;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
}

.blog-author {
    font-style: italic;
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.blog-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
}

.edit-btn {
    background: #28a745;
    color: white;
}

.delete-btn {
    background: #dc3545;
    color: white;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>