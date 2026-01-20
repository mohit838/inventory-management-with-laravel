import axios, { AxiosError } from 'axios';
import { AppConstants } from '../constants/AppConstants';

const api = axios.create({
    baseURL: AppConstants.API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem(AppConstants.TOKEN_KEY);
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error: AxiosError) => Promise.reject(error)
);

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;
            const refreshToken = localStorage.getItem(AppConstants.REFRESH_TOKEN_KEY);

            if (refreshToken) {
                try {
                    const response = await axios.post(`${AppConstants.API_URL}/refresh`, {
                        refresh_token: refreshToken,
                    });
                    const { access_token } = response.data;
                    localStorage.setItem(AppConstants.TOKEN_KEY, access_token);
                    api.defaults.headers.common['Authorization'] = `Bearer ${access_token}`;
                    return api(originalRequest);
                } catch (refreshError) {
                    localStorage.removeItem(AppConstants.TOKEN_KEY);
                    localStorage.removeItem(AppConstants.REFRESH_TOKEN_KEY);
                    localStorage.removeItem(AppConstants.USER_KEY);
                    window.location.href = '/login';
                    return Promise.reject(refreshError);
                }
            }
        }
        return Promise.reject(error);
    }
);

export default api;
