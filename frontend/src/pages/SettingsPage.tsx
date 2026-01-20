import React from 'react';
import { useSettings } from '../hooks/useSettings';
import { useAuthStore } from '../store/useAuthStore';
import { Shield, User, Key, CheckCircle2 } from 'lucide-react';

const SettingsPage: React.FC = () => {
    const { data: settings, isLoading } = useSettings();
    const user = useAuthStore((state) => state.user);

    if (isLoading) {
        return <div className="animate-pulse space-y-8">
            <div className="h-32 bg-white rounded-2xl border border-slate-200"></div>
            <div className="h-64 bg-white rounded-2xl border border-slate-200"></div>
        </div>;
    }

    return (
        <div className="max-w-4xl mx-auto space-y-8">
            <div>
                <h1 className="text-2xl font-bold text-slate-900">Settings</h1>
                <p className="text-slate-500">Manage your profile and permissions</p>
            </div>

            {/* Profile Section */}
            <section className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                    <User size={20} className="text-slate-600" />
                    <h2 className="font-semibold text-slate-900">Personal Information</h2>
                </div>
                <div className="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Full Name</label>
                        <p className="text-slate-900 font-medium">{user?.name}</p>
                    </div>
                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Email Address</label>
                        <p className="text-slate-900 font-medium">{user?.email}</p>
                    </div>
                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Role</label>
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 capitalize">
                            {user?.role}
                        </span>
                    </div>
                </div>
            </section>

            {/* Roles & Permissions Section */}
            <section className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div className="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                    <Shield size={20} className="text-slate-600" />
                    <h2 className="font-semibold text-slate-900">System Permissions</h2>
                </div>
                <div className="p-6 space-y-6">
                    <p className="text-sm text-slate-500">Below are the permissions associated with your <span className="font-bold text-slate-900 capitalize">{user?.role}</span> role.</p>

                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        {settings?.role_permissions?.map((permission: string) => (
                            <div
                                key={permission}
                                className="flex items-center gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-700"
                            >
                                <CheckCircle2 size={16} className="text-emerald-500 shrink-0" />
                                <span className="text-xs font-semibold uppercase">{permission.replace('.', ' ')}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* API Configuration (Placeholder) */}
            <section className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden opacity-50">
                <div className="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center gap-2">
                    <Key size={20} className="text-slate-600" />
                    <h2 className="font-semibold text-slate-900">API Configuration</h2>
                </div>
                <div className="p-6">
                    <p className="text-sm text-slate-500">Advanced API settings and webhooks are currently restricted.</p>
                </div>
            </section>
        </div>
    );
};

export default SettingsPage;
