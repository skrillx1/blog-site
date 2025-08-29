<template>
    <div class="login-container">
        <div class="card">
            <h2>Login</h2>
            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="form.email" type="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input v-model="form.password" type="password" required>
                </div>
                <button type="submit" :disabled="loading">
                    {{ loading ? 'Logging in...' : 'Login' }}
                </button>
                <p v-if="error" class="error">{{ error }}</p>
            </form>
            <p>Don't have an account? <router-link to="/register">Register</router-link></p>
        </div>
    </div>
</template>

<script>
import { authService } from '../services/auth';

export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false,
            error: ''
        };
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            this.error = '';

            try {
                const response = await authService.login(this.form);
                
                localStorage.setItem('auth_token', response.token);
                localStorage.setItem('user', JSON.stringify(response.user));
                
                this.$router.push('/blogs');
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed';
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

.card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.form-group {
    margin-bottom: 1rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button {
    width: 100%;
    padding: 0.75rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:disabled {
    opacity: 0.6;
}

.error {
    color: #dc3545;
    margin-top: 1rem;
}
</style>