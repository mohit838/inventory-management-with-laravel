import React, { useState } from 'react';
import type { ColumnDef } from '@tanstack/react-table';
import { Edit, Trash2, Plus, Search } from 'lucide-react';
import { useCategories, useDeleteCategory } from '../../hooks/useCategories';
import type { Category } from '../../hooks/useCategories';
import DataTable from '../../components/common/DataTable';
import Swal from 'sweetalert2';

const CategoryList: React.FC = () => {
    const [page, setPage] = useState(1);
    const { data, isLoading } = useCategories(page);
    const { mutate: deleteCategory } = useDeleteCategory();

    const handleDelete = (id: number) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCategory(id);
            }
        });
    };

    const columns: ColumnDef<Category>[] = [
        {
            accessorKey: 'name',
            header: 'Name',
            cell: (info) => <span className="font-semibold text-slate-900">{info.getValue() as string}</span>,
        },
        {
            accessorKey: 'description',
            header: 'Description',
            cell: (info) => (info.getValue() as string) || <span className="text-slate-400 italic">No description</span>,
        },
        {
            accessorKey: 'is_active',
            header: 'Status',
            cell: (info) => (
                <span className={`px-2 py-1 rounded-full text-xs font-bold ${info.getValue() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'
                    }`}>
                    {info.getValue() ? 'Active' : 'Inactive'}
                </span>
            ),
        },
        {
            accessorKey: 'products_count',
            header: 'Products',
            cell: (info) => <span className="font-medium">{info.getValue() as number} items</span>,
        },
        {
            id: 'actions',
            header: 'Actions',
            cell: (info) => (
                <div className="flex items-center gap-2">
                    <button
                        className="p-2 hover:bg-blue-50 text-blue-600 rounded-lg transition-colors group"
                        title="Edit"
                    >
                        <Edit size={18} />
                    </button>
                    <button
                        className="p-2 hover:bg-red-50 text-red-600 rounded-lg transition-colors group"
                        title="Delete"
                        onClick={() => handleDelete(info.row.original.id)}
                    >
                        <Trash2 size={18} />
                    </button>
                </div>
            ),
        },
    ];

    return (
        <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900">Categories</h1>
                    <p className="text-slate-500">Manage your product categories</p>
                </div>
                <button className="flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    <Plus size={20} />
                    Add Category
                </button>
            </div>

            <div className="flex flex-col sm:flex-row gap-4">
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />
                    <input
                        type="text"
                        placeholder="Search categories..."
                        className="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                    />
                </div>
            </div>

            <DataTable
                columns={columns}
                data={data?.data || []}
                isLoading={isLoading}
                pagination={{
                    currentPage: data?.current_page || 1,
                    lastPage: data?.last_page || 1,
                    onPageChange: (newPage) => setPage(newPage),
                }}
            />
        </div>
    );
};

export default CategoryList;
