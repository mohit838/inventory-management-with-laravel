/* eslint-disable react-hooks/incompatible-library */
import {
    useReactTable,
    getCoreRowModel,
    flexRender,
} from '@tanstack/react-table';
import type { ColumnDef } from '@tanstack/react-table';
import { Loader2, Inbox } from 'lucide-react';
import Pagination from './Pagination';

interface DataTableProps<TData> {
    columns: ColumnDef<TData, unknown>[];
    data: TData[];
    isLoading?: boolean;
    pagination?: {
        currentPage: number;
        lastPage: number;
        onPageChange: (page: number) => void;
    };
}

const DataTable = <TData,>({
    columns,
    data,
    isLoading,
    pagination,
}: DataTableProps<TData>) => {
    const table = useReactTable({
        data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        manualPagination: true,
    });

    return (
        <div className="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                    <thead>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <tr key={headerGroup.id} className="bg-slate-50 border-b border-slate-200 transition-colors">
                                {headerGroup.headers.map((header) => (
                                    <th
                                        key={header.id}
                                        className="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap"
                                    >
                                        {header.isPlaceholder
                                            ? null
                                            : flexRender(
                                                header.column.columnDef.header,
                                                header.getContext()
                                            )}
                                    </th>
                                ))}
                            </tr>
                        ))}
                    </thead>
                    <tbody className="divide-y divide-slate-100 relative">
                        {isLoading && (
                            <tr>
                                <td colSpan={columns.length} className="px-6 py-12">
                                    <div className="flex flex-col items-center justify-center gap-3">
                                        <Loader2 className="h-8 w-8 text-blue-500 animate-spin" />
                                        <p className="text-sm text-slate-500 font-medium">Loading data...</p>
                                    </div>
                                </td>
                            </tr>
                        )}
                        {!isLoading && data.length === 0 && (
                            <tr>
                                <td colSpan={columns.length} className="px-6 py-12">
                                    <div className="flex flex-col items-center justify-center gap-3 text-slate-400">
                                        <Inbox size={48} strokeWidth={1.5} />
                                        <p className="text-sm font-medium text-slate-500">No data found</p>
                                    </div>
                                </td>
                            </tr>
                        )}
                        {!isLoading && table.getRowModel().rows.map((row) => (
                            <tr
                                key={row.id}
                                className="hover:bg-slate-50/50 transition-colors group"
                            >
                                {row.getVisibleCells().map((cell) => (
                                    <td
                                        key={cell.id}
                                        className="px-6 py-4 text-sm text-slate-600 truncate max-w-xs"
                                    >
                                        {flexRender(cell.column.columnDef.cell, cell.getContext())}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            {pagination && (
                <Pagination
                    currentPage={pagination.currentPage}
                    lastPage={pagination.lastPage}
                    onPageChange={pagination.onPageChange}
                    isLoading={isLoading}
                />
            )}
        </div>
    );
};

export default DataTable;
