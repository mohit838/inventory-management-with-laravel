export const AppConstants = {
    API_URL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1',
    TOKEN_KEY: 'auth_token',
    REFRESH_TOKEN_KEY: 'refresh_token',
    USER_KEY: 'user_data',
};
