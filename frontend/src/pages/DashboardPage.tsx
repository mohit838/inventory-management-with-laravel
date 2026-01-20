import React from 'react';
import { useQuery } from '@tanstack/react-query';
import api from '../api/axios.instance';
import {
    Users,
    Layers,
    ShoppingCart,
    TrendingUp,
    ArrowUpRight,
    ArrowDownRight
} from 'lucide-react';

const DashboardPage: React.FC = () => {
    const { data: summary, isLoading } = useQuery({
        queryKey: ['dashboard-summary'],
        queryFn: async () => {
            const response = await api.get('/dashboard/summary');
            return response.data;
        },
    });

    const stats = [
        {
            title: 'Total Categories',
            value: summary?.total_categories || 0,
            icon: Layers,
            color: 'blue',
            trend: '+12%',
            isUp: true
        },
        {
            title: 'Total Products',
            value: summary?.total_products || 0,
            icon: TrendingUp,
            color: 'emerald',
            trend: '+5%',
            isUp: true
        },
        {
            title: 'Active Orders',
            value: summary?.total_orders || 0,
            icon: ShoppingCart,
            color: 'orange',
            trend: '-2%',
            isUp: false
        },
        {
            title: 'Active Users',
            value: summary?.total_users || 0,
            icon: Users,
            color: 'purple',
            trend: '+8%',
            isUp: true
        },
    ];

    if (isLoading) {
        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-pulse">
                {[1, 2, 3, 4].map((i) => (
                    <div key={i} className="h-32 bg-white rounded-2xl border border-slate-200 shadow-sm"></div>
                ))}
            </div>
        );
    }

    return (
        <div className="space-y-8">
            <div>
                <h1 className="text-2xl font-bold text-slate-900">Dashboard Overview</h1>
                <p className="text-slate-500">Welcome to your inventory management system</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {stats.map((stat) => (
                    <div
                        key={stat.title}
                        className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow group flex flex-col justify-between"
                    >
                        <div className="flex items-center justify-between mb-4">
                            <div className={`p-3 rounded-xl bg-${stat.color}-50 text-${stat.color}-600 group-hover:scale-110 transition-transform`}>
                                <stat.icon size={24} />
                            </div>
                            <div className={`flex items-center gap-1 text-xs font-bold ${stat.isUp ? 'text-emerald-500' : 'text-red-500'} bg-${stat.isUp ? 'emerald' : 'red'}-50 px-2 py-1 rounded-full`}>
                                {stat.isUp ? <ArrowUpRight size={14} /> : <ArrowDownRight size={14} />}
                                {stat.trend}
                            </div>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-slate-500 mb-1">{stat.title}</p>
                            <h3 className="text-3xl font-bold text-slate-900">{stat.value}</h3>
                        </div>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm h-96 flex flex-col items-center justify-center text-slate-400">
                    {/* Placeholder for real charts if needed later */}
                    <TrendingUp size={48} strokeWidth={1} className="mb-4" />
                    <p className="font-medium">Sales & Activity Volume</p>
                    <p className="text-sm">Chart visualization would go here</p>
                </div>
                <div className="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm h-96 flex flex-col items-center justify-center text-slate-400">
                    <Layers size={48} strokeWidth={1} className="mb-4" />
                    <p className="font-medium">Stock Distribution</p>
                    <p className="text-sm">Category-wise product distribution chart</p>
                </div>
            </div>
        </div>
    );
};

export default DashboardPage;
