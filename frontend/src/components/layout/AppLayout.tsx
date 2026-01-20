import React, { useState } from 'react';
import { Link, useLocation, Outlet } from 'react-router-dom';
import {
    LayoutDashboard,
    Layers,
    Settings,
    LogOut,
    Menu,
    Bell,
    User as UserIcon
} from 'lucide-react';
import { useAuthStore } from '../../store/useAuthStore';
import { useLogout } from '../../hooks/useAuth';

const AppLayout: React.FC = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const user = useAuthStore((state) => state.user);
    const { mutate: logout } = useLogout();
    const location = useLocation();

    const menuItems = [
        { title: 'Dashboard', icon: LayoutDashboard, path: '/dashboard' },
        { title: 'Categories', icon: Layers, path: '/categories' },
        { title: 'Settings', icon: Settings, path: '/settings' },
    ];

    return (
        <div className="min-h-screen bg-slate-50 flex">
            {/* Sidebar Overlay */}
            {isSidebarOpen && (
                <div
                    className="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden"
                    onClick={() => setIsSidebarOpen(false)}
                />
            )}

            {/* Sidebar */}
            <aside className={`
        fixed inset-y-0 left-0 w-64 bg-slate-900 text-slate-300 z-50 transform transition-transform duration-300 lg:static lg:translate-x-0
        ${isSidebarOpen ? 'translate-x-0' : '-translate-x-full'}
      `}>
                <div className="p-6">
                    <h2 className="text-2xl font-bold text-white flex items-center gap-2">
                        <span className="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black">I</span>
                        InvSys
                    </h2>
                </div>

                <nav className="mt-6 px-4 space-y-1">
                    {menuItems.map((item) => (
                        <Link
                            key={item.path}
                            to={item.path}
                            className={`
                flex items-center gap-3 px-4 py-3 rounded-xl transition-all
                ${location.pathname === item.path
                                    ? 'bg-blue-600/10 text-blue-400 font-medium'
                                    : 'hover:bg-slate-800 hover:text-white'}
              `}
                            onClick={() => setIsSidebarOpen(false)}
                        >
                            <item.icon size={20} />
                            {item.title}
                        </Link>
                    ))}
                </nav>

                <div className="absolute bottom-4 left-0 w-full px-4">
                    <button
                        onClick={() => logout()}
                        className="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-red-500/10 hover:text-red-400"
                    >
                        <LogOut size={20} />
                        Logout
                    </button>
                </div>
            </aside>

            {/* Main Content */}
            <div className="flex-1 flex flex-col min-w-0">
                {/* Header */}
                <header className="h-20 bg-white border-b border-slate-200 px-4 flex items-center justify-between sticky top-0 z-30">
                    <button
                        className="p-2 hover:bg-slate-100 rounded-lg lg:hidden"
                        onClick={() => setIsSidebarOpen(true)}
                    >
                        <Menu size={24} className="text-slate-600" />
                    </button>

                    <div className="ml-auto flex items-center gap-4">
                        <button className="p-2 hover:bg-slate-100 rounded-lg relative text-slate-600 transition-colors">
                            <Bell size={20} />
                            <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div className="h-8 w-px bg-slate-200 mx-2 hidden sm:block"></div>
                        <div className="flex items-center gap-3">
                            <div className="text-right hidden sm:block">
                                <p className="text-sm font-semibold text-slate-900">{user?.name}</p>
                                <p className="text-xs text-slate-500 capitalize">{user?.role}</p>
                            </div>
                            <div className="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm">
                                <UserIcon size={20} />
                            </div>
                        </div>
                    </div>
                </header>

                {/* Page Content */}
                <main className="flex-1 p-4 md:p-8">
                    <Outlet />
                </main>
            </div>
        </div>
    );
};

export default AppLayout;
