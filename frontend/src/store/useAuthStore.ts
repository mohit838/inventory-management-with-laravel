import { create } from 'zustand';
import { AppConstants } from '../constants/AppConstants';

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    permissions?: string[];
}

interface AuthState {
    user: User | null;
    token: string | null;
    refreshToken: string | null;
    isAuthenticated: boolean;
    setAuth: (user: User, token: string, refreshToken: string) => void;
    logout: () => void;
    hasPermission: (permission: string) => boolean;
}

export const useAuthStore = create<AuthState>((set, get) => ({
    user: JSON.parse(localStorage.getItem(AppConstants.USER_KEY) || 'null'),
    token: localStorage.getItem(AppConstants.TOKEN_KEY),
    refreshToken: localStorage.getItem(AppConstants.REFRESH_TOKEN_KEY),
    isAuthenticated: !!localStorage.getItem(AppConstants.TOKEN_KEY),

    setAuth: (user, token, refreshToken) => {
        localStorage.setItem(AppConstants.USER_KEY, JSON.stringify(user));
        localStorage.setItem(AppConstants.TOKEN_KEY, token);
        localStorage.setItem(AppConstants.REFRESH_TOKEN_KEY, refreshToken);
        set({ user, token, refreshToken, isAuthenticated: true });
    },

    logout: () => {
        localStorage.removeItem(AppConstants.USER_KEY);
        localStorage.removeItem(AppConstants.TOKEN_KEY);
        localStorage.removeItem(AppConstants.REFRESH_TOKEN_KEY);
        set({ user: null, token: null, refreshToken: null, isAuthenticated: false });
    },

    hasPermission: (permission) => {
        const user = get().user;
        if (user?.role === 'super-admin') return true;
        return user?.permissions?.includes(permission) || false;
    },
}));
