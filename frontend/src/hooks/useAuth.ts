import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import type { AxiosError } from 'axios';
import api from '../api/axios.instance';
import { useAuthStore } from '../store/useAuthStore';
import { toast } from '../utils/toast';
import type { LoginFormValues, RegisterFormValues } from '../schemas/auth.schema';

export const useLogin = () => {
    const navigate = useNavigate();
    const setAuth = useAuthStore((state) => state.setAuth);

    return useMutation({
        mutationFn: async (data: LoginFormValues) => {
            const response = await api.post('/login', data);
            return response.data;
        },
        onSuccess: (data) => {
            setAuth(data.user, data.access_token, data.refresh_token);
            toast.success('Login successful!');
            navigate('/dashboard');
        },
        onError: (error: AxiosError<{ message?: string }>) => {
            toast.error(error.response?.data?.message || 'Login failed');
        },
    });
};

export const useRegister = () => {
    const navigate = useNavigate();

    return useMutation({
        mutationFn: async (data: RegisterFormValues) => {
            const response = await api.post('/register', data);
            return response.data;
        },
        onSuccess: () => {
            toast.success('Registration successful! Please login.');
            navigate('/login');
        },
        onError: (error: AxiosError<{ message?: string }>) => {
            toast.error(error.response?.data?.message || 'Registration failed');
        },
    });
};

export const useLogout = () => {
    const logout = useAuthStore((state) => state.logout);
    const navigate = useNavigate();

    return useMutation({
        mutationFn: async () => {
            await api.post('/logout');
        },
        onSuccess: () => {
            logout();
            toast.success('Logged out successfully');
            navigate('/login');
        },
        onSettled: () => {
            logout();
            navigate('/login');
        },
    });
};
